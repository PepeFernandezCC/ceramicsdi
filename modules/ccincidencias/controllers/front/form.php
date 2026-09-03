<?php
/**
 * Controlador del formulario publico de incidencias.
 *
 * Solo para clientes logueados ($auth = true). No escribe en ninguna
 * tabla de negocio: solo registra intentos en ccincidencias_log
 * (antibot) y envia un correo. Ver "Formulario - CC.pdf".
 */
class CcIncidenciasFormModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $ssl = true;

    /** @var CcIncidencias */
    public $module;

    // Nombres distintos de $errors/$success: ModuleFrontController ya
    // declara esas dos propiedades como public, y PHP no permite
    // reducir la visibilidad al redeclararlas en la clase hija.
    private $ccErrors = array();
    private $ccSuccess = false;
    private $photosDroppedForSize = false;

    /** @var Order|null Pedido resuelto cuando se llega desde el historial (id_order en la URL). */
    private $lockedOrder = null;

    /**
     * Antes de que ModuleFrontController::init() compruebe $auth y
     * redirija a login, le decimos a donde volver (incluye el
     * ?id_order= si venimos del boton del historial de pedidos).
     */
    public function init()
    {
        $scheme = Tools::usingSecureMode() ? 'https://' : 'http://';
        $this->authRedirection = urlencode($scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $this->resolveLockedOrder();

        if (Tools::isSubmit('submitCcIncidencia')) {
            $this->processSubmission();
        }

        $this->assignTemplateVars();
        $this->setTemplate('module:ccincidencias/views/templates/front/form.tpl');
    }

    /**
     * Si llegamos con ?id_order=X (boton del historial de pedidos), lo
     * validamos: debe existir y pertenecer al cliente logueado. Si no
     * cumple alguna de las dos, simplemente se ignora y el formulario
     * se comporta como si se hubiera accedido directamente (con el
     * campo de referencia visible).
     */
    private function resolveLockedOrder()
    {
        $idOrder = (int) Tools::getValue('id_order');
        if (!$idOrder) {
            return;
        }

        $order = new Order($idOrder);
        if (!Validate::isLoadedObject($order) || (int) $order->id_customer !== (int) $this->context->customer->id) {
            return;
        }

        $this->lockedOrder = $order;
    }

    private function assignTemplateVars()
    {
        $t = array();
        foreach (array(
            'page_title', 'intro', 'label_tipo', 'label_referencia', 'referencia_placeholder',
            'referencia_warning', 'label_seguimiento', 'label_telefono',
            'label_es_muestra', 'label_descripcion', 'descripcion_placeholder', 'label_fotos', 'fotos_help',
            'fotos_too_big_notice', 'consentimiento_prefix', 'consentimiento_link_text', 'honeypot_label',
            'btn_submit', 'required_mark', 'tipo_placeholder', 'success_title', 'success_message', 'back_home',
            'from_order_note',
        ) as $key) {
            $t[$key] = $this->module->ccL($key);
        }

        $old = Tools::getAllValues();

        $lockedReference = null;
        if ($this->lockedOrder) {
            list($lockedReference,) = $this->module->normalizeReference($this->lockedOrder->reference);
        }

        $this->context->smarty->assign(array(
            'cc_t' => $t,
            'cc_tipo_options' => $this->module->getTipoOptions(),
            'cc_privacy_url' => $this->module->getPrivacyPolicyUrl(),
            'cc_action' => $this->module->getFormUrl($this->context->language->id, $this->lockedOrder ? (int) $this->lockedOrder->id : null),
            'cc_errors' => $this->ccErrors,
            'cc_success' => $this->ccSuccess,
            'cc_photos_dropped' => $this->photosDroppedForSize,
            'cc_ts' => time(),
            'cc_old' => $old,
            'cc_max_mb' => round(CcIncidencias::MAX_PHOTOS_BYTES / 1048576),
            'cc_locked_order' => (bool) $this->lockedOrder,
            'cc_locked_id_order' => $this->lockedOrder ? (int) $this->lockedOrder->id : 0,
            'cc_locked_reference' => $lockedReference,
        ));
    }

    private function processSubmission()
    {
        // Honeypot: si el campo trampa viene relleno, se descarta en
        // silencio y se muestra la pantalla de exito como si nada.
        if (Tools::getValue('cc_web') !== '' && Tools::getValue('cc_web') !== false) {
            $this->ccSuccess = true;

            return;
        }

        $ip = Tools::getRemoteAddr();

        // Comprobacion de envio no instantaneo (apartado 9 del PDF).
        $minSeconds = (int) Configuration::get('CCINCIDENCIAS_MIN_SECONDS');
        if ($minSeconds <= 0) {
            $minSeconds = 3;
        }
        $ts = (int) Tools::getValue('cc_ts');
        if ($ts > 0 && (time() - $ts) < $minSeconds) {
            // Comportamiento tipico de bot: se descarta en silencio.
            $this->ccSuccess = true;

            return;
        }

        if ($this->module->isRateLimited($ip)) {
            $this->ccErrors[] = $this->module->ccL('error_rate_limited');

            return;
        }

        // El tipo es una fila de ccincidencias_tipo (CRUD en Admin), no
        // una clave fija en codigo: se carga ya en el idioma del cliente.
        $idTipo = (int) Tools::getValue('tipo');
        $tipoObj = new CcIncidenciasTipo($idTipo, $this->context->language->id);
        if (!Validate::isLoadedObject($tipoObj) || !$tipoObj->active) {
            $this->ccErrors[] = $this->module->ccL('error_required_tipo');
        }

        // Si venimos del historial de pedidos, la referencia la ponemos
        // nosotros (pedido ya verificado en resolveLockedOrder()); si no,
        // la escribe el cliente y hay que validarla mas abajo.
        $referenciaRaw = $this->lockedOrder ? $this->lockedOrder->reference : (string) Tools::getValue('referencia');
        if (trim($referenciaRaw) === '') {
            $this->ccErrors[] = $this->module->ccL('error_required_referencia');
        }

        // Nombre y email ya no los escribe el cliente: salen directamente
        // de la cuenta con la que ha iniciado sesion (siempre valida, no
        // hace falta comprobarla).
        $nombre = trim($this->context->customer->firstname . ' ' . $this->context->customer->lastname);
        $email = trim((string) $this->context->customer->email);

        $descripcion = trim((string) Tools::getValue('descripcion'));
        if ($descripcion === '') {
            $this->ccErrors[] = $this->module->ccL('error_required_descripcion');
        }

        if (!Tools::getValue('consentimiento')) {
            $this->ccErrors[] = $this->module->ccL('error_required_consentimiento');
        }

        $photos = $this->collectValidPhotos();
        if ($photos === false) {
            $this->ccErrors[] = $this->module->ccL('error_photos_type');
        } elseif (Validate::isLoadedObject($tipoObj) && $tipoObj->require_photos && empty($photos)) {
            $this->ccErrors[] = $this->module->ccL('error_required_fotos');
        }

        if (!empty($this->ccErrors)) {
            return;
        }

        list($referenciaNorm, $referenciaValida) = $this->module->normalizeReference($referenciaRaw);
        $referenciaNorm = $this->module->truncateField($referenciaNorm, 40);

        // Si la referencia la ha escrito el cliente (no viene del boton del
        // historial), hay que comprobar que el pedido existe y que es
        // suyo antes de dejar enviar. Si viene del historial ya esta
        // verificado en resolveLockedOrder().
        if (!$this->lockedOrder) {
            $ownershipError = $this->checkReferenceOwnership($referenciaNorm);
            if ($ownershipError) {
                $this->ccErrors[] = $ownershipError;

                return;
            }
        }

        // Pedido resuelto: el del historial si venimos de ahi, o el que
        // corresponde a la referencia (ya comprobada como del cliente
        // logueado) si la ha escrito el propio cliente.
        $order = $this->lockedOrder ?: $this->resolveOrderByReference($referenciaNorm);

        $seguimiento = $this->module->truncateField(trim((string) Tools::getValue('seguimiento')), 60);
        $nombre = $this->module->truncateField($nombre, 120);
        $email = $this->module->truncateField($email, 150);
        $telefono = $this->module->truncateField(trim((string) Tools::getValue('telefono')), 30);
        $esMuestra = (bool) Tools::getValue('es_muestra');
        $descripcion = $this->module->truncateField($descripcion, 4000, true);
        $descripcion = $this->module->protectBlockDelimiters($descripcion);

        $idioma = $this->module->getCurrentIsoCode();
        $idiomaUpper = Tools::strtoupper($idioma);
        $tipoValue = $tipoObj->code;
        $tipoLabel = $tipoObj->descripcion;
        $toEmail = $tipoObj->email !== '' && $tipoObj->email !== null
            ? $tipoObj->email
            : Configuration::get('CCINCIDENCIAS_TO_EMAIL');

        // Total de fotos: si supera el limite, se envia sin adjuntos
        // pero la incidencia se manda igual (apartado 7 del PDF).
        $totalBytes = 0;
        foreach ($photos as $photo) {
            $totalBytes += $photo['size'];
        }
        if ($totalBytes > CcIncidencias::MAX_PHOTOS_BYTES) {
            $photos = array();
            $this->photosDroppedForSize = true;
        }

        $block = $this->buildDataBlock(array(
            'tipo' => $tipoValue,
            'referencia' => $referenciaNorm,
            'referencia_valida' => $referenciaValida,
            'seguimiento' => $seguimiento,
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'idioma' => $idiomaUpper,
            'es_muestra' => $esMuestra,
            'comentario' => $descripcion,
        ));

        $humanText = $this->buildHumanText(array(
            'tipo_label' => $tipoLabel,
            'referencia' => $referenciaNorm,
            'seguimiento' => $seguimiento,
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'idioma' => $idioma,
            'es_muestra' => $esMuestra,
            'comentario' => $descripcion,
            'order_details' => $order ? $this->buildOrderDetails($order) : null,
        ));

        $body = $block . "\n\n" . $humanText;

        $subject = $this->module->stripHeaderBreaks(sprintf('[TICKET] %s - %s', $referenciaNorm, $tipoValue));
        $replyToEmail = $this->module->stripHeaderBreaks($email);
        $replyToName = $this->module->stripHeaderBreaks($nombre);

        $sent = $this->sendIncidentEmail($subject, $body, $replyToEmail, $replyToName, $toEmail, $photos);

        $this->module->logAttempt($ip, $sent);

        if (!$sent) {
            $this->ccErrors[] = $this->module->ccL('error_send_failed');

            return;
        }

        $this->ccSuccess = true;
    }

    /**
     * Comprueba que la referencia escrita a mano corresponde a un
     * pedido real y que ese pedido es del cliente logueado. Un pedido
     * puede tener varias filas con la misma referencia (envios
     * partidos), asi que se comprueba contra todas.
     *
     * @return string|null Mensaje de error, o null si esta todo bien.
     */
    private function checkReferenceOwnership($referenciaNorm)
    {
        $existsCount = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'orders` WHERE reference = "' . pSQL($referenciaNorm) . '"'
        );

        if (!$existsCount) {
            return $this->module->ccL('error_referencia_not_found');
        }

        $ownCount = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'orders`
             WHERE reference = "' . pSQL($referenciaNorm) . '" AND id_customer = ' . (int) $this->context->customer->id
        );

        if (!$ownCount) {
            return $this->module->ccL('error_referencia_wrong_customer');
        }

        return null;
    }

    /**
     * Pedido real correspondiente a una referencia ya comprobada como
     * del cliente logueado (checkReferenceOwnership la valido antes de
     * llamar aqui). Si hay envios partidos con la misma referencia, nos
     * quedamos con el mas antiguo. Devuelve null solo si algo raro pasa
     * entre medias (pedido borrado, etc.); nunca debe impedir el envio
     * de la incidencia.
     */
    private function resolveOrderByReference($referenciaNorm)
    {
        $idOrder = (int) Db::getInstance()->getValue(
            'SELECT id_order FROM `' . _DB_PREFIX_ . 'orders`
             WHERE reference = "' . pSQL($referenciaNorm) . '" AND id_customer = ' . (int) $this->context->customer->id . '
             ORDER BY id_order ASC'
        );

        if (!$idOrder) {
            return null;
        }

        $order = new Order($idOrder);

        return Validate::isLoadedObject($order) ? $order : null;
    }

    /**
     * Datos del pedido a incluir en el correo (solo en el texto legible
     * por personas, no en el bloque de datos de maquina: ese bloque
     * tiene un formato pactado que no se toca sin acordar antes una
     * nueva version, ver CcIncidencias::BLOCK_VERSION). Si algo falla al
     * calcular alguno de estos datos, simplemente se omite: nunca debe
     * impedir el envio de la incidencia.
     */
    private function buildOrderDetails(Order $order)
    {
        $details = array(
            'total_paid' => null,
            'carrier_name' => null,
            'fecha_entrega' => null,
            'lineas' => array(),
        );

        try {
            $details['total_paid'] = Tools::displayPrice((float) $order->total_paid, new Currency((int) $order->id_currency));
        } catch (Exception $e) {
        }

        if ((int) $order->id_carrier) {
            $carrier = new Carrier((int) $order->id_carrier);
            if (Validate::isLoadedObject($carrier)) {
                $details['carrier_name'] = $carrier->name;
            }
        }

        $deliveredDate = Db::getInstance()->getValue(
            'SELECT oh.date_add FROM `' . _DB_PREFIX_ . 'order_history` oh
             INNER JOIN `' . _DB_PREFIX_ . 'order_state` os ON os.id_order_state = oh.id_order_state
             WHERE oh.id_order = ' . (int) $order->id . ' AND os.delivery = 1
             ORDER BY oh.date_add DESC'
        );
        if ($deliveredDate) {
            $details['fecha_entrega'] = date('d/m/Y', strtotime($deliveredDate));
        }

        $rows = Db::getInstance()->executeS(
            'SELECT product_reference, product_name, product_quantity, unit_price_tax_incl, total_price_tax_incl
             FROM `' . _DB_PREFIX_ . 'order_detail`
             WHERE id_order = ' . (int) $order->id
        );
        if (is_array($rows)) {
            $currency = new Currency((int) $order->id_currency);
            foreach ($rows as $row) {
                $details['lineas'][] = array(
                    'referencia' => (string) $row['product_reference'],
                    'nombre' => (string) $row['product_name'],
                    'cantidad' => (int) $row['product_quantity'],
                    'precio_unidad' => Tools::displayPrice((float) $row['unit_price_tax_incl'], $currency),
                    'precio_total' => Tools::displayPrice((float) $row['total_price_tax_incl'], $currency),
                );
            }
        }

        return $details;
    }

    /**
     * Bloque de datos legible por maquina. Formato exacto (apartado 5-6
     * del PDF): una linea por clave en clave: valor, todas las claves y
     * en este orden, comentario siempre la ultima y puede ocupar varias
     * lineas, version: 1 siempre, sin indentar y sin nada delante de
     * cada linea.
     */
    private function buildDataBlock(array $d)
    {
        $lines = array();
        $lines[] = '---DATOS-TICKET-INICIO---';
        $lines[] = 'version: ' . CcIncidencias::BLOCK_VERSION;
        $lines[] = 'tipo: ' . $d['tipo'];
        $lines[] = 'referencia: ' . $d['referencia'];
        $lines[] = 'referencia_valida: ' . ($d['referencia_valida'] ? 'si' : 'no');
        $lines[] = 'seguimiento: ' . $d['seguimiento'];
        $lines[] = 'nombre: ' . $d['nombre'];
        $lines[] = 'email: ' . $d['email'];
        $lines[] = 'telefono: ' . $d['telefono'];
        $lines[] = 'idioma: ' . $d['idioma'];
        $lines[] = 'es_muestra: ' . ($d['es_muestra'] ? 'si' : 'no');
        $lines[] = 'comentario: ' . $d['comentario'];
        $lines[] = '---DATOS-TICKET-FIN---';

        return implode("\n", $lines);
    }

    /**
     * Texto legible para personas debajo del bloque. Formato libre
     * (apartado 9.4 del PDF): aqui se puede tocar sin romper nada.
     */
    private function buildHumanText(array $d)
    {
        $langLabelKey = 'email_lang_' . $d['idioma'];
        $langLabel = $this->module->ccL($langLabelKey);
        $yesNo = $d['es_muestra'] ? $this->module->ccL('email_yes') : $this->module->ccL('email_no');

        $lines = array();
        $lines[] = $this->module->ccL('email_heading');
        $lines[] = '';
        $lines[] = $this->module->ccL('email_label_tipo') . ': ' . $d['tipo_label'];
        $lines[] = $this->module->ccL('email_label_referencia') . ': ' . $d['referencia'];
        $lines[] = $this->module->ccL('email_label_seguimiento') . ': ' . $d['seguimiento'];
        $lines[] = $this->module->ccL('email_label_cliente') . ': ' . $d['nombre'] . ' (' . $d['email'] . ')';
        $lines[] = $this->module->ccL('email_label_telefono') . ': ' . $d['telefono'];
        $lines[] = $this->module->ccL('email_label_idioma') . ': ' . $langLabel;
        $lines[] = $this->module->ccL('email_label_muestras') . ': ' . $yesNo;

        if (!empty($d['order_details'])) {
            $od = $d['order_details'];
            $lines[] = '';
            if ($od['total_paid'] !== null) {
                $lines[] = $this->module->ccL('email_label_total_pedido') . ': ' . $od['total_paid'];
            }
            if ($od['carrier_name'] !== null) {
                $lines[] = $this->module->ccL('email_label_transportista') . ': ' . $od['carrier_name'];
            }
            if ($od['fecha_entrega'] !== null) {
                $lines[] = $this->module->ccL('email_label_fecha_entrega') . ': ' . $od['fecha_entrega'];
            }
            if (!empty($od['lineas'])) {
                $lines[] = $this->module->ccL('email_label_lineas_pedido') . ':';
                foreach ($od['lineas'] as $linea) {
                    $lines[] = sprintf(
                        '- [%s] %s x%d - %s/ud - %s',
                        $linea['referencia'],
                        $linea['nombre'],
                        $linea['cantidad'],
                        $linea['precio_unidad'],
                        $linea['precio_total']
                    );
                }
            }
        }

        $lines[] = '';
        $lines[] = $this->module->ccL('email_label_comentario') . ':';
        $lines[] = $d['comentario'];
        $lines[] = '';
        $lines[] = sprintf($this->module->ccL('email_sent_on'), date('d/m/Y'), date('H:i'));

        return implode("\n", $lines);
    }

    /**
     * Valida y recoge las fotos subidas. Devuelve false si alguna no
     * tiene un formato admitido; nunca bloquea por tamaño aqui (eso se
     * gestiona aparte, dejando enviar la incidencia sin fotos).
     */
    private function collectValidPhotos()
    {
        $photos = array();

        if (empty($_FILES['fotos']) || empty($_FILES['fotos']['name'])) {
            return $photos;
        }

        $files = $_FILES['fotos'];
        $count = is_array($files['name']) ? count($files['name']) : 0;

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $originalName = $files['name'][$i];
            $ext = Tools::strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (!isset(CcIncidencias::ALLOWED_PHOTO_EXT[$ext])) {
                return false;
            }

            $content = Tools::file_get_contents($files['tmp_name'][$i]);
            if ($content === false) {
                continue;
            }

            $mime = 'application/octet-stream';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detected = finfo_file($finfo, $files['tmp_name'][$i]);
                finfo_close($finfo);
                if ($detected) {
                    $mime = $detected;
                }
            }

            $photos[] = array(
                'name' => Tools::substr(basename($originalName), 0, 150),
                'mime' => in_array($ext, array('jpg', 'jpeg')) ? 'image/jpeg' : ($ext === 'png' ? 'image/png' : ($ext === 'webp' ? 'image/webp' : ($ext === 'pdf' ? 'application/pdf' : $mime))),
                'content' => $content,
                'size' => (int) $files['size'][$i],
            );
        }

        return $photos;
    }

    /**
     * Envia el correo con control total sobre asunto, cuerpo y
     * cabeceras (Mail::send de Prestashop antepone el nombre de la
     * tienda al asunto, lo que rompe el formato exacto exigido). Usa
     * la misma configuracion de transporte (SMTP/sendmail) que el resto
     * de la tienda, para salir autenticado por el dominio.
     */
    private function sendIncidentEmail($subject, $plainBody, $replyToEmail, $replyToName, $toEmail, array $photos)
    {
        if (!class_exists('Swift_Message')) {
            require_once _PS_ROOT_DIR_ . '/vendor/swiftmailer/swiftmailer/lib/swift_required.php';
        }

        $fromEmail = Configuration::get('CCINCIDENCIAS_FROM_EMAIL');
        $fromName = Configuration::get('CCINCIDENCIAS_FROM_NAME');

        if (!$fromEmail || !$toEmail || !Validate::isEmail($toEmail)) {
            return false;
        }

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                $transport = $this->buildTransport();
                if (!$transport) {
                    continue;
                }

                $mailer = new Swift_Mailer($transport);

                $message = new Swift_Message($subject);
                $message->setCharset('utf-8');
                $message->setFrom(array($fromEmail => $fromName));
                $message->setTo(array($toEmail => 'Ceramic Connection'));

                if (Validate::isEmail($replyToEmail)) {
                    $message->setReplyTo(array($replyToEmail => ($replyToName !== '' ? $replyToName : null)));
                }

                // La parte text/plain SIEMPRE existe y es la primera; el
                // bloque de datos se lee de ahi. HTML es opcional y nunca
                // reemplaza al texto plano.
                $message->setBody($plainBody, 'text/plain', 'utf-8');
                $message->addPart(nl2br(Tools::safeOutput($plainBody)), 'text/html', 'utf-8');

                foreach ($photos as $photo) {
                    $attachment = new Swift_Attachment($photo['content'], $photo['name'], $photo['mime']);
                    $message->attach($attachment);
                }

                $failedRecipients = array();
                $result = $mailer->send($message, $failedRecipients);

                if ($result > 0) {
                    return true;
                }
            } catch (Exception $e) {
                PrestaShopLogger::addLog('ccincidencias: fallo envio correo - ' . $e->getMessage(), 3);
            }
        }

        return false;
    }

    private function buildTransport()
    {
        $method = Configuration::get('PS_MAIL_METHOD');

        if ((int) $method === Mail::METHOD_DISABLE) {
            return false;
        }

        if ((int) $method === Mail::METHOD_SMTP) {
            $server = Configuration::get('PS_MAIL_SERVER');
            $port = Configuration::get('PS_MAIL_SMTP_PORT');

            if (!$server || !$port) {
                return false;
            }

            $transport = new Swift_SmtpTransport($server, $port, Configuration::get('PS_MAIL_SMTP_ENCRYPTION'));
            $transport->setUsername(Configuration::get('PS_MAIL_USER'));
            $transport->setPassword(Configuration::get('PS_MAIL_PASSWD'));

            return $transport;
        }

        return new Swift_SendmailTransport();
    }
}
