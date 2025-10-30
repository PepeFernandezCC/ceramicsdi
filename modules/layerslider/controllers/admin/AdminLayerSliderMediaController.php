<?php
/**
 * Creative Slider - Responsive Slideshow Module
 * https://creativeslider.webshopworks.com
 *
 * @author    WebshopWorks <info@webshopworks.com>
 * @copyright 2015-2025 WebshopWorks
 * @license   One Domain Licence
 *
 * Not allowed to resell or redistribute this software
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminLayerSliderMediaController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        include _PS_MODULE_DIR_ . 'layerslider/mediamanager/php/functions.php';

        header_register_callback(function () {
            header_remove('Content-Security-Policy');
            header_remove('X-Content-Type-Options');
            header_remove('X-Frame-Options');
            header_remove('X-Xss-Protection');
        });
    }

    public function postProcess()
    {
        parent::postProcess();
        if (isset($this->context->cookie->ls_error)) {
            $this->errors[] = $this->context->cookie->ls_error;
            unset($this->context->cookie->ls_error);
        }
        $action = Tools::getValue('action', '');
        if ($action) {
            $this->{'action' . $action}();
        }
    }

    public function actionListImages()
    {
        $dir = preg_replace('/\.+/', '.', Tools::getValue('d'));
        $imagepath = _PS_IMG_DIR_ . $dir;

        if ($del = Tools::getValue('del')) {
            $delimagepath = $imagepath . urldecode($del);

            if (realpath($delimagepath) === $delimagepath) {
                @call_user_func('unlink', $delimagepath);
            }
        }

        $files = is_dir($imagepath) ? scandir($imagepath) : [];
        $files_array = [];

        if (_PS_IMG_DIR_ !== $imagepath) {
            $files_array[] = '..';
        }

        foreach ($files as $file_name) {
            $file_path = $imagepath . $file_name;
            if (is_dir($file_path) && $file_name[0] !== '.' && ($imagepath !== _PS_IMG_DIR_ || !in_array($file_name, ['admin', 'tmp', 'jquery-ui']))) {
                $files_array[] = $file_name;
            }
        }
        $pattern = '/\.(jpe?g|png|gif|bmp|webp)$/i';
        // $mediatype = Tools::getValue('type', 'image');
        foreach ($files as $file_name) {
            $file_path = $imagepath . $file_name;
            if (is_file($file_path) && '.' !== $file_name[0] && preg_match($pattern, $file_name)) {
                $files_array[] = $file_name;
            }
        }

        $nofiles = count($files_array);
        $resultspp = 4096;
        $nopages = ceil($nofiles / $resultspp);

        $pageno = Tools::getValue('p');

        $error = [
            'thumbhtml' => iconv('UTF-8', 'UTF-8//IGNORE', display_gallery_page($files_array, $pageno, $dir, $resultspp, false)),
            'paginationhtml' => display_gallery_pagination('', count($files_array), $pageno, $resultspp, false),
            'noofpages' => $nopages,
        ];

        exit(json_encode([$error]));
    }

    public function actionUploadFile()
    {
        $uploadfolder = _PS_IMG_DIR_;
        $reluploadfolder = _PS_IMG_;

        if (Tools::getIsset('uploadfolder')) {
            $dir = preg_replace('/\.+/', '.', Tools::getValue('uploadfolder'));
            $uploadfolder .= $dir;
            $reluploadfolder .= $dir;
        }
        if (file_exists($uploadfolder)) {
            if (isset($_FILES['userfile']['name'])) {
                $mediatype = Tools::getValue('mediatype');

                $tname = $_FILES['userfile']['name'];

                $name = strtr($tname, 'ŔÁÂĂÄĹÇČÉĘËĚÍÎĎŇÓÔŐÖŮÚŰÜÝŕáâăäĺçčéęëěíîďđňóôőöůúűüý˙', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
                $name = preg_replace('/\s+/', '-', $name);

                $fileext = Tools::strtolower(pathinfo($name, PATHINFO_EXTENSION));

                $filename = Tools::strtolower(pathinfo($name, PATHINFO_FILENAME));

                $destination = $uploadfolder . $name;
                $reldestination = $reluploadfolder . $name;

                if (file_exists($destination)) {
                    $uniqid = uniqid();
                    $filename = $filename . '_' . $uniqid;
                    $name = $filename . '.' . $fileext;
                    $destination = $uploadfolder . $name;
                    $reldestination = $reluploadfolder . $name;
                }

                if (move_uploaded_file($_FILES['userfile']['tmp_name'], $destination)) {
                    $uploadinfo = new stdClass();

                    $uploadinfo->name = $name;
                    $uploadinfo->destination = $reldestination;
                    $uploadinfo->mediatype = $mediatype;
                    exit(json_encode([$uploadinfo]));
                } else {
                    $error = ['error' => 'There was a problem uploading the file, please try again'];
                    exit(json_encode([$error]));
                }
            } else {
                $error = ['error' => 'No file sent'];
                exit(json_encode([$error]));
            }
        } else {
            $error = ['error' => 'Upload folder not correctly configured! ' . $uploadfolder];
            exit(json_encode([$error]));
        }
    }

    public function initContent()
    {
        if ((int) _PS_VERSION_ < 9 && !$this->viewAccess()) {
            $this->errors[] = Tools::displayError('You do not have permission to view this.');

            return;
        }

        require_once _PS_MODULE_DIR_ . $this->module->name . '/mediamanager/mediamanager.php';
        exit;
    }
}
