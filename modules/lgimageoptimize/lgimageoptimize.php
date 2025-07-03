<?php
/**
 * Copyright 2022 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use Intervention\Image\ImageManager as Intervention;

require realpath(dirname(__FILE__)) . '/config/config.inc.php';

class LGImageOptimize extends Module
{
    public $webp_available;

    public function __construct()
    {
        $this->name = 'lgimageoptimize';
        $this->tab = 'quick_bulk_update';
        $this->version = '1.0.5';
        $this->author = 'Línea Gráfica';
        $this->module_key = '55ed3ac7e999d7f0bacd9af501426c3b';
        $this->need_instance = 1;

        $this->bootstrap = true;

        parent::__construct();

        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_,
        ];

        $this->displayName = $this->l('Optimize your catalog images');
        $this->description = $this->l('Configure your miniature generator to optimize and improve your images');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall LGPrime module and related data?');

        $this->l('Normal');
        $this->l('Hight');
        $this->l('Very Hight');

        $gd_info = gd_info();

        if (isset($gd_info['WebP Support']) && $gd_info['WebP Support'] == 1) {
            $this->webp_available = 1;
        } else {
            $this->webp_available = 0;
        }
    }

    public function install()
    {
        Configuration::updateValue('LGIMAGEOPTIMIZE_COMPRESSION_RATE', 90);
        Configuration::updateValue('LGIMAGEOPTIMIZE_SHARPEN_RATE', 5);

        return parent::install() &&
            $this->registerHook('actionOnImageResizeAfter') &&
            $this->registerHook('displayBackofficeHeader');
    }

    public function installOverrides()
    {
        $path = _PS_MODULE_DIR_ . $this->name .
            DIRECTORY_SEPARATOR . 'override' .
            DIRECTORY_SEPARATOR . 'classes' .
            DIRECTORY_SEPARATOR;

        if (version_compare(_PS_VERSION_, '8.0', '>=')) {
            copy($path . 'ImageManager80.php', $path . 'ImageManager.php');
        } elseif (version_compare(_PS_VERSION_, '1.7.2.0', '>=')) {
            copy($path . 'ImageManager1720.php', $path . 'ImageManager.php');
        } elseif (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            copy($path . 'ImageManager1700.php', $path . 'ImageManager.php');
        } elseif (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            copy($path . 'ImageManager1610.php', $path . 'ImageManager.php');
        } elseif (version_compare(_PS_VERSION_, '1.6.0.11', '>=')) {
            copy($path . 'ImageManager16011.php', $path . 'ImageManager.php');
        } else {
            copy($path . 'ImageManager1604.php', $path . 'ImageManager.php');
        }

        return parent::installOverrides();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->postProcess();
        $id_lang = $this->context->language->id;

        $image_types = array();
        $image_types_final = array();
        foreach ($image_types as $index => $image_type) {
            $k = 0;
            foreach ($image_type as $value) {
                $image_types_final[$index][$k] = $value['name'];
                $k++;
            }
        }

        $this->context->smarty->assign('image_types', self::jsonEncode($image_types_final));
        $this->context->smarty->assign('lgimageoptimize_token', Tools::getAdminTokenLite('AdminModules'));

        // Writing test
        $testfile = _PS_IMG_DIR_ . DIRECTORY_SEPARATOR . 'p' . DIRECTORY_SEPARATOR . 'test.txt';
        $f = fopen($testfile, 'w+');
        if ($f !== false) {
            fclose($f);
            unlink($testfile);
        } else {
            $this->_errors[] = $this->displayError(
                $this->l('Error: Cannot write in ')
                . _PS_IMG_DIR_ . DIRECTORY_SEPARATOR . 'p'
                . $this->l(' please check file permission.')
            );
        }

        $link = $this->context->link;

        $id_product = (int) Configuration::get('LGIMAGEOPTIMIZE_PRODUCT_SELECTED', 0);
        if ($id_product) {
            $product = new Product($id_product, true, $id_lang);
            $cover = Product::getCover($id_product);
            $image = new Image((int) $cover['id_image']);

            $url_image =
                Context::getContext()->shop->getBaseURL(true) .
                'img/p/' .
                $image->getImgPath() .
                '-' .
                (Tools::version_compare(_PS_VERSION_, '1.7', '>=') ?
                    ImageType::getFormattedName('cart') :
                    ImageType::getFormatedName('cart')
                ) .
                '.jpg';
        } else {
            $product = null;
            $url_image = '';
        }

        if (Module::isEnabled('lgimagesregenerator')) {
            $url_regenerate_tools = $this->context->link->getAdminLink('AdminModules') .
                '&configure=lgimagesregenerator';
        } else {
            $url_regenerate_tools = $this->context->link->getAdminLink('AdminImages');
        }

        $this->context->smarty->assign(array(
            'urlmoduleimageoptimize' => $link->getModuleLink('lgimageoptimize', 'actions'),
            'type_image' => ImageTypeCore::getImagesTypes(),
            'type_image_selected' => Configuration::get('LGIMAGEOPTIMIZE_TYPE_IMAGE', ''),
            'product_search' => Configuration::get('LGIMAGEOPTIMIZE_PRODUCT_SEARCH', ''),
            'product_result' => Configuration::get('LGIMAGEOPTIMIZE_PRODUCT_RESULT', ''),
            'product_select' => $product,
            'url_image' => $url_image,
            'active_function' => Configuration::get('LGIMAGEOPTIMIZE_ACTIVE'),
            'url_regenerate_tools' => $url_regenerate_tools,
            'self' => dirname(__FILE__),
        ));

        $html_header = $this->display(__FILE__, '/views/templates/admin/_partials/header.tpl');
        $html_search = $this->display(__FILE__, '/views/templates/admin/_partials/demo_form.tpl');
        $html_footer = $this->display(__FILE__, '/views/templates/admin/_partials/footer.tpl');

        return $this->getP('top') .
            $html_header .
            $this->renderFormConfiguration() .
            $html_search .
            $html_footer .
            $this->getP('bottom');
    }

    public function hookDisplayBackofficeHeader()
    {
        if ($this->context->controller instanceof AdminController
            && pSQL(Tools::getValue('configure')) == $this->name
        ) {
            $this->context->controller->addJQuery();
            $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/back.js');
            $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/product_page.bundle.js');
            $this->context->controller->addCSS($this->_path . '/views/css/publi/lgpubli.css');
        }
    }

    protected function getConfigFormConfiguration()
    {
        $switch_or_radio = ($this->bootstrap) ? 'switch' : 'radio';

        $image_types = ImageType::getImagesTypes('products');
        $image_types_final = array();
        foreach ($image_types as $image_type) {
            $type = array();
            $type['id'] = $image_type['id_image_type'];
            $type['name'] = $image_type['name'];
            $image_types_final[] = $type;
        }

        $compression_rates = array();
        $compression_rates[] = array('value' => 80, 'id' => 'normal', 'label' => $this->l('Normal'));
        $compression_rates[] = array('value' => 90, 'id' => 'hight', 'label' => $this->l('Hight'));
        $compression_rates[] = array('value' => 100, 'id' => 'maximum', 'label' => $this->l('Very Hight'));

        $sharpen_rates = array();
        $sharpen_rates[] = array('value' => 0, 'id' => 'normal', 'label' => $this->l('Normal'));
        $sharpen_rates[] = array('value' => 5, 'id' => 'hight', 'label' => $this->l('Hight'));
        $sharpen_rates[] = array('value' => 10, 'id' => 'maximum', 'label' => $this->l('Very Hight'));

        $notice = $this->l('You can active this feature to generate high optimized images.');

        if (!$this->webp_available) {
            $notice =
                $this->l('You must active webp functions in your PHP Configuration. ') .
                $this->l('Contact with your hosting service.');
        }

        $return = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Optimize Images'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => $switch_or_radio,
                        'label' => $this->l('Active module'),
                        'desc' =>
                            $this->l('Enable this option if you want to force optimization.'),
                        'name' => 'active_imageoptimize',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_imageoptimiz_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_imageoptimiz_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => $switch_or_radio,
                        'label' => $this->l('Active WebP format'),
                        'desc' => $notice,
                        'name' => 'active_webp',
                        'required' => false,
                        'is_bool' => true,
                        'disabled' => ($this->webp_available != 1),
                        'values' => array(
                            array(
                                'id' => 'active_webp_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_webp_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => 'radio',
                        'values' => $compression_rates,
                        'label' => $this->l('Quality'),
                        'name' => 'compression_rate',
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Sharpen'),
                        'values' => $sharpen_rates,
                        'name' => 'sharpen_rate',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'button btn btn-default lgimageoptimize_btn',
                ),
            ),
        );

        return $return;
    }

    protected function getConfigFormValuesConfiguration()
    {
        return array(
            'active_imageoptimize' => (
            Configuration::get('LGIMAGEOPTIMIZE_ACTIVE') ? Configuration::get('LGIMAGEOPTIMIZE_ACTIVE') : '0'
            ),
            'active_advanced' => (
            Configuration::get('LGIMAGEOPTIMIZE_ADVANCED') ? Configuration::get('LGIMAGEOPTIMIZE_ADVANCED') : '0'
            ),
            'active_webp' => (
            Configuration::get('LGIMAGEOPTIMIZE_WEBP') && $this->webp_available ?
                Configuration::get('LGIMAGEOPTIMIZE_WEBP') : '0'
            ),
            'compression_rate' => (
            Configuration::get('LGIMAGEOPTIMIZE_COMPRESSION_RATE') ?
                Configuration::get('LGIMAGEOPTIMIZE_COMPRESSION_RATE') : '0'
            ),
            'sharpen_rate' => (
            Configuration::get('LGIMAGEOPTIMIZE_SHARPEN_RATE') ?
                Configuration::get('LGIMAGEOPTIMIZE_SHARPEN_RATE') : '0'
            ),
        );
    }

    protected function renderFormConfiguration()
    {
        $this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/views/css/jquery-ui.css');
        $this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/views/css/back.css');

        // Ensures jQuery loading
        $this->context->controller->addJquery();
        $this->context->controller->addJs(_MODULE_DIR_ . $this->name . '/views/js/jquery-ui.js');
        $this->context->controller->addJs(_MODULE_DIR_ . $this->name . '/views/js/admin.js');


        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLgimageoptimizeConfigurationModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValuesConfiguration(), /* Add values for your inputs */
        );

        return $helper->generateForm(array($this->getConfigFormConfiguration()));
    }

    protected function postProcess()
    {
        $this->context->smarty->assign('show_message', 0);
        if (Tools::isSubmit('submitLgimageoptimizeConfigurationModule')) {
            Configuration::updateValue('LGIMAGEOPTIMIZE_ACTIVE', Tools::getValue('active_imageoptimize', 0));
            // Configuration::updateValue('LGIMAGEOPTIMIZE_ADVANCED',Tools::getValue('active_advanced', 0));
            Configuration::updateValue('LGIMAGEOPTIMIZE_ADVANCED', 1);
            Configuration::updateValue('LGIMAGEOPTIMIZE_WEBP', Tools::getValue('active_webp', 0));
            Configuration::updateValue('LGIMAGEOPTIMIZE_COMPRESSION_RATE', Tools::getValue('compression_rate', 0));
            Configuration::updateValue('LGIMAGEOPTIMIZE_SHARPEN_RATE', Tools::getValue('sharpen_rate', 0));
            $this->context->smarty->assign('show_message', 1);
        }
    }

    public function hookActionOnImageResizeAfter($params)
    {
        if (!Configuration::get('LGIMAGEOPTIMIZE_ADVANCED') &&
            !get_class(Context::getContext()->controller) == 'LgImageOptimizeActionsModuleFrontController') {
            $dst_file = $params['dst_file'];
            $file_type = $params['file_type'];

            $quality = Configuration::get('LGIMAGEOPTIMIZE_COMPRESSION_RATE');
            $sharpen = Configuration::get('LGIMAGEOPTIMIZE_SHARPEN_RATE');


            $intervention = new ImageManager(array('driver' => 'gd'));
            //$intervention = new ImageManager(array('driver' => 'imagick'));

            // Creamos una imagen a partir de un archivo ya existente
            $image = $intervention->make($dst_file);

            if ($sharpen) {
                // Ajustamos la nitidez de la imagen
                $image->sharpen($sharpen);
            }

            if (Configuration::get('LGIMAGEOPTIMIZE_WEBP')) {
                $file_type = 'webp';
            }

            // guardamos la imagen en el sistema de archivos, con otro nombre
            $image->save($dst_file, $quality, $file_type);
        }
    }

    private function getP($template)
    {
        $iso_langs = array('es', 'en', 'fr', 'it', 'de');
        $current_iso_lang = $this->context->language->iso_code;
        $iso = (in_array($current_iso_lang, $iso_langs)) ? $current_iso_lang : 'en';

        $this->context->smarty->assign(
            array(
                'lgimageoptimize_iso' => $iso,
                'base_url' => _MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR,
            )
        );

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name
            . DIRECTORY_SEPARATOR . 'views'
            . DIRECTORY_SEPARATOR . 'templates'
            . DIRECTORY_SEPARATOR . 'admin'
            . DIRECTORY_SEPARATOR . '_p_' . $template . '.tpl'
        );
    }

    public static function jsonEncode($data, $options = 0, $depth = 512)
    {
        return method_exists('Tools', 'jsonEncode') ?
            Tools::jsonEncode($data) :
            json_encode($data, $options, $depth);
    }

    public static function jsonDecode($data, $assoc = false, $depth = 512, $options = 0)
    {
        return method_exists('Tools', 'jsonDecode') ?
            Tools::jsonDecode($data, $assoc) :
            json_decode($data, $assoc, $depth, $options);
    }

    // TRASLADAMOS LA FUNCiÖN AL MÖDULO


    /**
     * Resize, cut and optimize image
     *
     * @param string $src_file Image object from $_FILE
     * @param string $dst_file Destination filename
     * @param integer $dst_width Desired width (optional)
     * @param integer $dst_height Desired height (optional)
     * @param string $file_type
     *
     * @return boolean Operation result
     */
    public function resizeLG1604(
        $src_file,
        $dst_file,
        $dst_width = null,
        $dst_height = null,
        $file_type = 'jpg',
        $force_type = false,
        &$error = 0
    )
    {
        if (PHP_VERSION_ID < 50300) {
            clearstatcache();
        } else {
            clearstatcache(true, $src_file);
        }

        if (!file_exists($src_file) || !filesize($src_file)) {
            return !($error = ImageManager::ERROR_FILE_NOT_EXIST);
        }

        list($src_width, $src_height, $type) = getimagesize($src_file);

        // If PS_IMAGE_QUALITY is activated, the generated image will be a PNG with .jpg as a file extension.
        // This allow for higher quality and for transparency. JPG source files will also benefit from a higher quality
        // because JPG reencoding by GD, even with max quality setting, degrades the image.
        if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all' ||
            (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $type == IMAGETYPE_PNG)
            && !$force_type
        ) {
            $file_type = 'png';
        }

        if (!$src_width) {
            return !($error = ImageManager::ERROR_FILE_WIDTH);
        }
        if (!$dst_width) {
            $dst_width = $src_width;
        }
        if (!$dst_height) {
            $dst_height = $src_height;
        }

        $src_image = ImageManager::create($type, $src_file);

        $width_diff = $dst_width / $src_width;
        $height_diff = $dst_height / $src_height;

        if ($width_diff > 1 && $height_diff > 1) {
            $next_width = $src_width;
            $next_height = $src_height;
        } else {
            if (Configuration::get('PS_IMAGE_GENERATION_METHOD') == 2 ||
                (!Configuration::get('PS_IMAGE_GENERATION_METHOD') && $width_diff > $height_diff)
            ) {
                $next_height = $dst_height;
                $next_width = round(($src_width * $next_height) / $src_height);
                $dst_width = (int) (!Configuration::get('PS_IMAGE_GENERATION_METHOD') ? $dst_width : $next_width);
            } else {
                $next_width = $dst_width;
                $next_height = round($src_height * $dst_width / $src_width);
                $dst_height = (int) (!Configuration::get('PS_IMAGE_GENERATION_METHOD') ? $dst_height : $next_height);
            }
        }

        if (!ImageManager::checkImageMemoryLimit($src_file)) {
            return !($error = ImageManager::ERROR_MEMORY_LIMIT);
        }

        $dest_image = imagecreatetruecolor($dst_width, $dst_height);

        // If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
        if ($file_type == 'png' && $type == IMAGETYPE_PNG) {
            imagealphablending($dest_image, false);
            imagesavealpha($dest_image, true);
            $transparent = imagecolorallocatealpha($dest_image, 255, 255, 255, 127);
            imagefilledrectangle($dest_image, 0, 0, $dst_width, $dst_height, $transparent);
        } else {
            $white = imagecolorallocate($dest_image, 255, 255, 255);
            imagefilledrectangle($dest_image, 0, 0, $dst_width, $dst_height, $white);
        }

        imagecopyresampled(
            $dest_image,
            $src_image,
            (int) (($dst_width - $next_width) / 2),
            (int) (($dst_height - $next_height) / 2),
            0,
            0,
            $next_width,
            $next_height,
            $src_width,
            $src_height
        );

        // return (ImageManager::write($file_type, $dest_image, $dst_file));

        $quality_lg = Configuration::get('LGIMAGEOPTIMIZE_COMPRESSION_RATE');
        $sharpen_lg = Configuration::get('LGIMAGEOPTIMIZE_SHARPEN_RATE');

        $intervention = new Intervention(array('driver' => 'gd'));

        // Creamos una imagen a partir de un archivo ya existente
        $image = $intervention->make($dest_image);

        if ($sharpen_lg) {
            // Ajustamos la nitidez de la imagen
            $image->sharpen($sharpen_lg);
        }

        $file_type_lg = Configuration::get('LGIMAGEOPTIMIZE_WEBP') ? 'webp' : $file_type;

        $write_file = $image->save($dst_file, $quality_lg, $file_type_lg);

        return $write_file;
    }


    /**
     * Resize, cut and optimize image
     *
     * @param string $src_file Image object from $_FILE
     * @param string $dst_file Destination filename
     * @param integer $dst_width Desired width (optional)
     * @param integer $dst_height Desired height (optional)
     * @param string $file_type
     *
     * @return boolean Operation result
     */
    public static function resizeLG16011(
        $src_file,
        $dst_file,
        $dst_width = null,
        $dst_height = null,
        $file_type = 'jpg',
        $force_type = false,
        &$error = 0
    )
    {
        if (PHP_VERSION_ID < 50300) {
            clearstatcache();
        } else {
            clearstatcache(true, $src_file);
        }

        if (!file_exists($src_file) || !filesize($src_file)) {
            return !($error = ImageManager::ERROR_FILE_NOT_EXIST);
        }

        list($tmp_width, $tmp_height, $type) = getimagesize($src_file);
        $src_image = ImageManager::create($type, $src_file);

        if (function_exists('exif_read_data') && function_exists('mb_strtolower')) {
            $exif = @exif_read_data($src_file);

            if ($exif && isset($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $src_width = $tmp_width;
                        $src_height = $tmp_height;
                        $src_image = imagerotate($src_image, 180, 0);
                        break;

                    case 6:
                        $src_width = $tmp_height;
                        $src_height = $tmp_width;
                        $src_image = imagerotate($src_image, -90, 0);
                        break;

                    case 8:
                        $src_width = $tmp_height;
                        $src_height = $tmp_width;
                        $src_image = imagerotate($src_image, 90, 0);
                        break;

                    default:
                        $src_width = $tmp_width;
                        $src_height = $tmp_height;
                }
            } else {
                $src_width = $tmp_width;
                $src_height = $tmp_height;
            }
        } else {
            $src_width = $tmp_width;
            $src_height = $tmp_height;
        }

        // If PS_IMAGE_QUALITY is activated, the generated image will be a PNG with .jpg as a file extension.
        // This allow for higher quality and for transparency. JPG source files will also benefit from a higher quality
        // because JPG reencoding by GD, even with max quality setting, degrades the image.
        if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all' ||
            (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $type == IMAGETYPE_PNG) &&
            !$force_type
        ) {
            $file_type = 'png';
        }

        if (!$src_width) {
            return !($error = ImageManager::ERROR_FILE_WIDTH);
        }
        if (!$dst_width) {
            $dst_width = $src_width;
        }
        if (!$dst_height) {
            $dst_height = $src_height;
        }

        $width_diff = $dst_width / $src_width;
        $height_diff = $dst_height / $src_height;

        if ($width_diff > 1 && $height_diff > 1) {
            $next_width = $src_width;
            $next_height = $src_height;
        } else {
            if (Configuration::get('PS_IMAGE_GENERATION_METHOD') == 2 ||
                (!Configuration::get('PS_IMAGE_GENERATION_METHOD') && $width_diff > $height_diff)
            ) {
                $next_height = $dst_height;
                $next_width = round(($src_width * $next_height) / $src_height);
                $dst_width = (int) (!Configuration::get('PS_IMAGE_GENERATION_METHOD') ? $dst_width : $next_width);
            } else {
                $next_width = $dst_width;
                $next_height = round($src_height * $dst_width / $src_width);
                $dst_height = (int) (!Configuration::get('PS_IMAGE_GENERATION_METHOD') ? $dst_height : $next_height);
            }
        }

        if (!ImageManager::checkImageMemoryLimit($src_file)) {
            return !($error = ImageManager::ERROR_MEMORY_LIMIT);
        }

        $dest_image = imagecreatetruecolor($dst_width, $dst_height);

        // If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
        if ($file_type == 'png' && $type == IMAGETYPE_PNG) {
            imagealphablending($dest_image, false);
            imagesavealpha($dest_image, true);
            $transparent = imagecolorallocatealpha($dest_image, 255, 255, 255, 127);
            imagefilledrectangle($dest_image, 0, 0, $dst_width, $dst_height, $transparent);
        } else {
            $white = imagecolorallocate($dest_image, 255, 255, 255);
            imagefilledrectangle($dest_image, 0, 0, $dst_width, $dst_height, $white);
        }

        imagecopyresampled(
            $dest_image,
            $src_image,
            (int) (($dst_width - $next_width) / 2),
            (int) (($dst_height - $next_height) / 2),
            0,
            0,
            $next_width,
            $next_height,
            $src_width,
            $src_height
        );

        // return (ImageManager::write($file_type, $dest_image, $dst_file));

        $quality_lg = Configuration::get('LGIMAGEOPTIMIZE_COMPRESSION_RATE');
        $sharpen_lg = Configuration::get('LGIMAGEOPTIMIZE_SHARPEN_RATE');

        $intervention = new Intervention(array('driver' => 'gd'));

        // Creamos una imagen a partir de un archivo ya existente
        $image = $intervention->make($dest_image);

        if ($sharpen_lg) {
            // Ajustamos la nitidez de la imagen
            $image->sharpen($sharpen_lg);
        }

        $file_type_lg = Configuration::get('LGIMAGEOPTIMIZE_WEBP') ? 'webp' : $file_type;

        $write_file = $image->save($dst_file, $quality_lg, $file_type_lg);

        return $write_file;
    }


    /**
     * Resize, cut and optimize image
     *
     * @param string $src_file Image object from $_FILE
     * @param string $dst_file Destination filename
     * @param int $dst_width Desired width (optional)
     * @param int $dst_height Desired height (optional)
     * @param string $file_type
     * @param bool $force_type
     * @param int $error
     * @param int $tgt_width
     * @param int $tgt_height
     * @param int $quality
     * @param int $src_width
     * @param int $src_height
     *
     * @return bool Operation result
     */
    public function resizeLG1610(
        $src_file,
        $dst_file,
        $dst_width = null,
        $dst_height = null,
        $file_type = 'jpg',
        $force_type = false,
        &$error = 0,
        &$tgt_width = null,
        &$tgt_height = null,
        $quality = 5,
        &$src_width = null,
        &$src_height = null
    )
    {
        if (PHP_VERSION_ID < 50300) {
            clearstatcache();
        } else {
            clearstatcache(true, $src_file);
        }

        if (!file_exists($src_file) || !filesize($src_file)) {
            return !($error = ImageManager::ERROR_FILE_NOT_EXIST);
        }

        $quality = 100;

        list($tmp_width, $tmp_height, $type) = getimagesize($src_file);
        $rotate = 0;
        if (function_exists('exif_read_data') && function_exists('mb_strtolower')) {
            $exif = @exif_read_data($src_file);

            if ($exif && isset($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $src_width = $tmp_width;
                        $src_height = $tmp_height;
                        $rotate = 180;
                        break;

                    case 6:
                        $src_width = $tmp_height;
                        $src_height = $tmp_width;
                        $rotate = -90;
                        break;

                    case 8:
                        $src_width = $tmp_height;
                        $src_height = $tmp_width;
                        $rotate = 90;
                        break;

                    default:
                        $src_width = $tmp_width;
                        $src_height = $tmp_height;
                }
            } else {
                $src_width = $tmp_width;
                $src_height = $tmp_height;
            }
        } else {
            $src_width = $tmp_width;
            $src_height = $tmp_height;
        }

        // If PS_IMAGE_QUALITY is activated, the generated image will be a PNG with .jpg as a file extension.
        // This allow for higher quality and for transparency. JPG source files will also benefit from a higher quality
        // because JPG reencoding by GD, even with max quality setting, degrades the image.
        if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all'
            || (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $type == IMAGETYPE_PNG) && !$force_type) {
            $file_type = 'png';
        }

        if (!$src_width) {
            return !($error = ImageManager::ERROR_FILE_WIDTH);
        }
        if (!$dst_width) {
            $dst_width = $src_width;
        }
        if (!$dst_height) {
            $dst_height = $src_height;
        }

        $width_diff = $dst_width / $src_width;
        $height_diff = $dst_height / $src_height;

        $ps_image_generation_method = Configuration::get('PS_IMAGE_GENERATION_METHOD');
        if ($width_diff > 1 && $height_diff > 1) {
            $next_width = $src_width;
            $next_height = $src_height;
        } else {
            if ($ps_image_generation_method == 2 || (!$ps_image_generation_method && $width_diff > $height_diff)) {
                $next_height = $dst_height;
                $next_width = round(($src_width * $next_height) / $src_height);
                $dst_width = (int) (!$ps_image_generation_method ? $dst_width : $next_width);
            } else {
                $next_width = $dst_width;
                $next_height = round($src_height * $dst_width / $src_width);
                $dst_height = (int) (!$ps_image_generation_method ? $dst_height : $next_height);
            }
        }

        if (!ImageManager::checkImageMemoryLimit($src_file)) {
            return !($error = ImageManager::ERROR_MEMORY_LIMIT);
        }

        $tgt_width = $dst_width;
        $tgt_height = $dst_height;

        $dest_image = imagecreatetruecolor($dst_width, $dst_height);

        // If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
        if ($file_type == 'png' && $type == IMAGETYPE_PNG) {
            imagealphablending($dest_image, false);
            imagesavealpha($dest_image, true);
            $transparent = imagecolorallocatealpha($dest_image, 255, 255, 255, 127);
            imagefilledrectangle($dest_image, 0, 0, $dst_width, $dst_height, $transparent);
        } else {
            $white = imagecolorallocate($dest_image, 255, 255, 255);
            imagefilledrectangle($dest_image, 0, 0, $dst_width, $dst_height, $white);
        }

        $src_image = ImageManager::create($type, $src_file);
        if ($rotate) {
            $src_image = imagerotate($src_image, $rotate, 0);
        }

        if ($dst_width >= $src_width && $dst_height >= $src_height) {
            imagecopyresized(
                $dest_image,
                $src_image,
                (int) (($dst_width - $next_width) / 2),
                (int) (($dst_height - $next_height) / 2),
                0,
                0,
                $next_width,
                $next_height,
                $src_width,
                $src_height
            );
        } else {
            ImageManager::imagecopyresampled(
                $dest_image,
                $src_image,
                (int) (($dst_width - $next_width) / 2),
                (int) (($dst_height - $next_height) / 2),
                0,
                0,
                $next_width,
                $next_height,
                $src_width,
                $src_height,
                $quality
            );
        }

        // $write_file = ImageManager::write($file_type, $dest_image, $dst_file);

        $quality_lg = Configuration::get('LGIMAGEOPTIMIZE_COMPRESSION_RATE');
        $sharpen_lg = Configuration::get('LGIMAGEOPTIMIZE_SHARPEN_RATE');

        $intervention = new Intervention(array('driver' => 'gd'));

        // Creamos una imagen a partir de un archivo ya existente
        $image = $intervention->make($dest_image);

        if ($sharpen_lg) {
            // Ajustamos la nitidez de la imagen
            $image->sharpen($sharpen_lg);
        }

        $file_type_lg = Configuration::get('LGIMAGEOPTIMIZE_WEBP') ? 'webp' : $file_type;

        $write_file = $image->save($dst_file, $quality_lg, $file_type_lg);

        @imagedestroy($src_image);

        return $write_file;
    }

    /**
     * Resize, cut and optimize image
     *
     * @param string $sourceFile Image object from $_FILE
     * @param string $destinationFile Destination filename
     * @param int $destinationWidth Desired width (optional)
     * @param int $destinationHeight Desired height (optional)
     * @param string $fileType Desired file_type (may be override by PS_IMAGE_QUALITY)
     * @param bool $forceType Don't override $file_type
     * @param int $error Out error code
     * @param int $targetWidth Needed by AdminImportController to speed up the import process
     * @param int $targetHeight Needed by AdminImportController to speed up the import process
     * @param int $quality Needed by AdminImportController to speed up the import process
     * @param int $sourceWidth Needed by AdminImportController to speed up the import process
     * @param int $sourceHeight Needed by AdminImportController to speed up the import process
     *
     * @return bool Operation result
     */
    public static function resizeLG1700(
        $sourceFile,
        $destinationFile,
        $destinationWidth = null,
        $destinationHeight = null,
        $fileType = 'jpg',
        $forceType = false,
        &$error = 0,
        &$targetWidth = null,
        &$targetHeight = null,
        $quality = 5,
        &$sourceWidth = null,
        &$sourceHeight = null
    )
    {
        clearstatcache(true, $sourceFile);

        if (!file_exists($sourceFile) || !filesize($sourceFile)) {
            return !($error = ImageManager::ERROR_FILE_NOT_EXIST);
        }

        $quality = 100;

        list($tmpWidth, $tmpHeight, $type) = getimagesize($sourceFile);
        $rotate = 0;
        if (function_exists('exif_read_data') && function_exists('mb_strtolower')) {
            $exif = @exif_read_data($sourceFile);

            if ($exif && isset($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $sourceWidth = $tmpWidth;
                        $sourceHeight = $tmpHeight;
                        $rotate = 180;

                        break;

                    case 6:
                        $sourceWidth = $tmpHeight;
                        $sourceHeight = $tmpWidth;
                        $rotate = -90;

                        break;

                    case 8:
                        $sourceWidth = $tmpHeight;
                        $sourceHeight = $tmpWidth;
                        $rotate = 90;

                        break;

                    default:
                        $sourceWidth = $tmpWidth;
                        $sourceHeight = $tmpHeight;
                }
            } else {
                $sourceWidth = $tmpWidth;
                $sourceHeight = $tmpHeight;
            }
        } else {
            $sourceWidth = $tmpWidth;
            $sourceHeight = $tmpHeight;
        }

        // If PS_IMAGE_QUALITY is activated, the generated image will be a PNG with .jpg as a file extension.
        // This allow for higher quality and for transparency. JPG source files will also benefit from a higher quality
        // because JPG reencoding by GD, even with max quality setting, degrades the image.
        if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all'
            || (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $type == IMAGETYPE_PNG) && !$forceType) {
            $fileType = 'png';
        }

        if (!$sourceWidth) {
            return !($error = ImageManager::ERROR_FILE_WIDTH);
        }
        if (!$destinationWidth) {
            $destinationWidth = $sourceWidth;
        }
        if (!$destinationHeight) {
            $destinationHeight = $sourceHeight;
        }

        $widthDiff = $destinationWidth / $sourceWidth;
        $heightDiff = $destinationHeight / $sourceHeight;

        $psImageGenerationMethod = Configuration::get('PS_IMAGE_GENERATION_METHOD');
        if ($widthDiff > 1 && $heightDiff > 1) {
            $nextWidth = $sourceWidth;
            $nextHeight = $sourceHeight;
        } else {
            if ($psImageGenerationMethod == 2 || (!$psImageGenerationMethod && $widthDiff > $heightDiff)) {
                $nextHeight = $destinationHeight;
                $nextWidth = round(($sourceWidth * $nextHeight) / $sourceHeight);
                $destinationWidth = (int) (!$psImageGenerationMethod ? $destinationWidth : $nextWidth);
            } else {
                $nextWidth = $destinationWidth;
                $nextHeight = round($sourceHeight * $destinationWidth / $sourceWidth);
                $destinationHeight = (int) (!$psImageGenerationMethod ? $destinationHeight : $nextHeight);
            }
        }

        if (!ImageManager::checkImageMemoryLimit($sourceFile)) {
            return !($error = ImageManager::ERROR_MEMORY_LIMIT);
        }

        $targetWidth = $destinationWidth;
        $targetHeight = $destinationHeight;

        $destImage = imagecreatetruecolor($destinationWidth, $destinationHeight);

        // If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
        if ($fileType == 'png' && $type == IMAGETYPE_PNG) {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $destinationWidth, $destinationHeight, $transparent);
        } else {
            $white = imagecolorallocate($destImage, 255, 255, 255);
            imagefilledrectangle($destImage, 0, 0, $destinationWidth, $destinationHeight, $white);
        }

        $srcImage = ImageManager::create($type, $sourceFile);
        if ($rotate) {
            $srcImage = imagerotate($srcImage, $rotate, 0);
        }

        if ($destinationWidth >= $sourceWidth && $destinationHeight >= $sourceHeight) {
            imagecopyresized(
                $destImage,
                $srcImage,
                (int) (($destinationWidth - $nextWidth) / 2),
                (int) (($destinationHeight - $nextHeight) / 2),
                0,
                0,
                $nextWidth,
                $nextHeight,
                $sourceWidth,
                $sourceHeight
            );
        } else {
            ImageManager::imagecopyresampled(
                $destImage,
                $srcImage,
                (int) (($destinationWidth - $nextWidth) / 2),
                (int) (($destinationHeight - $nextHeight) / 2),
                0,
                0,
                $nextWidth,
                $nextHeight,
                $sourceWidth,
                $sourceHeight,
                $quality
            );
        }

        // $writeFile = ImageManager::write($fileType, $destImage, $destinationFile);

        $quality_lg = Configuration::get('LGIMAGEOPTIMIZE_COMPRESSION_RATE');
        $sharpen_lg = Configuration::get('LGIMAGEOPTIMIZE_SHARPEN_RATE');

        $intervention = new Intervention(array('driver' => 'gd'));

        // Creamos una imagen a partir de un archivo ya existente
        $image = $intervention->make($destImage);

        if ($sharpen_lg) {
            // Ajustamos la nitidez de la imagen
            $image->sharpen($sharpen_lg);
        }

        $fileType_lg = Configuration::get('LGIMAGEOPTIMIZE_WEBP') ? 'webp' : $fileType;

        $writeFile = $image->save($destinationFile, $quality_lg, $fileType_lg);

        Hook::exec('actionOnImageResizeAfter', array('dst_file' => $destinationFile, 'file_type' => $fileType));
        @imagedestroy($srcImage);

        file_put_contents(
            dirname($destinationFile) . DIRECTORY_SEPARATOR . 'fileType',
            $fileType
        );

        return $writeFile;
    }

    /**
     * Resize, cut and optimize image.
     *
     * @param string $sourceFile Image object from $_FILE
     * @param string $destinationFile Destination filename
     * @param int $destinationWidth Desired width (optional)
     * @param int $destinationHeight Desired height (optional)
     * @param string $fileType Desired file_type (may be override by PS_IMAGE_QUALITY)
     * @param bool $forceType Don't override $file_type
     * @param int $error Out error code
     * @param int $targetWidth Needed by AdminImportController to speed up the import process
     * @param int $targetHeight Needed by AdminImportController to speed up the import process
     * @param int $quality Needed by AdminImportController to speed up the import process
     * @param int $sourceWidth Needed by AdminImportController to speed up the import process
     * @param int $sourceHeight Needed by AdminImportController to speed up the import process
     *
     * @return bool Operation result
     */
    public static function resizeLG1720(
        $sourceFile,
        $destinationFile,
        $destinationWidth = null,
        $destinationHeight = null,
        $fileType = 'jpg',
        $forceType = false,
        &$error = 0,
        &$targetWidth = null,
        &$targetHeight = null,
        $quality = 5,
        &$sourceWidth = null,
        &$sourceHeight = null
    )
    {
        clearstatcache(true, $sourceFile);

        if (!file_exists($sourceFile) || !filesize($sourceFile)) {
            return !($error = ImageManager::ERROR_FILE_NOT_EXIST);
        }

        $quality = 100;

        list($tmpWidth, $tmpHeight, $type) = getimagesize($sourceFile);
        $rotate = 0;
        if (function_exists('exif_read_data') && function_exists('mb_strtolower')) {
            $exif = @exif_read_data($sourceFile);

            if ($exif && isset($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $sourceWidth = $tmpWidth;
                        $sourceHeight = $tmpHeight;
                        $rotate = 180;

                        break;

                    case 6:
                        $sourceWidth = $tmpHeight;
                        $sourceHeight = $tmpWidth;
                        $rotate = -90;

                        break;

                    case 8:
                        $sourceWidth = $tmpHeight;
                        $sourceHeight = $tmpWidth;
                        $rotate = 90;

                        break;

                    default:
                        $sourceWidth = $tmpWidth;
                        $sourceHeight = $tmpHeight;
                }
            } else {
                $sourceWidth = $tmpWidth;
                $sourceHeight = $tmpHeight;
            }
        } else {
            $sourceWidth = $tmpWidth;
            $sourceHeight = $tmpHeight;
        }

        // If PS_IMAGE_QUALITY is activated, the generated image will be a PNG with .jpg as a file extension.
        // This allow for higher quality and for transparency. JPG source files will also benefit from a higher quality
        // because JPG reencoding by GD, even with max quality setting, degrades the image.
        if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all'
            || (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $type == IMAGETYPE_PNG) && !$forceType) {
            $fileType = 'png';
        }

        if (!$sourceWidth) {
            return !($error = ImageManager::ERROR_FILE_WIDTH);
        }
        if (!$destinationWidth) {
            $destinationWidth = $sourceWidth;
        }
        if (!$destinationHeight) {
            $destinationHeight = $sourceHeight;
        }

        $widthDiff = $destinationWidth / $sourceWidth;
        $heightDiff = $destinationHeight / $sourceHeight;

        $psImageGenerationMethod = Configuration::get('PS_IMAGE_GENERATION_METHOD');
        if ($widthDiff > 1 && $heightDiff > 1) {
            $nextWidth = $sourceWidth;
            $nextHeight = $sourceHeight;
        } else {
            if ($psImageGenerationMethod == 2 || (!$psImageGenerationMethod && $widthDiff > $heightDiff)) {
                $nextHeight = $destinationHeight;
                $nextWidth = round(($sourceWidth * $nextHeight) / $sourceHeight);
                $destinationWidth = (int) (!$psImageGenerationMethod ? $destinationWidth : $nextWidth);
            } else {
                $nextWidth = $destinationWidth;
                $nextHeight = round($sourceHeight * $destinationWidth / $sourceWidth);
                $destinationHeight = (int) (!$psImageGenerationMethod ? $destinationHeight : $nextHeight);
            }
        }

        if (!ImageManager::checkImageMemoryLimit($sourceFile)) {
            return !($error = ImageManager::ERROR_MEMORY_LIMIT);
        }

        $targetWidth = $destinationWidth;
        $targetHeight = $destinationHeight;

        $destImage = imagecreatetruecolor($destinationWidth, $destinationHeight);

        // If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
        if ($fileType == 'png' && $type == IMAGETYPE_PNG) {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $destinationWidth, $destinationHeight, $transparent);
        } else {
            $white = imagecolorallocate($destImage, 255, 255, 255);
            imagefilledrectangle($destImage, 0, 0, $destinationWidth, $destinationHeight, $white);
        }

        $srcImage = ImageManager::create($type, $sourceFile);
        if ($rotate) {
            $srcImage = imagerotate($srcImage, $rotate, 0);
        }

        if ($destinationWidth >= $sourceWidth && $destinationHeight >= $sourceHeight) {
            imagecopyresized(
                $destImage,
                $srcImage,
                (int) (($destinationWidth - $nextWidth) / 2),
                (int) (($destinationHeight - $nextHeight) / 2),
                0,
                0,
                $nextWidth,
                $nextHeight,
                $sourceWidth,
                $sourceHeight
            );
        } else {
            ImageManager::imagecopyresampled(
                $destImage,
                $srcImage,
                (int) (($destinationWidth - $nextWidth) / 2),
                (int) (($destinationHeight - $nextHeight) / 2),
                0,
                0,
                $nextWidth,
                $nextHeight,
                $sourceWidth,
                $sourceHeight,
                $quality
            );
        }

        // $writeFile = ImageManager::write($fileType, $destImage, $destinationFile);

        $quality_lg = Configuration::get('LGIMAGEOPTIMIZE_COMPRESSION_RATE');
        $sharpen_lg = Configuration::get('LGIMAGEOPTIMIZE_SHARPEN_RATE');

        $intervention = new Intervention(array('driver' => 'gd'));

        // Creamos una imagen a partir de un archivo ya existente
        $image = $intervention->make($destImage);

        if ($sharpen_lg) {
            // Ajustamos la nitidez de la imagen
            $image->sharpen($sharpen_lg);
        }

        $fileType_lg = Configuration::get('LGIMAGEOPTIMIZE_WEBP') ? 'webp' : $fileType;

        $writeFile = $image->save($destinationFile, $quality_lg, $fileType_lg);

        Hook::exec('actionOnImageResizeAfter', array('dst_file' => $destinationFile, 'file_type' => $fileType));
        @imagedestroy($srcImage);

        file_put_contents(
            dirname($destinationFile) . DIRECTORY_SEPARATOR . 'fileType',
            $fileType
        );

        return $writeFile;
    }
}
