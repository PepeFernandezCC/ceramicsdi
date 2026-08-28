<?php
/**
 * Ceramic Connection - Formulario publico de incidencias.
 *
 * Genera un correo con un bloque de datos legible por maquina
 * (ver especificacion "Formulario - CC.pdf", version del bloque = 1)
 * a incidencias@ceramicconnection.es. El formulario no escribe en
 * ninguna base de datos de negocio, no llama a ninguna API externa,
 * no consulta Odoo/ERP y no valida si el pedido existe: solo envia
 * un correo bien formado. El correo ES la entrega.
 *
 * Prestashop 1.7 / 8.x module.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class CcIncidencias extends Module
{
    /**
     * Version del bloque de datos del correo (ver apartado 11 del PDF).
     * Cualquier cambio de formato del bloque, de las claves o del From
     * exige acordar antes una nueva version con el responsable del
     * sistema de incidencias.
     */
    const BLOCK_VERSION = 1;

    /** Idiomas soportados por el formulario y por el bloque de datos. */
    const SUPPORTED_ISO = array('es', 'fr', 'en', 'de', 'pt', 'nl');

    /** Slug amigable del formulario, uno por idioma. */
    const SLUGS = array(
        'es' => 'formulario-de-incidencias',
        'fr' => 'formulaire-incident',
        'en' => 'incident-form',
        'de' => 'schadensformular',
        'pt' => 'formulario-de-incidencia',
        'nl' => 'incidentenformulier',
    );

    /** Mapeo opcion->valor TIPO del correo. El valor NUNCA se traduce. */
    const TIPO_VALUES = array(
        'rotura' => 'ROTURA',
        'falta' => 'FALTA MAT',
        'erroneo' => 'MAT ERRONEO',
        'perdido' => 'PERDIDO?',
        'tte' => 'TTE',
        'otro' => 'SIN CLASIFICAR',
    );

    /** Formatos de foto aceptados (extension => mimes válidos). */
    const ALLOWED_PHOTO_EXT = array(
        'jpg' => array('image/jpeg', 'image/pjpeg'),
        'jpeg' => array('image/jpeg', 'image/pjpeg'),
        'png' => array('image/png'),
        'webp' => array('image/webp'),
        'pdf' => array('application/pdf'),
        'heic' => array('image/heic', 'image/heif', 'application/octet-stream'),
        'heif' => array('image/heic', 'image/heif', 'application/octet-stream'),
    );

    const MAX_PHOTOS_BYTES = 20971520; // 20 MB, margen bajo el limite de 25 MB de Gmail

    public function __construct()
    {
        $this->name = 'ccincidencias';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Ceramic Connection';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->ccL('module_name');
        $this->description = $this->ccL('module_description');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHook('moduleRoutes')
            && $this->registerHook('displayHeader')
            && Configuration::updateValue('CCINCIDENCIAS_TO_EMAIL', 'incidencias@ceramicconnection.es')
            && Configuration::updateValue('CCINCIDENCIAS_FROM_EMAIL', 'web@ceramicconnection.es')
            && Configuration::updateValue('CCINCIDENCIAS_FROM_NAME', 'Ceramic Connection Web')
            && Configuration::updateValue('CCINCIDENCIAS_CMS_PRIVACY_ID', 0)
            && Configuration::updateValue('CCINCIDENCIAS_MAX_PER_HOUR', 5)
            && Configuration::updateValue('CCINCIDENCIAS_MIN_SECONDS', 3);
    }

    public function uninstall()
    {
        return Configuration::deleteByName('CCINCIDENCIAS_TO_EMAIL')
            && Configuration::deleteByName('CCINCIDENCIAS_FROM_EMAIL')
            && Configuration::deleteByName('CCINCIDENCIAS_FROM_NAME')
            && Configuration::deleteByName('CCINCIDENCIAS_CMS_PRIVACY_ID')
            && Configuration::deleteByName('CCINCIDENCIAS_MAX_PER_HOUR')
            && Configuration::deleteByName('CCINCIDENCIAS_MIN_SECONDS')
            && parent::uninstall();
    }

    private function installDb()
    {
        // Solo se usa para el limite de envios por IP (antibot). No es
        // el sistema de incidencias: eso vive en el correo, no aqui.
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ccincidencias_log` (
            `id_ccincidencias_log` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `ip` VARCHAR(45) NOT NULL,
            `sent` TINYINT(1) NOT NULL DEFAULT 0,
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_ccincidencias_log`),
            KEY `ip_date` (`ip`, `date_add`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        return Db::getInstance()->execute($sql);
    }

    /**
     * Registra las rutas amigables del formulario, una por idioma. Todas
     * apuntan al mismo controlador para que el enlace funcione sea cual
     * sea el idioma en el que el cliente lo abra.
     */
    public function hookModuleRoutes($params)
    {
        $routes = array();

        foreach (self::SLUGS as $iso => $slug) {
            $routes['module-' . $this->name . '-form-' . $iso] = array(
                'controller' => 'form',
                'rule' => $slug,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                    'controller' => 'form',
                ),
            );
        }

        return $routes;
    }

    /**
     * URL amigable del formulario para un idioma dado (o el idioma actual),
     * con el mismo prefijo de idioma (es/, fr/...) que usa el resto de la
     * tienda en sus enlaces cuando el multi-idioma con URLs amigables
     * esta activo.
     */
    public function getFormUrl($idLang = null)
    {
        if ($idLang === null) {
            $idLang = $this->context->language->id;
        }

        $iso = $this->getIsoById($idLang);
        $slug = isset(self::SLUGS[$iso]) ? self::SLUGS[$iso] : self::SLUGS['en'];

        $base = rtrim($this->context->link->getBaseLink(null, null, false), '/');

        return $base . '/' . $this->getLangPrefix((int) $idLang) . $slug;
    }

    /**
     * Replica la logica protegida Link::getLangLink(): "es/" si el
     * multi-idioma con URLs amigables esta activo, cadena vacia si no.
     */
    private function getLangPrefix($idLang)
    {
        if (!(int) Configuration::get('PS_REWRITING_SETTINGS')) {
            return '';
        }

        if (!Language::isMultiLanguageActivated()) {
            return '';
        }

        $isoReal = Language::getIsoById($idLang);

        return $isoReal ? ($isoReal . '/') : '';
    }

    public function getIsoById($idLang = null)
    {
        if ($idLang === null) {
            $idLang = $this->context->language->id;
        }

        $iso = Tools::strtolower((string) Language::getIsoById((int) $idLang));

        if (!in_array($iso, self::SUPPORTED_ISO, true)) {
            $iso = 'en';
        }

        return $iso;
    }

    public function getCurrentIsoCode()
    {
        return $this->getIsoById($this->context->language->id);
    }

    public function ccL($key)
    {
        $translations = $this->getCcTranslations();
        $iso = $this->getCurrentIsoCode();

        if (isset($translations[$iso][$key])) {
            return $translations[$iso][$key];
        }
        if (isset($translations['en'][$key])) {
            return $translations['en'][$key];
        }

        return $key;
    }

    /**
     * Normaliza la referencia tal y como exige el apartado 3 del PDF:
     * 1) trim, 2) mayusculas, 3) fuera espacios/guiones/puntos interiores,
     * 4) validar contra ^[A-Z]{9}$.
     *
     * @return array array($referenciaNormalizada, $esValida)
     */
    public function normalizeReference($raw)
    {
        $ref = trim((string) $raw);
        $ref = Tools::strtoupper($ref);
        $ref = str_replace(array(' ', '-', '.'), '', $ref);

        $valid = (bool) preg_match('/^[A-Z]{9}$/', $ref);

        return array($ref, $valid);
    }

    /**
     * Quita retornos de carro y saltos de linea de un valor que vaya a
     * una cabecera de correo (asunto, reply-to). Es la unica cuestion de
     * seguridad de la especificacion: sin esto, cualquiera podria
     * inyectar cabeceras desde el formulario. No es opcional.
     */
    public function stripHeaderBreaks($value)
    {
        return str_replace(array("\r", "\n"), '', (string) $value);
    }

    /**
     * Antepone un espacio a cualquier linea que empiece por el
     * delimitador del bloque, para que el cliente no pueda romper el
     * parser escribiendolo en la descripcion.
     */
    public function protectBlockDelimiters($text)
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $text);

        foreach ($lines as &$line) {
            if (strpos($line, '---DATOS-TICKET-') === 0) {
                $line = ' ' . $line;
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Recorta un valor al limite de caracteres de la tabla del apartado 2.
     * Si $withEllipsis es true (descripcion), añade [...] al final.
     */
    public function truncateField($value, $limit, $withEllipsis = false)
    {
        $value = (string) $value;

        if (Tools::strlen($value) <= $limit) {
            return $value;
        }

        if ($withEllipsis) {
            return Tools::substr($value, 0, $limit) . '[...]';
        }

        return Tools::substr($value, 0, $limit);
    }

    public function getTipoOptions()
    {
        return array(
            'rotura' => $this->ccL('tipo_rotura'),
            'falta' => $this->ccL('tipo_falta'),
            'erroneo' => $this->ccL('tipo_erroneo'),
            'perdido' => $this->ccL('tipo_perdido'),
            'tte' => $this->ccL('tipo_tte'),
            'otro' => $this->ccL('tipo_otro'),
        );
    }

    public function getPrivacyPolicyUrl()
    {
        $idCms = (int) Configuration::get('CCINCIDENCIAS_CMS_PRIVACY_ID');

        if ($idCms > 0) {
            return $this->context->link->getCMSLink($idCms);
        }

        return '#';
    }

    /**
     * Limite de envios por IP (antibot, apartado 9). No bloquea si la
     * tabla no existe todavia por cualquier motivo: nunca debe impedir
     * a un cliente legitimo enviar su incidencia.
     */
    public function isRateLimited($ip)
    {
        $max = (int) Configuration::get('CCINCIDENCIAS_MAX_PER_HOUR');
        if ($max <= 0) {
            $max = 5;
        }

        $count = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ccincidencias_log`
             WHERE ip = "' . pSQL($ip) . '" AND date_add > DATE_SUB(NOW(), INTERVAL 1 HOUR)'
        );

        return $count >= $max;
    }

    public function logAttempt($ip, $sent)
    {
        Db::getInstance()->insert('ccincidencias_log', array(
            'ip' => pSQL($ip),
            'sent' => $sent ? 1 : 0,
            'date_add' => date('Y-m-d H:i:s'),
        ));

        // Limpieza ligera de entradas viejas para no acumular basura.
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'ccincidencias_log` WHERE date_add < DATE_SUB(NOW(), INTERVAL 2 DAY)'
        );
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitCcIncidenciasConfig')) {
            Configuration::updateValue('CCINCIDENCIAS_TO_EMAIL', pSQL(Tools::getValue('CCINCIDENCIAS_TO_EMAIL')));
            Configuration::updateValue('CCINCIDENCIAS_FROM_EMAIL', pSQL(Tools::getValue('CCINCIDENCIAS_FROM_EMAIL')));
            Configuration::updateValue('CCINCIDENCIAS_FROM_NAME', pSQL(Tools::getValue('CCINCIDENCIAS_FROM_NAME')));
            Configuration::updateValue('CCINCIDENCIAS_CMS_PRIVACY_ID', (int) Tools::getValue('CCINCIDENCIAS_CMS_PRIVACY_ID'));
            Configuration::updateValue('CCINCIDENCIAS_MAX_PER_HOUR', (int) Tools::getValue('CCINCIDENCIAS_MAX_PER_HOUR'));
            Configuration::updateValue('CCINCIDENCIAS_MIN_SECONDS', (int) Tools::getValue('CCINCIDENCIAS_MIN_SECONDS'));
            $output .= $this->displayConfirmation($this->ccL('config_saved'));
        }

        $output .= $this->renderUrlsPanel();

        return $output . $this->renderForm();
    }

    private function renderUrlsPanel()
    {
        $html = '<div class="panel"><h3>' . $this->ccL('form_urls') . '</h3><table class="table"><thead><tr><th>' . $this->ccL('language') . '</th><th>URL</th></tr></thead><tbody>';

        foreach (Language::getLanguages(false) as $lang) {
            $url = $this->getFormUrl((int) $lang['id_lang']);
            $html .= '<tr><td>' . htmlspecialchars($lang['name']) . '</td><td><a href="' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($url) . '</a></td></tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    private function renderForm()
    {
        $fieldsForm = array(
            'form' => array(
                'legend' => array('title' => $this->ccL('configuration')),
                'input' => array(
                    array('type' => 'text', 'label' => $this->ccL('to_email'), 'name' => 'CCINCIDENCIAS_TO_EMAIL', 'desc' => $this->ccL('to_email_help')),
                    array('type' => 'text', 'label' => $this->ccL('from_email'), 'name' => 'CCINCIDENCIAS_FROM_EMAIL', 'desc' => $this->ccL('from_email_help')),
                    array('type' => 'text', 'label' => $this->ccL('from_name'), 'name' => 'CCINCIDENCIAS_FROM_NAME'),
                    array('type' => 'text', 'label' => $this->ccL('cms_privacy_id'), 'name' => 'CCINCIDENCIAS_CMS_PRIVACY_ID', 'desc' => $this->ccL('cms_privacy_id_help')),
                    array('type' => 'text', 'label' => $this->ccL('max_per_hour'), 'name' => 'CCINCIDENCIAS_MAX_PER_HOUR'),
                    array('type' => 'text', 'label' => $this->ccL('min_seconds'), 'name' => 'CCINCIDENCIAS_MIN_SECONDS'),
                ),
                'submit' => array('title' => $this->ccL('save')),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCcIncidenciasConfig';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        foreach (array('CCINCIDENCIAS_TO_EMAIL', 'CCINCIDENCIAS_FROM_EMAIL', 'CCINCIDENCIAS_FROM_NAME', 'CCINCIDENCIAS_CMS_PRIVACY_ID', 'CCINCIDENCIAS_MAX_PER_HOUR', 'CCINCIDENCIAS_MIN_SECONDS') as $key) {
            $helper->fields_value[$key] = Configuration::get($key);
        }

        return $helper->generateForm(array($fieldsForm));
    }

    public function getCcTranslations()
    {
        return include dirname(__FILE__) . '/translations.php';
    }

    public function hookDisplayHeader($params)
    {
        if (!$this->context->controller instanceof CcIncidenciasFormModuleFrontController) {
            return '';
        }

        $this->context->controller->registerStylesheet(
            'module-ccincidencias-front',
            'modules/' . $this->name . '/views/css/front.css',
            array('media' => 'all', 'priority' => 150)
        );

        $this->context->controller->registerJavascript(
            'module-ccincidencias-front',
            'modules/' . $this->name . '/views/js/front.js',
            array('position' => 'bottom', 'priority' => 150)
        );

        return '';
    }
}
