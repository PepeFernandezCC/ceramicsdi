<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licensed under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    MyPrestaModules
 * @copyright 2013-2020 MyPrestaModules
 * @license LICENSE.txt
 */

require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportFile.php';


if (!class_exists('Crypt_RSA')) {
    require_once _PS_MODULE_DIR_ . 'exportproducts/libraries/phpseclib/Crypt/RSA.php';
}

class PEFTPManager
{
    private $protocol;
    private $port;
    private $server;
    private $user;
    private $password;
    private $path;
    private $authentication_type;
    private $passive_mode;
    private $file_transfer_mode;

    public function __construct($configuration)
    {
        $this->protocol = $configuration['ftp_protocol'];
        $this->port = $this->getPortFromConfig($configuration['ftp_port']);
        $this->server = $configuration['ftp_server'];
        $this->user = $configuration['ftp_username'];
        $this->password = $configuration['ftp_password'];
        $this->key_path = !empty($configuration['ftp_key_path']) ? $configuration['ftp_key_path'] : '';
        $this->path = $this->getSavePathFromConfig($configuration['ftp_folder_path']);
        $this->passive_mode = !empty($configuration['ftp_passive_mode']) ? $configuration['ftp_passive_mode'] : 0;
        $this->file_transfer_mode = !empty($configuration['ftp_file_transfer_mode']) ? $configuration['ftp_file_transfer_mode'] : FTP_ASCII;
        $this->authentication_type = !empty($configuration['ftp_authentication_type']) ? $configuration['ftp_authentication_type'] : 'password';
    }

    public function copyFileToServer(PEExportFile $export_file)
    {
        if ($this->protocol == 'sftp') {
            $this->copyFileToSFTPServer($export_file);
        } else {
            $this->copyFileToFTPServer($export_file);
        }
    }

    private function copyFileToFTPServer(PEExportFile $export_file)
    {
        $connection_id = @ftp_connect($this->server, $this->port);

        if (!$connection_id) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Can not connect to FTP server!', __CLASS__));
        }

        $is_logged = @ftp_login($connection_id, $this->user, $this->password);

        if (!$is_logged) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Can not login to FTP account! Make sure that username and password are correct!', __CLASS__));
        }

        if ($this->passive_mode) {
            ftp_pasv($connection_id, true);
        }

        $shop_server_file_path = $export_file->getServerPathToFile();
        $ftp_server_file_path = $this->path . $export_file->getName();

        $is_saved = ftp_put($connection_id, $ftp_server_file_path, $shop_server_file_path, $this->file_transfer_mode);

        if (!$is_saved) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Can not save file to FTP server!', __CLASS__));
        }

        return true;
    }

    private function copyFileToSFTPServer(PEExportFile $export_file)
    {
        require_once _PS_MODULE_DIR_ . 'exportproducts/libraries/phpseclib/Net/SFTP.php';

        $sftp = new Net_SFTP($this->server, $this->port);

        if ($this->authentication_type === 'key') {
            $key = new Crypt_RSA();
            if (!empty($this->password)) {
                $key->setPassword($this->password);
            }
            $key->loadKey(Tools::file_get_contents($this->key_path));
            $is_logged = $sftp->login($this->user, $key);

        } else {
            $is_logged = $sftp->login($this->user, $this->password);
        }

        if (!$is_logged) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Can not login to FTP account! Make sure that username and password are correct!', __CLASS__));
        }

        $shop_server_file_path = $export_file->getServerPathToFile();
        $ftp_server_file_path = $this->path . $export_file->getName();

        $is_saved = $sftp->put($ftp_server_file_path, $shop_server_file_path, NET_SFTP_LOCAL_FILE);

        if (!$is_saved) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Can not save file to FTP server!', __CLASS__));
        }

        return true;
    }

    private function getSavePathFromConfig($path_from_configuration)
    {
        $path = '';

        if ($path_from_configuration) {
            $path = $path_from_configuration . '/';
            $path = str_replace('//', '/', $path);
        }

        return $path;
    }

    private function getPortFromConfig($port_from_configuration)
    {
        $default_port = ($this->protocol === 'sftp') ? 22 : 21;
        return !empty($port_from_configuration) ? $port_from_configuration : $default_port;
    }
}