<?php

require_once dirname(__FILE__) . '/../vendor/ecommerce_common_lib/config.inc.php';

class Analitica
{
    protected $host;
    protected $url_shop_record;
    protected $url_module_record;
    protected $url_external_module_record;
    protected $url_uninstall_record;
    protected $url_deactivate_record;
    protected $url_notification_list;
    protected $url_notification_check;
    protected $url_module_config;
    protected $url_module_config_sender;
    public $context;
    public $shop_url;
    public $shop_name;
    
    public function __construct()
    {
        $this->host = Config::getAnaliticaHost();

        $this->url_shop_record            = 'https://' . $this->host . '/logistics/accregavex/api/v1/shop/record';
        $this->url_external_module_record = 'https://' . $this->host . '/logistics/accregavex/api/v1/shop/external-modules';
        $this->url_module_record          = 'https://' . $this->host . '/logistics/accregavex/api/v1/module/record';
        $this->url_uninstall_record       = 'https://' . $this->host . '/logistics/accregavex/api/v1/module/uninstall';
        $this->url_deactivate_record      = 'https://' . $this->host . '/logistics/accregavex/api/v1/module/deactivate';
        $this->url_module_config          = 'https://' . $this->host . '/logistics/accregavex/api/v1/module/configuration';
        $this->url_module_config_sender   = 'https://' . $this->host . '/logistics/accregavex/api/v1/module/configuration-sender';
        $this->url_notification_list      = 'https://' . $this->host . '/logistics/accregavex/api/v1/notification/list';
        $this->url_notification_check     = 'https://' . $this->host . '/logistics/accregavex/api/v1/notification/check';

        // Shop Url
        $this->context = Context::getContext();
        $shop = (Configuration::get('PS_SSL_ENABLED')) ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_;
        $shop = strpos($shop, 'localhost') == false ? $shop : 'https://' . $this->context->shop->name . '.es';
        $this->shop_url = $shop;

        // Shop Name
        $this->shop_name = $this->context->shop->name;
        if(empty($this->shop_name)) {
            $this->shop_name = $this->shop_url;
        }

    }

    public function gdpr($vars)
    {
        if (isset($vars['correos-gdpr-check']) && 
            $vars['correos-gdpr-check'] === 'on' && 
            isset($vars['correos-dataProtect-check']) &&
            $vars['correos-dataProtect-check'] === 'on'
        ) {            
            $isRegistered = $this->shopRecord();

            if ($isRegistered['status'] == 200 || $isRegistered['status'] == 201) {

                $thisMoment = date('Y-m-d H:i:s');
                $fields = [
                    'GDPR' => 1,
                    'Analitica_date' => $thisMoment
                ];
                foreach ($fields as $fk => $fv) {
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->update('correos_oficial_configuration',['value' => $fv],'name = "' . $fk . '"');
                }

            }
            unset($thisMoment, $fields, $vars['correos-gdpr-check'], $vars['correos-betatester-check']);
            
            $this->moduleRecord();
            $this->externalModulesRecord();
            $this->configurationCall('undefined');

        }
        $gdpr = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT
                `value`
            FROM
                ' . _DB_PREFIX_ . 'correos_oficial_configuration
            WHERE
                `name` = "GDPR"
        ');

        if ($gdpr === 0) {
                return true;
            }
            return false;
    }

    public function analiticaApi($url, $method, $body)
    {
        $contentLength = strlen(json_encode($body, JSON_UNESCAPED_SLASHES));

        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . $contentLength,
            'User-Agent: PHP-Prestashop',
            'Host: ' . $this->host,
            'client_id: ' . ANALYTICS_CLIENT_ID,
            'client_secret: ' . ANALYTICS_CLIENT_SECRET
        ];

        if (strtoupper($method) === 'GET') {
            unset($headers[1]);
            $first = true;
            foreach ($body as $key => $param) {
                if ($first) {
                    $url .= '?';
                    $first = false;
                } else {
                    $url .= '&';
                }
                $url .= $key . '=' . $param;
            }
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if (strtoupper($method) === 'POST') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_SLASHES));
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($curl);
        
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $return = [
            'output' => json_decode($response),
            'status' => $httpCode
        ];

        $logData = array(
			'url' => $url,
			'method' => $method,
			'body' => $body,
			'response' => $return,
		);

		// Para debug en el archivo de debug.log de Wordpress
		// error_log('Analitica API: ' . print_r($logData, true));

        return $return;
    }

    protected function getVersions($type)
    {
        $return = false;
        switch($type) {
            case 'module':
                $configFile = file_get_contents(_PS_CORE_DIR_ . '/modules/correosoficial/config.xml');
                $module = new SimpleXMLElement($configFile);
                $return = $module->version;
                break;
            case 'db':
                $return = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT VERSION()');
                break;
            default:
                break;
        }

        return $return;
    }

    public function shopRecord()
    {
        $body = [
            'shopDistinctive' => $this->shop_url,
            'shopName' => $this->shop_name
        ];
        return $this->analiticaApi($this->url_shop_record, 'POST', $body);
    }

    public function moduleRecord()
    {
        $moduleVersion = $this->getVersions('module');
        $dbVersion = $this->getVersions('db');
        $body = [
            'shopDistinctive' => $this->shop_url,
            'moduleCode' => 'PRST',
            'moduleVersion' =>  (string) $moduleVersion,
            'databaseCode' => 'MYSQL',
            'databaseVersion' => $dbVersion,
            'techVersion' => phpversion(),
            'platformVersion' => _PS_VERSION_ 
        ];
        $this->analiticaApi($this->url_module_record, 'POST', $body);
    }

    public function externalModulesRecord()
    {
        $sql = '
        SELECT
            CONCAT(`name`," ",`version`) AS name, `active` = 1 AS isActive
        FROM
            ' . _DB_PREFIX_ . 'module
        ';
        $modules = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        foreach ($modules as $mi => $module) {
            if ($module['isActive'] === '1') {
                unset($modules[$mi]['isActive']);
            } else {
                $modules[$mi]['isActive'] = false;
            }
        }

        $body = [
            'shopDistinctive' => $this->shop_url,
            'externalModules' => $modules
        ];

        $this->analiticaApi($this->url_external_module_record, 'POST', $body);
    }

    public function configurationCall($betatester)
    {
        if ($betatester === 'undefined') {
            $betatester = (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                SELECT
                    value
                FROM
                    ' . _DB_PREFIX_ . 'correos_oficial_configuration
                WHERE
                    name = "betatester"
                AND
                    type = "analitica"
            ');
        }

        $body = [
            'shopDistinctive' => $this->shop_url,
            'moduleCode' => 'PRST',
            'isBetaTester' => false, //$betatester
            'sender' => [],
        ];

        $sqlSenders = 'SELECT * FROM ' . _DB_PREFIX_ . 'correos_oficial_senders';
        $senders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sqlSenders);

        foreach($senders as $sender) {

            $accounts = [];

            if($sender['correos_code']){
                $sqlCorreosCode = 'SELECT * FROM ' . _DB_PREFIX_ . 'correos_oficial_codes WHERE id = ' . $sender['correos_code'];
                $correosCode = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sqlCorreosCode);
                if (!empty($correosCode)) {
                    $accounts[0]['correosCustomerCode'] = $correosCode['CorreosCustomer'];
                    $accounts[0]['contractNumber'] = $correosCode['CorreosContract'];
                    $accounts[0]['labellerCode'] = $correosCode['CorreosKey'];
                }
            }

            if($sender['cex_code']){
                $sqlCexCode = 'SELECT * FROM ' . _DB_PREFIX_ . 'correos_oficial_codes WHERE id = ' . $sender['cex_code'];
                $cexCode = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sqlCexCode);
                if (!empty($cexCode)) {
                    $accounts[0]['cexCustomerCode'] = $cexCode['CEXCustomer'];
                }
            }

            $body['sender'][] = [
                'isDefault' => $sender['sender_default'] === '1' ? true : false,
                'countryCode' => $sender['sender_iso_code_pais'],
                'postalCode' => $sender['sender_cp'],
                'account' => $accounts,
            ];

        }

        $this->analiticaApi($this->url_module_config_sender, 'POST', $body);
    }

    public function uninstallCall()
    {
        $body = [
            'shopDistinctive' => $this->shop_url,
            'moduleCode' => 'PRST'
        ];

        $this->analiticaApi($this->url_uninstall_record, 'POST', $body);
    }

    public function disableCall()
    {
        $body = [
            'shopDistinctive' => $this->shop_url,
            'moduleCode' => 'PRST'
        ];

        $this->analiticaApi($this->url_deactivate_record, 'POST', $body);
    }

    public function getNotifications()
    {
        $params = [
            'shopDistinctive' => base64_encode($this->shop_url),
            'moduleCode' => 'PRST'
        ];
        $results = $this->analiticaApi($this->url_notification_list, 'GET', $params);
        
        return $results;
    }

    public function checkNotifications($id)
    {
        $body = [
            'shopDistinctive' => $this->shop_url,
            'moduleCode' => 'PRST',
            'notificationId' => (int) $id
        ];

        $this->analiticaApi($this->url_notification_check, 'POST', $body);
    }

    public static function gdprAccepted()
    {
        $sql = '
        SELECT
            `value`
        FROM
            ' . _DB_PREFIX_ . 'correos_oficial_configuration
        WHERE
            `name` = "GDPR"
        AND
            `type` = "analitica"
        ';

        $tableExists = Db::getInstance()->ExecuteS('SHOW TABLES LIKE "'._DB_PREFIX_.'correos_oficial_configuration"');

        if (!$tableExists) {
            return false;
        } 

            
        if ((int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql) === 1) {
            return true;
        }
        return false;
    }

    public function lastHour()
    {
        $sql = '
        SELECT
            `value`
        FROM
        ' . _DB_PREFIX_ . 'correos_oficial_configuration
        WHERE
            `name` = "Analitica_date"
        ';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
}
