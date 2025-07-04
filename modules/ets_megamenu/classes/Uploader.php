<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

class Uploader
{
    const DEFAULT_MAX_SIZE = 10485760;
    private $_check_file_size;
    private $_accept_types;
    private $_files;
    private $_max_size;
    private $_name;
    private $_save_path;
    public function __construct($name = null)
    {
        $this->setName($name);
        $this->setCheckFileSize(true);
        $this->files = array();
    }
    public function setAcceptTypes($value)
    {
        if (is_array($value) && count($value)) {
            $value = array_map(array('Tools', 'strtolower'), $value);
        }
        $this->_accept_types = $value;
        return $this;
    }
    public function getAcceptTypes()
    {
        return $this->_accept_types;
    }
    public function setCheckFileSize($value)
    {
        $this->_check_file_size = $value;
        return $this;
    }
    public function getFilePath($file_name = null)
    {
        if (!isset($file_name)) {
            return tempnam($this->getSavePath(), $this->getUniqueFileName());
        }
        return $this->getSavePath().$file_name;
    }
    public function getFiles()
    {
        if (!isset($this->_files)) {
            $this->_files = array();
        }
        return $this->_files;
    }
    public function setMaxSize($value)
    {
        $this->_max_size = (int)$value;
        return $this;
    }
    public function getMaxSize()
    {
        if (!isset($this->_max_size) || empty($this->_max_size)) {
            $this->setMaxSize(self::DEFAULT_MAX_SIZE);
        }
        return $this->_max_size;
    }
    public function setName($value)
    {
        $this->_name = $value;
        return $this;
    }
    public function getName()
    {
        return $this->_name;
    }
    public function setSavePath($value)
    {
        $this->_save_path = $value;
        return $this;
    }
    public function getPostMaxSizeBytes()
    {
        $post_max_size = ini_get('post_max_size');
        $bytes         = (int)trim($post_max_size);
        $last          = Tools::strtolower($post_max_size[Tools::strlen($post_max_size) - 1]);
        switch ($last) {
            case 'g': $bytes *= 1024;
            case 'm': $bytes *= 1024;
            case 'k': $bytes *= 1024;
        }
        if ($bytes == '') {
            $bytes = null;
        }
        return $bytes;
    }
    public function getSavePath()
    {
        if (!isset($this->_save_path)) {
            $this->setSavePath(_PS_UPLOAD_DIR_);
        }
        return $this->_normalizeDirectory($this->_save_path);
    }
    public function getUniqueFileName($prefix = 'PS')
    {
        return uniqid($prefix, true);
    }
    public function checkFileSize()
    {
        return (isset($this->_check_file_size) && $this->_check_file_size);
    }
    public function process($dest = null)
    {
        $upload = isset($_FILES[$this->getName()]) ? $_FILES[$this->getName()] : null;
        if ($upload && is_array($upload['tmp_name'])) {
            $tmp = array();
            foreach ($upload['tmp_name'] as $index => $value) {
                $tmp[$index] = array(
                    'tmp_name' => $upload['tmp_name'][$index],
                    'name'     => $upload['name'][$index],
                    'size'     => $upload['size'][$index],
                    'type'     => $upload['type'][$index],
                    'error'    => $upload['error'][$index]
                );
                $this->files[] = $this->upload($tmp[$index], $dest);
            }
            unset($value);
        } elseif ($upload) {
            $this->files[] = $this->upload($upload, $dest);
        }
        return $this->files;
    }
    public function upload($file, $dest = null)
    {
        if ($this->validate($file)) {
            if (isset($dest) && is_dir($dest)) {
                $file_path = $dest;
            } else {
                $file_path = $this->getFilePath(isset($dest) ? $dest : $file['name']);
            }
            if ($file['tmp_name'] && is_uploaded_file($file['tmp_name'])) {
                move_uploaded_file($file['tmp_name'], $file_path);
            } else {
                // Non-multipart uploads (PUT method support)
                file_put_contents($file_path, fopen('php://input', 'r'));
            }
            $file_size = $this->_getFileSize($file_path, true);
            if ($file_size === $file['size']) {
                $file['save_path'] = $file_path;
            } else {
                $file['size'] = $file_size;
                if(file_exists($file_path))
                    unlink($file_path);
                $file['error'] = Tools::displayError('Server file size is different from local file size');
            }
        }
        return $file;
    }
    protected function checkUploadError($error_code)
    {
        $error = 0;
        switch ($error_code) {
            case 1:
                $error = sprintf(Tools::displayError('The uploaded file exceeds %s'), ini_get('upload_max_filesize'));
                break;
            case 2:
                $error = sprintf(Tools::displayError('The uploaded file exceeds %s'), ini_get('post_max_size'));
                break;
            case 3:
                $error = Tools::displayError('The uploaded file was only partially uploaded');
                break;
            case 4:
                $error = Tools::displayError('No file was uploaded');
                break;
            case 6:
                $error = Tools::displayError('Missing temporary folder');
                break;
            case 7:
                $error = Tools::displayError('Failed to write file to disk');
                break;
            case 8:
                $error = Tools::displayError('A PHP extension stopped the file upload');
                break;
            default:
                break;
        }
        return $error;
    }
    protected function validate(&$file)
    {
        $file['error'] = $this->checkUploadError($file['error']);
        if ($file['error']) {
            return false;
        }
        $post_max_size = $this->getPostMaxSizeBytes();
        if ($post_max_size && ($this->_getServerVars('CONTENT_LENGTH') > $post_max_size)) {
            $file['error'] = Tools::displayError('The uploaded file exceeds the post_max_size directive in php.ini');
            return false;
        }
        if (preg_match('/\%00/', $file['name'])) {
            $file['error'] = Tools::displayError('Invalid file name');
            return false;
        }
        $types = $this->getAcceptTypes();
        //TODO check mime type.
        if (isset($types) && !in_array(Tools::strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), $types)) {
            $file['error'] = Tools::displayError('Filetype not allowed');
            return false;
        }
        if ($this->checkFileSize() && $file['size'] > $this->getMaxSize()) {
            $file['error'] = sprintf(Tools::displayError('File (size : %1s) is too big (max : %2s)'), $file['size'], $this->getMaxSize());
            return false;
        }
        return true;
    }
    protected function _getFileSize($file_path, $clear_stat_cache = false)
    {
        if ($clear_stat_cache) {
            clearstatcache(true, $file_path);
        }
        return filesize($file_path);
    }
    protected function _getServerVars($var)
    {
        return (isset($_SERVER[$var]) ? $_SERVER[$var] : '');
    }
    protected function _normalizeDirectory($directory)
    {
        $last = $directory[Tools::strlen($directory) - 1];
        if (in_array($last, array('/', '\\'))) {
            $directory[Tools::strlen($directory) - 1] = DIRECTORY_SEPARATOR;
            return $directory;
        }
        $directory .= DIRECTORY_SEPARATOR;
        return $directory;
    }
}
