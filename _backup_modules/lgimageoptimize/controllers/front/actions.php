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

/**
 * @since 1.5.0
 */
require _PS_MODULE_DIR_ . 'lgimageoptimize/vendor/autoload.php';

use Intervention\Image\ImageManager as Intervention;

define('URL_TEST', _MODULE_DIR_ . 'lgimageoptimize' . DIRECTORY_SEPARATOR . 'demotest/demo_[TYPE]_[SIZE].jpg');
define('DIR_TEST', _PS_MODULE_DIR_ . 'lgimageoptimize' . DIRECTORY_SEPARATOR . 'demotest/demo_[TYPE]_[SIZE].jpg');

class LgImageOptimizeActionsModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (Tools::getValue('action', 0) == 'productSearch' && Tools::getIsset('ajax')) {
            $out = $this->ajaxProcessSearchProducts();
            echo json_encode($out);
            exit;
        }
        if (Tools::getValue('action', 0) == 'selectProduct' && Tools::getIsset('ajax')) {
            $out = $this->ajaxProcessSelectProduct();
            echo json_encode($out);
            exit;
        }
        if (Tools::getValue('action', 0) == 'drawImageZona' && Tools::getIsset('ajax')) {
            $out = $this->ajaxProcessDrawImages();
            echo json_encode($out);
            exit;
        }
    }

    public function ajaxProcessSearchProducts()
    {
        $id_lang = Context::getContext()->language->id;
        if ($products = Product::searchByName(
            (int) $this->context->language->id,
            pSQL(Tools::getValue('searchProduct'))
        )) {
            foreach ($products as &$product) {
                $productObj = new Product($product['id_product'], true, $id_lang);
                $product['images'] = $productObj->getImages($id_lang);
                $product['html'] = $this->showTemplateLine($product);
            }

            $to_return = array(
                'products' => $products,
                'found' => true,
                'message' => $this->module->l('Productos localizados'),
            );
        } else {
            $to_return = array(
                'found' => false,
                'message' => $this->module->l('Productos no localizados'),
            );
        }

        Configuration::updateValue('LGIMAGEOPTIMIZE_PRODUCT_SEARCH', pSQL(Tools::getValue('searchProduct', '')));
        Configuration::updateValue('LGIMAGEOPTIMIZE_PRODUCT_RESULT', json_encode($to_return));

        return $to_return;
    }

    public function ajaxProcessSelectProduct()
    {
        $id_lang = Context::getContext()->language->id;
        $id_product = (int) Tools::getValue('selectProduct');
        Configuration::updateValue('LGIMAGEOPTIMIZE_PRODUCT_SELECTED', $id_product);
        Configuration::updateValue('LGIMAGEOPTIMIZE_TYPE_IMAGE', Tools::getValue('typeImage', ''));

        $productObj = new Product($id_product, false, $id_lang);
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

        $to_return = array(
            'found' => true,
            'html' => $this->showTemplateProduct($productObj, $url_image),
        );

        return $to_return;
    }

    public function ajaxProcessDrawImages()
    {
        $intervention = new Intervention(array('driver' => 'gd'));
        $type_images = ImageTypeCore::getImagesTypes('products');

        $id_product = (int) Configuration::get('LGIMAGEOPTIMIZE_PRODUCT_SELECTED');
        $cover = Product::getCover($id_product);
        $id_image = (int) $cover['id_image'];
        $image = new Image($id_image);

        $images = array();
        $path = _PS_IMG_DIR_ . 'p' . DIRECTORY_SEPARATOR . $image->getExistingImgPath() . '.jpg';

        if (Configuration::get('LGIMAGEOPTIMIZE_ADVANCED')) {

            foreach ($type_images as $image_type) {
                $newpathOriginal = str_replace('[SIZE]', $image_type['name'], DIR_TEST);
                $newpathOriginal = str_replace('[TYPE]', 'original', $newpathOriginal);

                $newurlOriginal = str_replace('[SIZE]', $image_type['name'], URL_TEST);
                $newurlOriginal = str_replace('[TYPE]', 'original', $newurlOriginal);

                ImageManagerCore::resize(
                    $path,
                    $newpathOriginal,
                    $image_type['width'],
                    $image_type['height']
                );

                $imageIntOrig = $intervention->make($newpathOriginal);
                $image_original = array();
                $image_original['url'] = $newurlOriginal . '?'.rand();
                $image_original['size'] = $imageIntOrig->getWidth() . 'x' . $imageIntOrig->getHeight() . ' px';
                $image_original['weight'] = round(($imageIntOrig->filesize() / 1024), 2) . ' kb.';

                $newpathProcessed = str_replace('[SIZE]', $image_type['name'], DIR_TEST);
                $newpathProcessed = str_replace('[TYPE]', 'processed', $newpathProcessed);

                $newurlProcessed = str_replace('[SIZE]', $image_type['name'], URL_TEST);
                $newurlProcessed = str_replace('[TYPE]', 'processed', $newurlProcessed);

                ImageManager::resizeLg(
                    $path,
                    $newpathProcessed,
                    $image_type['width'],
                    $image_type['height'],
                );


                $imageIntProccessed = $intervention->make($newpathProcessed);
                $image_optimized = array();
                $image_optimized['url'] = $newurlProcessed . '?'.rand();
                $image_optimized['size'] = $imageIntProccessed->getWidth() . 'x' .
                    $imageIntProccessed->getHeight() . ' px';
                $image_optimized['weight'] = round(($imageIntProccessed->filesize() / 1024), 2) . ' kb.';

                $imageArray = array();
                $imageArray['original'] = $image_original;
                $imageArray['optimized'] = $image_optimized;

                $images[$image_type['name']] = $imageArray;
            }
        } else {
            foreach ($type_images as $image_type) {
                $newpathOriginal = str_replace('[SIZE]', $image_type['name'], DIR_TEST);
                $newpathOriginal = str_replace('[TYPE]', 'original', $newpathOriginal);

                $newurlOriginal = str_replace('[SIZE]', $image_type['name'], URL_TEST);
                $newurlOriginal = str_replace('[TYPE]', 'original', $newurlOriginal);

                ImageManagerCore::resize(
                    $path,
                    $newpathOriginal,
                    $image_type['width'],
                    $image_type['height']
                );

                $imageIntOrig = $intervention->make($newpathOriginal);
                $image_original = array();
                $image_original['url'] = $newurlOriginal . '?'.rand();
                $image_original['size'] = $imageIntOrig->getWidth() . 'x' . $imageIntOrig->getHeight() . ' px';
                $image_original['weight'] = round(($imageIntOrig->filesize() / 1024), 2) . ' kb.';

                // Creamos una imagen a partir de un archivo ya existente
                $imageInt = $intervention->make($newpathOriginal);

                $quality = (int) Configuration::get('LGIMAGEOPTIMIZE_COMPRESSION_RATE');
                $sharpen = (int) Configuration::get('LGIMAGEOPTIMIZE_SHARPEN_RATE');

                if (Configuration::get('LGIMAGEOPTIMIZE_WEBP')) {
                    $file_type = 'webp';
                    //                $imageInt->encode('webp', $quality);
                } else {
                    $file_type = null;
                }

                if ($sharpen) {
                    // Mejoramos la imagen
                    $imageInt->sharpen($sharpen);
                }

                $newpathProcessed = str_replace('[SIZE]', $image_type['name'], DIR_TEST);
                $newpathProcessed = str_replace('[TYPE]', 'processed', $newpathProcessed);

                $newurlProcessed = str_replace('[SIZE]', $image_type['name'], URL_TEST);
                $newurlProcessed = str_replace('[TYPE]', 'processed', $newurlProcessed);

                $imageInt->save($newpathProcessed, $quality, $file_type);

                $image_optimized = array();
                $image_optimized['url'] = str_replace('[TYPE]', $image_type['name'], $newurlProcessed);
                $image_optimized['size'] = $imageInt->getWidth() . 'x' . $imageInt->getHeight() . ' px';
                $image_optimized['weight'] = round(($imageInt->filesize() / 1024), 2) . ' kb.';

                $imageArray = array();
                $imageArray['original'] = $image_original;
                $imageArray['optimized'] = $image_optimized;

                $images[$image_type['name']] = $imageArray;
            }
        }

        $to_return = array(
            'found' => true,
            'html' => $this->showTemplateDemo($images),
            'message' => $this->module->l('Imágenes generadas'),
        );

        return $to_return;
    }

    public function showTemplateLine($product)
    {
        Context::getContext()->smarty->assign('product', $product);
        $line_tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/product_list.tpl';
        return Context::getContext()->smarty->fetch($line_tpl);
    }

    public function showTemplateProduct($productObj, $url_image)
    {
        Context::getContext()->smarty->assign('product_select', $productObj);
        Context::getContext()->smarty->assign('url_image', $url_image);
        $product_tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/product_select.tpl';
        return Context::getContext()->smarty->fetch($product_tpl);
    }

    public function showTemplateDemo($images)
    {
        Context::getContext()->smarty->assign('images', $images);
        $demo_tpl = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/demo_zone.tpl';
        return Context::getContext()->smarty->fetch($demo_tpl);
    }
}
