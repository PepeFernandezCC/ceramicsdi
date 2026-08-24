<?php
/**
 * Ceramic Connection - Solicitud de desistimiento
 * Prestashop 1.7 / 8.x module.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class CcDesistimiento extends Module
{
    public function __construct()
    {
        $this->name = 'ccdesistimiento';
        $this->tab = 'front_office_features';
        $this->version = '1.1.2';
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
            && $this->installAdminTab()
            && $this->registerHook('displayOrderDetail')
            && $this->installCustomHooks()
            && $this->registerHook('displayCustomerAccount')
            && $this->registerHook('displayHeader')
            && Configuration::updateValue('CC_DESISTIMIENTO_DAYS', 14)
            && Configuration::updateValue('CC_DESISTIMIENTO_EMAIL', 'support@ceramicconnection.es')
            && Configuration::updateValue('CC_DESISTIMIENTO_PHONE', '+34 964 188 917')
            && Configuration::updateValue('CC_DESISTIMIENTO_RETURN_ADDRESS', 'Avenida Real de Extremadura, 9, Onda 12200, Espana')
            && Configuration::updateValue('CC_DESISTIMIENTO_DELIVERED_STATES', '5')
            && Configuration::updateValue('CC_DESISTIMIENTO_EXCLUDED_CATEGORIES', '');
    }

    public function uninstall()
    {
        $this->uninstallAdminTab();

        return Configuration::deleteByName('CC_DESISTIMIENTO_DAYS')
            && Configuration::deleteByName('CC_DESISTIMIENTO_EMAIL')
            && Configuration::deleteByName('CC_DESISTIMIENTO_PHONE')
            && Configuration::deleteByName('CC_DESISTIMIENTO_RETURN_ADDRESS')
            && Configuration::deleteByName('CC_DESISTIMIENTO_DELIVERED_STATES')
            && Configuration::deleteByName('CC_DESISTIMIENTO_EXCLUDED_CATEGORIES')
            && parent::uninstall();
    }

    public function installAdminTab()
    {
        $className = 'AdminCcDesistimiento';

        if ((int) Tab::getIdFromClassName($className)) {
            return true;
        }

        $parentId = (int) Tab::getIdFromClassName('AdminParentCustomer');

        if (!$parentId) {
            $parentId = (int) Tab::getIdFromClassName('AdminCustomers');
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->id_parent = $parentId;
        $tab->module = $this->name;

        foreach (Language::getLanguages(false) as $language) {
            $iso = strtolower(substr((string) $language['iso_code'], 0, 2));
            $tab->name[(int) $language['id_lang']] = $this->getAdminTabNameByIso($iso);
        }

        return (bool) $tab->add();
    }

    private function uninstallAdminTab()
    {
        $idTab = (int) Tab::getIdFromClassName('AdminCcDesistimiento');

        if (!$idTab) {
            return true;
        }

        $tab = new Tab($idTab);

        if (!Validate::isLoadedObject($tab)) {
            return true;
        }

        return (bool) $tab->delete();
    }

    private function getAdminTabNameByIso($iso)
    {
        $names = array(
            'es' => 'Desistimientos',
            'fr' => 'Retractations',
            'en' => 'Withdrawals',
            'de' => 'Widerrufe',
            'pt' => 'Desistencias',
            'nl' => 'Herroepingen',
        );

        return isset($names[$iso]) ? $names[$iso] : $names['en'];
    }

    private function installDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'cc_desistimiento` (
            `id_cc_desistimiento` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_order` INT UNSIGNED NOT NULL,
            `id_customer` INT UNSIGNED NOT NULL,
            `customer_name` VARCHAR(255) NOT NULL,
            `customer_email` VARCHAR(255) NOT NULL,
            `products` TEXT NULL,
            `comment` TEXT NULL,
            `status` VARCHAR(64) NOT NULL DEFAULT "pendiente",
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_cc_desistimiento`),
            KEY `id_order` (`id_order`),
            KEY `id_customer` (`id_customer`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';
        return Db::getInstance()->execute($sql);
    }

    public function getCurrentIsoCode()
    {
        $iso = 'en';
        if ($this->context && $this->context->language && !empty($this->context->language->iso_code)) {
            $iso = strtolower((string) $this->context->language->iso_code);
        }
        $iso = substr($iso, 0, 2);
        if (!in_array($iso, array('es', 'fr', 'en', 'de', 'pt', 'nl'), true)) {
            $iso = 'en';
        }
        return $iso;
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
        if (isset($translations['es'][$key])) {
            return $translations['es'][$key];
        }
        return $key;
    }

    public function getCcTranslations()
    {
        return array(
            'es' => array(
                'module_name' => 'Solicitud de desistimiento',
                'module_description' => 'Permite al cliente solicitar el desistimiento de un pedido desde su area de cliente.',
                'config_saved' => 'Configuracion guardada.',
                'configuration' => 'Configuracion',
                'period_days' => 'Plazo en dias',
                'internal_email' => 'Email interno',
                'phone_whatsapp' => 'Telefono/WhatsApp',
                'return_address' => 'Direccion devolucion',
                'delivered_state_ids' => 'Estados entregado IDs separados por coma',
                'delivered_state_ids_help' => 'Ejemplo: 5. Si usas otro estado Entregado, indica su ID.',
                'excluded_categories' => 'Categorias excluidas IDs separados por coma',
                'excluded_categories_help' => 'Productos a medida/personalizados. Si un producto pertenece a estas categorias, no se ofrecera desistimiento.',
                'save' => 'Guardar',
                'request_withdrawal' => 'Solicitar desistimiento',
                'withdrawal_requested' => 'Desistimiento solicitado',
                'withdrawal_status_pending' => 'Pendiente',
                'withdrawal_status_accepted' => 'Aceptada',
                'withdrawal_status_rejected' => 'Rechazada',
                'withdrawal_customer_status_pending' => 'Desistimiento en proceso',
                'withdrawal_customer_status_accepted' => 'Desistimiento aceptado',
                'withdrawal_customer_status_rejected' => 'Desistimiento rechazado',
                'latest_requests' => 'Ultimas solicitudes',
                'order' => 'Pedido',
                'customer' => 'Cliente',
                'products' => 'Productos',
                'status' => 'Estado',
                'date' => 'Fecha',
                'no_requests' => 'Sin solicitudes.',
                'request_from_order' => 'Solicitar desistimiento desde un pedido',
                'right_of_withdrawal' => 'Derecho de desistimiento',
                'already_requested_order_detail' => 'Ya has solicitado el desistimiento de este pedido. Ceramic Connection revisara la solicitud.',
                'order_detail_text' => 'Si deseas desistir de este pedido dentro del plazo legal, puedes comunicarlo desde aqui.',
                'request_title' => 'Solicitar desistimiento',
                'order_label' => 'Pedido',
                'customer_label' => 'Cliente',
                'request_info' => 'Esta solicitud solo comunica tu decision de ejercer el derecho de desistimiento. Ceramic Connection revisara la solicitud y el estado del material antes de tramitar el reembolso, si procede.',
                'affected_products' => 'Productos afectados',
                'optional_comments' => 'Comentarios opcionales',
                'return_warning' => 'La devolucion del material debera hacerse a: %s. El coste y gestion del transporte de devolucion corren a cargo del cliente, salvo defecto del producto o error en el envio. Recomendamos paletizar y embalar correctamente el material.',
                'confirm_withdrawal' => 'Confirmar desistimiento',
                'back_to_orders' => 'Volver a mis pedidos',
                'success_title' => 'Solicitud registrada',
                'success_message' => 'Hemos recibido tu solicitud de desistimiento. Te hemos enviado un email de confirmacion y Ceramic Connection revisara la solicitud.',
                'not_available' => 'Este pedido no esta disponible para solicitar desistimiento.',
                'already_exists' => 'Ya existe una solicitud de desistimiento para este pedido.',
                'invalid_token' => 'Token de seguridad no valido.',
                'select_product' => 'Selecciona al menos un producto.',
                'invalid_products' => 'Los productos seleccionados no son validos.',
                'save_error' => 'No se ha podido registrar la solicitud.',
                'order_message' => "Solicitud de desistimiento recibida el %s\nProductos:\n%s\nComentario:\n%s",
                'customer_email_subject' => 'Solicitud de desistimiento recibida',
                'admin_email_subject' => 'Nueva solicitud de desistimiento',
                'actions' => 'Acciones',
                'accept' => 'Aceptar',
                'reject' => 'Rechazar',
                'accepted' => 'Aceptado',
                'rejected' => 'Rechazado',
                'withdrawal_accepted_confirmation' => 'Solicitud de desistimiento aceptada. Se ha avisado al cliente.',
                'withdrawal_rejected_confirmation' => 'Solicitud de desistimiento rechazada. Se ha avisado al cliente.',
                'withdrawal_decision_error' => 'No se ha podido actualizar la solicitud de desistimiento.',
                'withdrawal_accepted_subject' => 'El procedimiento de desistimiento ha comenzado',
                'withdrawal_rejected_subject' => 'Solicitud de desistimiento no aceptada',
                'withdrawal_order_message_accepted' => 'Solicitud de desistimiento aceptada. Se ha enviado email al cliente indicando que el procedimiento ha comenzado.',
                'withdrawal_order_message_rejected' => 'Solicitud de desistimiento rechazada. Se ha enviado email al cliente indicando que no cumple los requisitos.',
            ),
            'fr' => array(
                'module_name' => 'Demande de retractation',
                'module_description' => 'Permet au client de demander la retractation d une commande depuis son espace client.',
                'config_saved' => 'Configuration enregistree.',
                'configuration' => 'Configuration',
                'period_days' => 'Delai en jours',
                'internal_email' => 'Email interne',
                'phone_whatsapp' => 'Telephone/WhatsApp',
                'return_address' => 'Adresse de retour',
                'delivered_state_ids' => 'ID des statuts livres separes par des virgules',
                'delivered_state_ids_help' => 'Exemple : 5. Si vous utilisez un autre statut Livre, indiquez son ID.',
                'excluded_categories' => 'ID des categories exclues separes par des virgules',
                'excluded_categories_help' => 'Produits sur mesure/personnalises. Si un produit appartient a ces categories, la retractation ne sera pas proposee.',
                'save' => 'Enregistrer',
                'request_withdrawal' => 'Demander la retractation',
                'withdrawal_requested' => 'Retractation demandee',
                'withdrawal_status_pending' => 'En attente',
                'withdrawal_status_accepted' => 'Acceptee',
                'withdrawal_status_rejected' => 'Refusee',
                'withdrawal_customer_status_pending' => 'Retractation en cours',
                'withdrawal_customer_status_accepted' => 'Retractation acceptee',
                'withdrawal_customer_status_rejected' => 'Retractation refusee',
                'latest_requests' => 'Dernieres demandes',
                'order' => 'Commande',
                'customer' => 'Client',
                'products' => 'Produits',
                'status' => 'Statut',
                'date' => 'Date',
                'no_requests' => 'Aucune demande.',
                'request_from_order' => 'Demander une retractation depuis une commande',
                'right_of_withdrawal' => 'Droit de retractation',
                'already_requested_order_detail' => 'Vous avez deja demande la retractation de cette commande. Ceramic Connection examinera la demande.',
                'order_detail_text' => 'Si vous souhaitez vous retracter de cette commande dans le delai legal, vous pouvez le communiquer ici.',
                'request_title' => 'Demander la retractation',
                'order_label' => 'Commande',
                'customer_label' => 'Client',
                'request_info' => 'Cette demande communique uniquement votre decision d exercer votre droit de retractation. Ceramic Connection examinera la demande et l etat du materiel avant de traiter le remboursement, le cas echeant.',
                'affected_products' => 'Produits concernes',
                'optional_comments' => 'Commentaires facultatifs',
                'return_warning' => 'Le materiel doit etre retourne a : %s. Les frais et la gestion du transport de retour sont a la charge du client, sauf en cas de defaut du produit ou d erreur d expedition. Nous recommandons de palettiser et d emballer correctement le materiel.',
                'confirm_withdrawal' => 'Confirmer la retractation',
                'back_to_orders' => 'Retour a mes commandes',
                'success_title' => 'Demande enregistree',
                'success_message' => 'Nous avons bien recu votre demande de retractation. Un email de confirmation vous a ete envoye et Ceramic Connection examinera la demande.',
                'not_available' => 'Cette commande n est pas disponible pour une demande de retractation.',
                'already_exists' => 'Une demande de retractation existe deja pour cette commande.',
                'invalid_token' => 'Jeton de securite non valide.',
                'select_product' => 'Selectionnez au moins un produit.',
                'invalid_products' => 'Les produits selectionnes ne sont pas valides.',
                'save_error' => 'La demande n a pas pu etre enregistree.',
                'order_message' => "Demande de retractation recue le %s\nProduits :\n%s\nCommentaire :\n%s",
                'customer_email_subject' => 'Demande de retractation recue',
                'admin_email_subject' => 'Nouvelle demande de retractation',
                'actions' => 'Actions',
                'accept' => 'Accepter',
                'reject' => 'Refuser',
                'accepted' => 'Accepte',
                'rejected' => 'Refuse',
                'withdrawal_accepted_confirmation' => 'Demande de retractation acceptee. Le client a ete informe.',
                'withdrawal_rejected_confirmation' => 'Demande de retractation refusee. Le client a ete informe.',
                'withdrawal_decision_error' => 'Impossible de mettre a jour la demande de retractation.',
                'withdrawal_accepted_subject' => 'La procedure de retractation a commence',
                'withdrawal_rejected_subject' => 'Demande de retractation non acceptee',
                'withdrawal_order_message_accepted' => 'Demande de retractation acceptee. Un email a ete envoye au client indiquant que la procedure a commence.',
                'withdrawal_order_message_rejected' => 'Demande de retractation refusee. Un email a ete envoye au client indiquant que les conditions ne sont pas remplies.',
            ),
            'en' => array(
                'module_name' => 'Withdrawal request',
                'module_description' => 'Allows customers to request withdrawal from an order from their customer account.',
                'config_saved' => 'Configuration saved.',
                'configuration' => 'Configuration',
                'period_days' => 'Period in days',
                'internal_email' => 'Internal email',
                'phone_whatsapp' => 'Phone/WhatsApp',
                'return_address' => 'Return address',
                'delivered_state_ids' => 'Delivered state IDs separated by commas',
                'delivered_state_ids_help' => 'Example: 5. If you use another Delivered status, enter its ID.',
                'excluded_categories' => 'Excluded category IDs separated by commas',
                'excluded_categories_help' => 'Made-to-measure/customised products. If a product belongs to these categories, withdrawal will not be offered.',
                'save' => 'Save',
                'request_withdrawal' => 'Request withdrawal',
                'withdrawal_requested' => 'Withdrawal requested',
                'withdrawal_status_pending' => 'Pending',
                'withdrawal_status_accepted' => 'Accepted',
                'withdrawal_status_rejected' => 'Rejected',
                'withdrawal_customer_status_pending' => 'Withdrawal in progress',
                'withdrawal_customer_status_accepted' => 'Withdrawal accepted',
                'withdrawal_customer_status_rejected' => 'Withdrawal rejected',
                'latest_requests' => 'Latest requests',
                'order' => 'Order',
                'customer' => 'Customer',
                'products' => 'Products',
                'status' => 'Status',
                'date' => 'Date',
                'no_requests' => 'No requests.',
                'request_from_order' => 'Request withdrawal from an order',
                'right_of_withdrawal' => 'Right of withdrawal',
                'already_requested_order_detail' => 'You have already requested withdrawal for this order. Ceramic Connection will review the request.',
                'order_detail_text' => 'If you wish to withdraw from this order within the legal period, you can notify us here.',
                'request_title' => 'Request withdrawal',
                'order_label' => 'Order',
                'customer_label' => 'Customer',
                'request_info' => 'This request only communicates your decision to exercise the right of withdrawal. Ceramic Connection will review the request and the condition of the material before processing any refund, if applicable.',
                'affected_products' => 'Affected products',
                'optional_comments' => 'Optional comments',
                'return_warning' => 'The material must be returned to: %s. The cost and management of return transport are the customer s responsibility, except in case of product defect or shipping error. We recommend palletising and packing the material correctly.',
                'confirm_withdrawal' => 'Confirm withdrawal',
                'back_to_orders' => 'Back to my orders',
                'success_title' => 'Request registered',
                'success_message' => 'We have received your withdrawal request. We have sent you a confirmation email and Ceramic Connection will review the request.',
                'not_available' => 'This order is not available for withdrawal request.',
                'already_exists' => 'A withdrawal request already exists for this order.',
                'invalid_token' => 'Invalid security token.',
                'select_product' => 'Select at least one product.',
                'invalid_products' => 'The selected products are not valid.',
                'save_error' => 'The request could not be registered.',
                'order_message' => "Withdrawal request received on %s\nProducts:\n%s\nComment:\n%s",
                'customer_email_subject' => 'Withdrawal request received',
                'admin_email_subject' => 'New withdrawal request',
                'actions' => 'Actions',
                'accept' => 'Accept',
                'reject' => 'Reject',
                'accepted' => 'Accepted',
                'rejected' => 'Rejected',
                'withdrawal_accepted_confirmation' => 'Withdrawal request accepted. The customer has been notified.',
                'withdrawal_rejected_confirmation' => 'Withdrawal request rejected. The customer has been notified.',
                'withdrawal_decision_error' => 'The withdrawal request could not be updated.',
                'withdrawal_accepted_subject' => 'The withdrawal procedure has started',
                'withdrawal_rejected_subject' => 'Withdrawal request not accepted',
                'withdrawal_order_message_accepted' => 'Withdrawal request accepted. An email has been sent to the customer indicating that the procedure has started.',
                'withdrawal_order_message_rejected' => 'Withdrawal request rejected. An email has been sent to the customer indicating that the requirements are not met.',
            ),
            'de' => array(
                'module_name' => 'Widerrufsanfrage',
                'module_description' => 'Ermoeglicht Kunden, den Widerruf einer Bestellung ueber ihr Kundenkonto zu beantragen.',
                'config_saved' => 'Konfiguration gespeichert.',
                'configuration' => 'Konfiguration',
                'period_days' => 'Frist in Tagen',
                'internal_email' => 'Interne E-Mail',
                'phone_whatsapp' => 'Telefon/WhatsApp',
                'return_address' => 'Ruecksendeadresse',
                'delivered_state_ids' => 'IDs der gelieferten Status, durch Kommas getrennt',
                'delivered_state_ids_help' => 'Beispiel: 5. Wenn Sie einen anderen Status Geliefert verwenden, geben Sie dessen ID ein.',
                'excluded_categories' => 'IDs ausgeschlossener Kategorien, durch Kommas getrennt',
                'excluded_categories_help' => 'Massgefertigte/personalisierte Produkte. Wenn ein Produkt zu diesen Kategorien gehoert, wird kein Widerruf angeboten.',
                'save' => 'Speichern',
                'request_withdrawal' => 'Widerruf beantragen',
                'withdrawal_requested' => 'Widerruf beantragt',
                'withdrawal_status_pending' => 'Ausstehend',
                'withdrawal_status_accepted' => 'Akzeptiert',
                'withdrawal_status_rejected' => 'Abgelehnt',
                'withdrawal_customer_status_pending' => 'Widerruf in Bearbeitung',
                'withdrawal_customer_status_accepted' => 'Widerruf akzeptiert',
                'withdrawal_customer_status_rejected' => 'Widerruf abgelehnt',
                'latest_requests' => 'Neueste Anfragen',
                'order' => 'Bestellung',
                'customer' => 'Kunde',
                'products' => 'Produkte',
                'status' => 'Status',
                'date' => 'Datum',
                'no_requests' => 'Keine Anfragen.',
                'request_from_order' => 'Widerruf aus einer Bestellung beantragen',
                'right_of_withdrawal' => 'Widerrufsrecht',
                'already_requested_order_detail' => 'Sie haben fuer diese Bestellung bereits einen Widerruf beantragt. Ceramic Connection wird die Anfrage pruefen.',
                'order_detail_text' => 'Wenn Sie diese Bestellung innerhalb der gesetzlichen Frist widerrufen moechten, koennen Sie dies hier mitteilen.',
                'request_title' => 'Widerruf beantragen',
                'order_label' => 'Bestellung',
                'customer_label' => 'Kunde',
                'request_info' => 'Diese Anfrage teilt nur Ihre Entscheidung mit, Ihr Widerrufsrecht auszuueben. Ceramic Connection prueft die Anfrage und den Zustand der Ware, bevor gegebenenfalls eine Erstattung bearbeitet wird.',
                'affected_products' => 'Betroffene Produkte',
                'optional_comments' => 'Optionale Kommentare',
                'return_warning' => 'Die Ware muss an folgende Adresse zurueckgesendet werden: %s. Kosten und Organisation des Ruecktransports traegt der Kunde, ausser bei Produktfehlern oder Versandfehlern. Wir empfehlen, die Ware korrekt zu palettieren und zu verpacken.',
                'confirm_withdrawal' => 'Widerruf bestaetigen',
                'back_to_orders' => 'Zurueck zu meinen Bestellungen',
                'success_title' => 'Anfrage registriert',
                'success_message' => 'Wir haben Ihre Widerrufsanfrage erhalten. Wir haben Ihnen eine Bestaetigungs-E-Mail gesendet und Ceramic Connection wird die Anfrage pruefen.',
                'not_available' => 'Diese Bestellung ist nicht fuer eine Widerrufsanfrage verfuegbar.',
                'already_exists' => 'Fuer diese Bestellung existiert bereits eine Widerrufsanfrage.',
                'invalid_token' => 'Ungueltiges Sicherheitstoken.',
                'select_product' => 'Waehlen Sie mindestens ein Produkt aus.',
                'invalid_products' => 'Die ausgewaehlten Produkte sind nicht gueltig.',
                'save_error' => 'Die Anfrage konnte nicht registriert werden.',
                'order_message' => "Widerrufsanfrage erhalten am %s\nProdukte:\n%s\nKommentar:\n%s",
                'customer_email_subject' => 'Widerrufsanfrage erhalten',
                'admin_email_subject' => 'Neue Widerrufsanfrage',
                'actions' => 'Aktionen',
                'accept' => 'Akzeptieren',
                'reject' => 'Ablehnen',
                'accepted' => 'Akzeptiert',
                'rejected' => 'Abgelehnt',
                'withdrawal_accepted_confirmation' => 'Widerrufsanfrage akzeptiert. Der Kunde wurde benachrichtigt.',
                'withdrawal_rejected_confirmation' => 'Widerrufsanfrage abgelehnt. Der Kunde wurde benachrichtigt.',
                'withdrawal_decision_error' => 'Die Widerrufsanfrage konnte nicht aktualisiert werden.',
                'withdrawal_accepted_subject' => 'Das Widerrufsverfahren hat begonnen',
                'withdrawal_rejected_subject' => 'Widerrufsanfrage nicht akzeptiert',
                'withdrawal_order_message_accepted' => 'Widerrufsanfrage akzeptiert. Eine E-Mail wurde an den Kunden gesendet, dass das Verfahren begonnen hat.',
                'withdrawal_order_message_rejected' => 'Widerrufsanfrage abgelehnt. Eine E-Mail wurde an den Kunden gesendet, dass die Voraussetzungen nicht erfuellt sind.',
            ),
            'pt' => array(
                'module_name' => 'Pedido de desistencia',
                'module_description' => 'Permite ao cliente solicitar a desistencia de uma encomenda a partir da sua conta de cliente.',
                'config_saved' => 'Configuracao guardada.',
                'configuration' => 'Configuracao',
                'period_days' => 'Prazo em dias',
                'internal_email' => 'Email interno',
                'phone_whatsapp' => 'Telefone/WhatsApp',
                'return_address' => 'Endereco de devolucao',
                'delivered_state_ids' => 'IDs dos estados entregues separados por virgulas',
                'delivered_state_ids_help' => 'Exemplo: 5. Se usar outro estado Entregue, indique o respetivo ID.',
                'excluded_categories' => 'IDs das categorias excluidas separados por virgulas',
                'excluded_categories_help' => 'Produtos feitos a medida/personalizados. Se um produto pertencer a estas categorias, a desistencia nao sera oferecida.',
                'save' => 'Guardar',
                'request_withdrawal' => 'Solicitar desistencia',
                'withdrawal_requested' => 'Desistencia solicitada',
                'withdrawal_status_pending' => 'Pendente',
                'withdrawal_status_accepted' => 'Aceite',
                'withdrawal_status_rejected' => 'Rejeitada',
                'withdrawal_customer_status_pending' => 'Desistencia em curso',
                'withdrawal_customer_status_accepted' => 'Desistencia aceite',
                'withdrawal_customer_status_rejected' => 'Desistencia rejeitada',
                'latest_requests' => 'Ultimos pedidos',
                'order' => 'Encomenda',
                'customer' => 'Cliente',
                'products' => 'Produtos',
                'status' => 'Estado',
                'date' => 'Data',
                'no_requests' => 'Sem pedidos.',
                'request_from_order' => 'Solicitar desistencia a partir de uma encomenda',
                'right_of_withdrawal' => 'Direito de desistencia',
                'already_requested_order_detail' => 'Ja solicitou a desistencia desta encomenda. A Ceramic Connection ira analisar o pedido.',
                'order_detail_text' => 'Se pretende desistir desta encomenda dentro do prazo legal, pode comunica-lo aqui.',
                'request_title' => 'Solicitar desistencia',
                'order_label' => 'Encomenda',
                'customer_label' => 'Cliente',
                'request_info' => 'Este pedido apenas comunica a sua decisao de exercer o direito de desistencia. A Ceramic Connection ira analisar o pedido e o estado do material antes de processar o reembolso, se aplicavel.',
                'affected_products' => 'Produtos afetados',
                'optional_comments' => 'Comentarios opcionais',
                'return_warning' => 'O material deve ser devolvido para: %s. O custo e a gestao do transporte de devolucao sao da responsabilidade do cliente, exceto em caso de defeito do produto ou erro no envio. Recomendamos paletizar e embalar corretamente o material.',
                'confirm_withdrawal' => 'Confirmar desistencia',
                'back_to_orders' => 'Voltar as minhas encomendas',
                'success_title' => 'Pedido registado',
                'success_message' => 'Recebemos o seu pedido de desistencia. Enviamos-lhe um email de confirmacao e a Ceramic Connection ira analisar o pedido.',
                'not_available' => 'Esta encomenda nao esta disponivel para pedido de desistencia.',
                'already_exists' => 'Ja existe um pedido de desistencia para esta encomenda.',
                'invalid_token' => 'Token de seguranca invalido.',
                'select_product' => 'Selecione pelo menos um produto.',
                'invalid_products' => 'Os produtos selecionados nao sao validos.',
                'save_error' => 'Nao foi possivel registar o pedido.',
                'order_message' => "Pedido de desistencia recebido em %s\nProdutos:\n%s\nComentario:\n%s",
                'customer_email_subject' => 'Pedido de desistencia recebido',
                'admin_email_subject' => 'Novo pedido de desistencia',
                'actions' => 'Acoes',
                'accept' => 'Aceitar',
                'reject' => 'Rejeitar',
                'accepted' => 'Aceite',
                'rejected' => 'Rejeitado',
                'withdrawal_accepted_confirmation' => 'Pedido de desistencia aceite. O cliente foi notificado.',
                'withdrawal_rejected_confirmation' => 'Pedido de desistencia rejeitado. O cliente foi notificado.',
                'withdrawal_decision_error' => 'Nao foi possivel atualizar o pedido de desistencia.',
                'withdrawal_accepted_subject' => 'O procedimento de desistencia comecou',
                'withdrawal_rejected_subject' => 'Pedido de desistencia nao aceite',
                'withdrawal_order_message_accepted' => 'Pedido de desistencia aceite. Foi enviado um email ao cliente indicando que o procedimento comecou.',
                'withdrawal_order_message_rejected' => 'Pedido de desistencia rejeitado. Foi enviado um email ao cliente indicando que os requisitos nao estao cumpridos.',
            ),
            'nl' => array(
                'module_name' => 'Herroepingsverzoek',
                'module_description' => 'Stelt klanten in staat om een herroeping van een bestelling aan te vragen vanuit hun klantaccount.',
                'config_saved' => 'Configuratie opgeslagen.',
                'configuration' => 'Configuratie',
                'period_days' => 'Termijn in dagen',
                'internal_email' => 'Interne e-mail',
                'phone_whatsapp' => 'Telefoon/WhatsApp',
                'return_address' => 'Retouradres',
                'delivered_state_ids' => 'IDs van geleverde statussen gescheiden door komma s',
                'delivered_state_ids_help' => 'Voorbeeld: 5. Als u een andere status Geleverd gebruikt, voer dan de ID in.',
                'excluded_categories' => 'IDs van uitgesloten categorieen gescheiden door komma s',
                'excluded_categories_help' => 'Op maat gemaakte/gepersonaliseerde producten. Als een product tot deze categorieen behoort, wordt herroeping niet aangeboden.',
                'save' => 'Opslaan',
                'request_withdrawal' => 'Herroeping aanvragen',
                'withdrawal_requested' => 'Herroeping aangevraagd',
                'withdrawal_status_pending' => 'In behandeling',
                'withdrawal_status_accepted' => 'Geaccepteerd',
                'withdrawal_status_rejected' => 'Afgewezen',
                'withdrawal_customer_status_pending' => 'Herroeping in behandeling',
                'withdrawal_customer_status_accepted' => 'Herroeping geaccepteerd',
                'withdrawal_customer_status_rejected' => 'Herroeping afgewezen',
                'latest_requests' => 'Laatste verzoeken',
                'order' => 'Bestelling',
                'customer' => 'Klant',
                'products' => 'Producten',
                'status' => 'Status',
                'date' => 'Datum',
                'no_requests' => 'Geen verzoeken.',
                'request_from_order' => 'Herroeping aanvragen vanuit een bestelling',
                'right_of_withdrawal' => 'Herroepingsrecht',
                'already_requested_order_detail' => 'U hebt al herroeping aangevraagd voor deze bestelling. Ceramic Connection zal het verzoek beoordelen.',
                'order_detail_text' => 'Als u deze bestelling binnen de wettelijke termijn wilt herroepen, kunt u dit hier melden.',
                'request_title' => 'Herroeping aanvragen',
                'order_label' => 'Bestelling',
                'customer_label' => 'Klant',
                'request_info' => 'Dit verzoek meldt alleen uw beslissing om het herroepingsrecht uit te oefenen. Ceramic Connection beoordeelt het verzoek en de staat van het materiaal voordat een terugbetaling, indien van toepassing, wordt verwerkt.',
                'affected_products' => 'Betrokken producten',
                'optional_comments' => 'Optionele opmerkingen',
                'return_warning' => 'Het materiaal moet worden geretourneerd naar: %s. De kosten en organisatie van het retourtransport zijn voor rekening van de klant, behalve bij een productdefect of verzendfout. Wij raden aan het materiaal correct te palletiseren en te verpakken.',
                'confirm_withdrawal' => 'Herroeping bevestigen',
                'back_to_orders' => 'Terug naar mijn bestellingen',
                'success_title' => 'Verzoek geregistreerd',
                'success_message' => 'We hebben uw herroepingsverzoek ontvangen. We hebben u een bevestigingsmail gestuurd en Ceramic Connection zal het verzoek beoordelen.',
                'not_available' => 'Deze bestelling is niet beschikbaar voor een herroepingsverzoek.',
                'already_exists' => 'Er bestaat al een herroepingsverzoek voor deze bestelling.',
                'invalid_token' => 'Ongeldig beveiligingstoken.',
                'select_product' => 'Selecteer ten minste een product.',
                'invalid_products' => 'De geselecteerde producten zijn niet geldig.',
                'save_error' => 'Het verzoek kon niet worden geregistreerd.',
                'order_message' => "Herroepingsverzoek ontvangen op %s\nProducten:\n%s\nOpmerking:\n%s",
                'customer_email_subject' => 'Herroepingsverzoek ontvangen',
                'admin_email_subject' => 'Nieuw herroepingsverzoek',
                'actions' => 'Acties',
                'accept' => 'Accepteren',
                'reject' => 'Afwijzen',
                'accepted' => 'Geaccepteerd',
                'rejected' => 'Afgewezen',
                'withdrawal_accepted_confirmation' => 'Herroepingsverzoek geaccepteerd. De klant is geinformeerd.',
                'withdrawal_rejected_confirmation' => 'Herroepingsverzoek afgewezen. De klant is geinformeerd.',
                'withdrawal_decision_error' => 'Het herroepingsverzoek kon niet worden bijgewerkt.',
                'withdrawal_accepted_subject' => 'De herroepingsprocedure is gestart',
                'withdrawal_rejected_subject' => 'Herroepingsverzoek niet geaccepteerd',
                'withdrawal_order_message_accepted' => 'Herroepingsverzoek geaccepteerd. Er is een e-mail naar de klant gestuurd waarin staat dat de procedure is gestart.',
                'withdrawal_order_message_rejected' => 'Herroepingsverzoek afgewezen. Er is een e-mail naar de klant gestuurd waarin staat dat niet aan de voorwaarden is voldaan.',
            ),
        );
    }

    public function getContent()
    {
        $output = $this->processAdminWithdrawalDecision();

        if (Tools::isSubmit('submitCcDesistimientoConfig')) {
            Configuration::updateValue('CC_DESISTIMIENTO_DAYS', (int) Tools::getValue('CC_DESISTIMIENTO_DAYS'));
            Configuration::updateValue('CC_DESISTIMIENTO_EMAIL', pSQL(Tools::getValue('CC_DESISTIMIENTO_EMAIL')));
            Configuration::updateValue('CC_DESISTIMIENTO_PHONE', pSQL(Tools::getValue('CC_DESISTIMIENTO_PHONE')));
            Configuration::updateValue('CC_DESISTIMIENTO_RETURN_ADDRESS', pSQL(Tools::getValue('CC_DESISTIMIENTO_RETURN_ADDRESS')));
            Configuration::updateValue('CC_DESISTIMIENTO_DELIVERED_STATES', pSQL(Tools::getValue('CC_DESISTIMIENTO_DELIVERED_STATES')));
            Configuration::updateValue('CC_DESISTIMIENTO_EXCLUDED_CATEGORIES', pSQL(Tools::getValue('CC_DESISTIMIENTO_EXCLUDED_CATEGORIES')));
            $output .= $this->displayConfirmation($this->ccL('config_saved'));
        }
        return $output . $this->renderForm() . $this->renderRequestsTable();
    }

    private function processAdminWithdrawalDecision()
    {
        if (!Tools::isSubmit('cc_desistimiento_action')) {
            return '';
        }

        $action = (string) Tools::getValue('cc_desistimiento_action');
        $idWithdrawal = (int) Tools::getValue('id_cc_desistimiento');

        if (!$idWithdrawal || !in_array($action, array('accept', 'reject'), true)) {
            return $this->displayError($this->ccL('withdrawal_decision_error'));
        }

        $status = $action === 'accept' ? 'aceptado' : 'rechazado';

        if ($this->updateWithdrawalStatusAndNotify($idWithdrawal, $status)) {
            return $this->displayConfirmation($action === 'accept' ? $this->ccL('withdrawal_accepted_confirmation') : $this->ccL('withdrawal_rejected_confirmation'));
        }

        return $this->displayError($this->ccL('withdrawal_decision_error'));
    }

    private function updateWithdrawalStatusAndNotify($idWithdrawal, $status)
    {
        $row = Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'cc_desistimiento` WHERE id_cc_desistimiento = ' . (int) $idWithdrawal
        );

        if (!$row) {
            return false;
        }

        $order = new Order((int) $row['id_order']);
        if (!Validate::isLoadedObject($order)) {
            return false;
        }

        $updated = Db::getInstance()->update(
            'cc_desistimiento',
            array(
                'status' => pSQL($status),
                'date_upd' => date('Y-m-d H:i:s'),
            ),
            'id_cc_desistimiento = ' . (int) $idWithdrawal
        );

        if (!$updated) {
            return false;
        }

        $this->addWithdrawalDecisionOrderMessage($order, $status);
        $this->sendWithdrawalDecisionEmail($order, $row, $status);

        return true;
    }

    private function addWithdrawalDecisionOrderMessage(Order $order, $status)
    {
        $message = new Message();
        $message->id_order = (int) $order->id;
        $message->id_customer = (int) $order->id_customer;
        $message->private = 1;
        $message->message = $status === 'aceptado'
            ? $this->ccL('withdrawal_order_message_accepted')
            : $this->ccL('withdrawal_order_message_rejected');

        return $message->add();
    }

    private function sendWithdrawalDecisionEmail(Order $order, array $withdrawal, $status)
    {
        $customer = new Customer((int) $order->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            return false;
        }

        $idLang = (int) $order->id_lang;
        if (!$idLang) {
            $idLang = (int) $this->context->language->id;
        }

        $contactEmail = Configuration::get('CC_DESISTIMIENTO_EMAIL');
        if (!$contactEmail) {
            $contactEmail = Configuration::get('PS_SHOP_EMAIL');
        }

        $template = $status === 'aceptado' ? 'withdrawal_accepted' : 'withdrawal_rejected';
        $subject = $status === 'aceptado' ? $this->ccL('withdrawal_accepted_subject') : $this->ccL('withdrawal_rejected_subject');

        $productsTxt = isset($withdrawal['products']) ? (string) $withdrawal['products'] : '';

        $vars = array(
            '{order_reference}' => $order->reference,
            '{order_id}' => (int) $order->id,
            '{customer_name}' => trim($customer->firstname . ' ' . $customer->lastname),
            '{customer_email}' => $customer->email,
            '{products}' => nl2br(Tools::safeOutput($productsTxt)),
            '{products_txt}' => $productsTxt,
            '{contact_email}' => $contactEmail,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
        );

        return Mail::Send(
            $idLang,
            $template,
            $subject,
            $vars,
            $customer->email,
            trim($customer->firstname . ' ' . $customer->lastname),
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . $this->name . '/mails/'
        );
    }

    private function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array('title' => $this->ccL('configuration')),
                'input' => array(
                    array('type' => 'text', 'label' => $this->ccL('period_days'), 'name' => 'CC_DESISTIMIENTO_DAYS'),
                    array('type' => 'text', 'label' => $this->ccL('internal_email'), 'name' => 'CC_DESISTIMIENTO_EMAIL'),
                    array('type' => 'text', 'label' => $this->ccL('phone_whatsapp'), 'name' => 'CC_DESISTIMIENTO_PHONE'),
                    array('type' => 'text', 'label' => $this->ccL('return_address'), 'name' => 'CC_DESISTIMIENTO_RETURN_ADDRESS'),
                    array('type' => 'text', 'label' => $this->ccL('delivered_state_ids'), 'name' => 'CC_DESISTIMIENTO_DELIVERED_STATES', 'desc' => $this->ccL('delivered_state_ids_help')),
                    array('type' => 'text', 'label' => $this->ccL('excluded_categories'), 'name' => 'CC_DESISTIMIENTO_EXCLUDED_CATEGORIES', 'desc' => $this->ccL('excluded_categories_help')),
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
        $helper->submit_action = 'submitCcDesistimientoConfig';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (array('CC_DESISTIMIENTO_DAYS','CC_DESISTIMIENTO_EMAIL','CC_DESISTIMIENTO_PHONE','CC_DESISTIMIENTO_RETURN_ADDRESS','CC_DESISTIMIENTO_DELIVERED_STATES','CC_DESISTIMIENTO_EXCLUDED_CATEGORIES') as $key) {
            $helper->fields_value[$key] = Configuration::get($key);
        }
        return $helper->generateForm(array($fields_form));
    }

    private function getWithdrawalStatusLabel($status)
    {
        $status = (string) $status;

        if ($status === 'aceptado') {
            return $this->ccL('withdrawal_status_accepted');
        }

        if ($status === 'rechazado') {
            return $this->ccL('withdrawal_status_rejected');
        }

        return $this->ccL('withdrawal_status_pending');
    }

    private function getCustomerWithdrawalStatusLabel($status)
    {
        $status = (string) $status;

        if ($status === 'aceptado') {
            return $this->ccL('withdrawal_customer_status_accepted');
        }

        if ($status === 'rechazado') {
            return $this->ccL('withdrawal_customer_status_rejected');
        }

        return $this->ccL('withdrawal_customer_status_pending');
    }

    private function getWithdrawalStatusCssClass($status)
    {
        $status = (string) $status;

        if ($status === 'aceptado') {
            return 'success';
        }

        if ($status === 'rechazado') {
            return 'danger';
        }

        return 'warning';
    }

    private function renderRequestsTable()
    {
        $rows = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'cc_desistimiento` ORDER BY date_add DESC LIMIT 50');
        $html = '<div class="panel"><h3>' . $this->ccL('latest_requests') . '</h3><table class="table"><thead><tr><th>ID</th><th>' . $this->ccL('order') . '</th><th>' . $this->ccL('customer') . '</th><th>Email</th><th>' . $this->ccL('products') . '</th><th>' . $this->ccL('status') . '</th><th>' . $this->ccL('date') . '</th><th>' . $this->ccL('actions') . '</th></tr></thead><tbody>';
        if (!$rows) {
            $html .= '<tr><td colspan="8">' . $this->ccL('no_requests') . '</td></tr>';
        } else {
            foreach ($rows as $row) {
                $html .= '<tr>';
                $html .= '<td>' . (int) $row['id_cc_desistimiento'] . '</td>';
                $html .= '<td>#' . (int) $row['id_order'] . '</td>';
                $html .= '<td>' . htmlspecialchars($row['customer_name']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['customer_email']) . '</td>';
                $html .= '<td>' . nl2br(htmlspecialchars($row['products'])) . '</td>';
                $html .= '<td><span class="label label-' . $this->getWithdrawalStatusCssClass($row['status']) . '">' . htmlspecialchars($this->getWithdrawalStatusLabel($row['status'])) . '</span></td>';
                $html .= '<td>' . htmlspecialchars($row['date_add']) . '</td>';
                $html .= '<td>' . $this->renderRequestActions($row) . '</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody></table></div>';
        return $html;
    }

    private function renderRequestActions(array $row)
    {
        if (!isset($row['status']) || $row['status'] !== 'pendiente') {
            return '-';
        }

        $baseUrl = AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&id_cc_desistimiento=' . (int) $row['id_cc_desistimiento'];
        $acceptUrl = $baseUrl . '&cc_desistimiento_action=accept';
        $rejectUrl = $baseUrl . '&cc_desistimiento_action=reject';

        return '<a class="btn btn-success btn-xs" href="' . htmlspecialchars($acceptUrl) . '" onclick="return confirm(\'' . addslashes($this->ccL('accept')) . '?\');">' . $this->ccL('accept') . '</a> '
            . '<a class="btn btn-danger btn-xs" href="' . htmlspecialchars($rejectUrl) . '" onclick="return confirm(\'' . addslashes($this->ccL('reject')) . '?\');">' . $this->ccL('reject') . '</a>';
    }

    public function hookDisplayHeader($params)
    {
        if (!$this->context->controller) {
            return '';
        }

        if (!$this->context->customer || !$this->context->customer->isLogged()) {
            return '';
        }



        Media::addJsDef(array(
            'ccDesistimientoHistory' => array(
                'eligibleUrl' => $this->context->link->getModuleLink($this->name, 'eligible', array(), true),
                'buttonText' => $this->ccL('request_withdrawal'),
                'requestedText' => $this->ccL('withdrawal_requested'),
            ),
        ));

        $this->context->controller->registerStylesheet(
            'module-ccdesistimiento-front',
            'modules/' . $this->name . '/views/css/front.css',
            array(
                'media' => 'all',
                'priority' => 150,
            )
        );

        $this->context->controller->registerJavascript(
            'module-ccdesistimiento-front',
            'modules/' . $this->name . '/views/js/front.js',
            array(
                'position' => 'bottom',
                'priority' => 150,
            )
        );

        return '';
    }

    public function getEligibleOrdersForCustomer($idCustomer)
    {
        $rows = Db::getInstance()->executeS('SELECT id_order FROM `' . _DB_PREFIX_ . 'orders` WHERE id_customer=' . (int) $idCustomer . ' ORDER BY date_add DESC LIMIT 100');
        $result = array();
        if (!$rows) {
            return $result;
        }
        foreach ($rows as $row) {
            $order = new Order((int) $row['id_order']);
            if (!Validate::isLoadedObject($order)) {
                continue;
            }

            $withdrawal = Db::getInstance()->getRow(
                'SELECT status FROM `' . _DB_PREFIX_ . 'cc_desistimiento`
                 WHERE id_order=' . (int) $order->id . '
                 ORDER BY id_cc_desistimiento DESC'
            );

            if (!$withdrawal && !$this->canRequestWithdrawal($order)) {
                continue;
            }

            $alreadyRequested = is_array($withdrawal) && isset($withdrawal['status']);
            $status = $alreadyRequested ? (string) $withdrawal['status'] : '';

            $result[] = array(
                'id_order' => (int) $order->id,
                'reference' => $order->reference,
                'url' => $this->context->link->getModuleLink($this->name, 'request', array('id_order' => (int) $order->id), true),
                'already_requested' => $alreadyRequested,
                'status' => $status,
                'status_text' => $alreadyRequested ? $this->getCustomerWithdrawalStatusLabel($status) : '',
            );
        }
        return $result;
    }

    public function hookDisplayCustomerAccount($params)
    {
        $this->context->smarty->assign(array(
            'cc_desistimiento_orders_link' => $this->context->link->getPageLink('history', true),
            'cc_desistimiento_customer_account_label' => $this->ccL('request_from_order'),
        ));
        return $this->display(__FILE__, 'views/templates/hook/customer_account.tpl');
    }

    public function hookDisplayOrderDetail($params)
    {
        $order = null;
        if (isset($params['order']) && $params['order'] instanceof Order) {
            $order = $params['order'];
        } elseif (Tools::getValue('id_order')) {
            $order = new Order((int) Tools::getValue('id_order'));
        }
        if (!$order || !Validate::isLoadedObject($order)) {
            return '';
        }
        if (!$this->canRequestWithdrawal($order)) {
            return '';
        }
        $existing = Db::getInstance()->getRow(
            'SELECT status FROM `' . _DB_PREFIX_ . 'cc_desistimiento`
             WHERE id_order=' . (int) $order->id . '
             ORDER BY id_cc_desistimiento DESC'
        );
        if ($existing) {
            $status = isset($existing['status']) ? (string) $existing['status'] : 'pendiente';
            $this->context->smarty->assign(array(
                'cc_desistimiento_already_requested' => true,
                'cc_desistimiento_title' => $this->ccL('right_of_withdrawal'),
                'cc_desistimiento_already_requested_text' => $this->getCustomerWithdrawalStatusLabel($status),
            ));
            return $this->display(__FILE__, 'views/templates/hook/order_detail.tpl');
        }
        $this->context->smarty->assign(array(
            'cc_desistimiento_already_requested' => false,
            'cc_desistimiento_link' => $this->context->link->getModuleLink($this->name, 'request', array('id_order' => (int) $order->id), true),
            'cc_desistimiento_title' => $this->ccL('right_of_withdrawal'),
            'cc_desistimiento_order_detail_text' => $this->ccL('order_detail_text'),
            'cc_desistimiento_button_label' => $this->ccL('request_withdrawal'),
        ));
        return $this->display(__FILE__, 'views/templates/hook/order_detail.tpl');
    }

    public function canRequestWithdrawal(Order $order)
    {
        $customer = $this->context->customer;

        if (!$customer || !$customer->isLogged()) {
            return false;
        }

        if ((int) $customer->id !== (int) $order->id_customer) {
            return false;
        }

        if ($this->orderHasOnlyExcludedProducts($order)) {
            return false;
        }

        if (empty($order->date_add)) {
            return false;
        }

        $days = (int) Configuration::get('CC_DESISTIMIENTO_DAYS');

        if ($days <= 0) {
            $days = 14;
        }

        $limit = strtotime($order->date_add . ' +' . $days . ' days');

        return time() <= $limit;
    }

    public function orderHasOnlyExcludedProducts(Order $order)
    {
        $excluded = array_filter(array_map('intval', explode(',', (string) Configuration::get('CC_DESISTIMIENTO_EXCLUDED_CATEGORIES'))));
        if (!$excluded) {
            return false;
        }
        $products = $order->getProducts();
        if (!$products) {
            return false;
        }
        foreach ($products as $productRow) {
            $idProduct = (int) $productRow['product_id'];
            $categories = Product::getProductCategories($idProduct);
            if (!array_intersect($excluded, array_map('intval', $categories))) {
                return false;
            }
        }
        return true;
    }

    public function hookDisplayCustomerOrderWithdrawalButton($params)
    {
       
        if (empty($params['order'])) {
            return '';
        }

        $orderPresenter = $params['order'];

        if (empty($orderPresenter['details']['reference'])) {
            return '';
        }

        $reference = pSQL($orderPresenter['details']['reference']);

        $idOrder = (int) Db::getInstance()->getValue(
            'SELECT id_order
            FROM ' . _DB_PREFIX_ . 'orders
            WHERE reference = "' . $reference . '"
            AND id_customer = ' . (int) $this->context->customer->id
        );
      
        if (!$idOrder) {
            return '';
        }

        $order = new Order($idOrder);

        if (!$this->canRequestWithdrawal($order)) {
            return '';
        }

        $this->context->smarty->assign([
            'cc_desistimiento_url' => $this->context->link->getModuleLink(
                $this->name,
                'request',
                ['id_order' => $idOrder],
                true
            ),
            'cc_desistimiento_button_label' => $this->ccL('request_withdrawal'),
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/history_button.tpl');
    }

    private function installCustomHooks()
    {
        $hookName = 'displayCustomerOrderWithdrawalButton';

        $idHook = (int) Hook::getIdByName($hookName);

        if (!$idHook) {
            $hook = new Hook();
            $hook->name = $hookName;
            $hook->title = 'Display customer order withdrawal button';
            $hook->description = 'Shows withdrawal button in customer order history';
            $hook->position = 1;
            $hook->add();
        }

        return $this->registerHook($hookName);
    }
}
