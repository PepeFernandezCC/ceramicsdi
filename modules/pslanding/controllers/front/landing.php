<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
// IMPORTANT: legacy presenters (sin namespace)
require_once _PS_ROOT_DIR_.'/classes/ProductAssembler.php';
require_once _PS_ROOT_DIR_.'/classes/ProductPresenterFactory.php';

class PslandingLandingModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {

        parent::initContent();

        $slug = Tools::getValue('slug');

        if (!$slug) {
            return $this->set404();
        }


        $id_lang = (int) $this->context->language->id;
        $id_shop = (int) $this->context->shop->id;

        $landing = $this->getLandingBySlug($slug, $id_lang, $id_shop);

        if (!$landing || !(int) $landing['active']) {
            return $this->set404();
        }
        header('X-Robots-Tag: noindex, nofollow', true);
        $this->context->smarty->assign('nobots', true);
        $this->context->smarty->assign('meta_robots', 'noindex,nofollow');
        
        // Construir URLs para media subidos (guardamos solo filename en BD)
        $uploadBase = $this->module->getPathUri() . 'uploads/';

        // Hero
        $heroMedia = $this->buildMedia($landing, 'hero_media', $uploadBase, 'hero_media_mobile');
        $landing['hero_media_url']  = $heroMedia['url'];     
        $landing['hero_media_type'] = $heroMedia['type'];
        $landing['hasMobileMedia'] = $heroMedia['hasMobileMedia'];
        if ($heroMedia['hasMobileMedia']) {
            $landing['hero_media_mobile_url'] = $heroMedia['mobile_url'];
            $landing['hero_media_mobile_type'] = $heroMedia['mobile_type'];
        }


        // Hero2
        $hero2Media = $this->buildMedia($landing, 'hero2_media', $uploadBase);
        $landing['hero2_media_url']  = $hero2Media['url'];
        $landing['hero2_media_type'] = $hero2Media['type'];
        if ((int)$landing['hero2_product'] != 0 ) {
            $landing['hero2_product_url'] = $this->context->link->getProductLink((int)$landing['hero2_product']);
        }

        // Block2
        $block2Media = $this->buildMedia($landing, 'block2_image', $uploadBase);
        $landing['block2_media_url']  = $block2Media['url'];
        $landing['block2_media_type'] = $block2Media['type'];

        // Block3
        $block3Media = $this->buildMedia($landing, 'block3_image', $uploadBase);
        $landing['block3_media_url']  = $block3Media['url'];
        $landing['block3_media_type'] = $block3Media['type'];

        // Block4
        $block4Media = $this->buildMedia($landing, 'block4_image', $uploadBase);
        $landing['block4_media_url']  = $block4Media['url'];
        $landing['block4_media_type'] = $block4Media['type'];

        // Block5
        $block5Media = $this->buildMedia($landing, 'block5_image', $uploadBase);
        $landing['block5_media_url']  = $block5Media['url'];
        $landing['block5_media_type'] = $block5Media['type'];

        // Block6
        $block6Media = $this->buildMedia($landing, 'block6_image', $uploadBase);
        $landing['block6_media_url']  = $block6Media['url'];
        $landing['block6_media_type'] = $block6Media['type'];

        // Block7
        $block7Media = $this->buildMedia($landing, 'block7_image', $uploadBase);
        $landing['block7_media_url']  = $block7Media['url'];
        $landing['block7_media_type'] = $block7Media['type'];


        $characteristics = $this->getCharacteristics(
            (int)$landing['id_pslanding'],
            (int)$this->context->language->id
        );

        $slides = $this->getSlidesByLanding((int)$landing['id_pslanding']);

        foreach ($slides as &$s) {
            $s['image_url'] = !empty($s['image']) ? $uploadBase.$s['image'] : '';
            $s['product_url'] = !empty($s['id_product'])
                ? $this->context->link->getProductLink((int)$s['id_product'])
                : '#';
            $s['category_url'] = !empty($s['id_category'])
                ? $this->context->link->getCategoryLink((int)$s['id_category'])
                : '#';
        }
        unset($s);

        // Productos relacionados por colección (feature_value)
        $relatedProducts = [];
        if (!empty($landing['id_feature_value_collection'])) {
            $relatedProducts = $this->getRelatedProductsByFeatureValue((int)$landing['id_feature_value_collection']);
        }
        $landing['stones_category_url'] = $this->context->link->getCategoryLink((int)82);
        $this->context->smarty->assign([
            'landing' => $landing,
            'landing_slides' => $slides,
            'characteristics' => $characteristics,
            'meta_title' => $landing['title'],
            'related_products' => $relatedProducts,
        ]);
   
        $template = !empty($landing['template']) ? $landing['template'] : 'landing-default';
        $this->setTemplate('module:pslanding/views/templates/front/' . $template . '.tpl');
    }

    /**
     * Devuelve el tipo de media a partir del filename.
     * Importante: solo por extensión (no fiarse de MIME del cliente).
     */
    private function getMediaTypeFromFilename(?string $filename): string
    {
        if (empty($filename)) {
            return 'none';
        }

        $ext = Tools::strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Ajusta según lo que vayas a permitir en BO
        $videoExt = ['mp4', 'webm', 'ogg'];
        $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        if (in_array($ext, $videoExt, true)) {
            return 'video';
        }
        if (in_array($ext, $imageExt, true)) {
            return 'image';
        }

        // Si llega algo raro, mejor no renderizar nada
        return 'none';
    }

    private function buildMedia(array $landing, string $fieldName, string $uploadBase, string $fieldMobileName = ''): array
    {
        $hasMobileMedia = false;
        // $fieldName contiene el filename en BD
        $filename = $landing[$fieldName] ?? '';
        $type = $this->getMediaTypeFromFilename($filename);
        if ($fieldMobileName != '') {
            // $fieldName contiene el filename en BD
            $mobile_filename = $landing[$fieldMobileName] ?? '';
            $mobile_type = $this->getMediaTypeFromFilename($mobile_filename);
            $hasMobileMedia = true;
            return [
                'url'  => ($type !== 'none') ? ($uploadBase . $filename) : '',
                'type' => $type,
                'name' => $filename,
                'mobile_url'  => ($mobile_type !== 'none') ? ($uploadBase . $mobile_filename) : '',
                'mobile_type' => $mobile_type,
                'mobile_name' => $mobile_filename,
                'hasMobileMedia' => $hasMobileMedia
            ];
        }

        return [
            'url'  => ($type !== 'none') ? ($uploadBase . $filename) : '',
            'type' => $type,
            'name' => $filename,
            'hasMobileMedia' => $hasMobileMedia
        ];
    }


    protected function set404()
    {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        $this->setTemplate('errors/404.tpl');
    }

    protected function getLandingBySlug($slug, $id_lang, $id_shop)
    {
        // 1) Intento: slug + idioma actual + shop actual
        $sql = 'SELECT
                    l.id_pslanding, l.active, l.template, l.id_feature_value_collection,
                    l.hero_media, l.hero_media_mobile, l.hero2_media, l.hero2_product, 
                    l.block2_image, l.block3_image, l.block4_image, l.block5_image, l.block6_image, l.block7_image,
                    pl.title,
                    pl.external_url,
                    pl.hero_title, pl.hero_subtitle,
                    pl.hero2_button, pl.hero2_title,
                    pl.block2_title, pl.block2_text,
                    pl.block3_title, pl.block3_text,
                    pl.block4_title, pl.block4_text,
                    pl.block5_title, pl.block5_text,
                    pl.block6_title, pl.block6_text,
                    pl.block7_title, pl.block7_text,
                    pl.products_title, pl.products_subtitle
                FROM `'._DB_PREFIX_.'pslanding` l
                INNER JOIN `'._DB_PREFIX_.'pslanding_lang` pl
                    ON (l.id_pslanding = pl.id_pslanding)
                WHERE pl.slug = "'.pSQL($slug).'"
                  AND pl.id_lang = '.(int)$id_lang.'
                  AND pl.id_shop = '.(int)$id_shop;

        $row = Db::getInstance()->getRow($sql);
        if ($row) {
            return $row;
        }

        // 2) Fallback: cualquier idioma para el mismo shop
        $sql2 = 'SELECT
                    l.id_pslanding, l.active, l.template, l.id_feature_value_collection,
                    l.hero_media, l.hero_media_mobile, l.hero2_product, l.hero2_media,
                    l.block2_image, l.block3_image, l.block4_image, l.block5_image, l.block6_image, l.block7_image,
                    pl.title,
                    pl.external_url,
                    pl.hero_title, pl.hero_subtitle,
                    pl.hero2_title, pl.hero2_button,
                    pl.block2_title, pl.block2_text,
                    pl.block3_title, pl.block3_text,
                    pl.block4_title, pl.block4_text,
                    pl.block5_title, pl.block5_text,
                    pl.block6_title, pl.block6_text,
                    pl.block7_title, pl.block7_text,
                    pl.products_title, pl.products_subtitle
                 FROM `'._DB_PREFIX_.'pslanding` l
                 INNER JOIN `'._DB_PREFIX_.'pslanding_lang` pl
                    ON (l.id_pslanding = pl.id_pslanding)
                 WHERE pl.slug = "'.pSQL($slug).'"
                   AND pl.id_shop = '.(int)$id_shop;

        return Db::getInstance()->getRow($sql2);
    }

    protected function getCharacteristics($idLanding, $id_lang)
    {
        return Db::getInstance()->executeS('
            SELECT c.position, cl.title, cl.text
            FROM '._DB_PREFIX_.'pslanding_characteristic c
            INNER JOIN '._DB_PREFIX_.'pslanding_characteristic_lang cl
                ON (
                    cl.id_pslanding_characteristic = c.id_pslanding_characteristic
                    AND cl.id_lang = '.(int)$id_lang.'
                )
            WHERE c.id_pslanding = '.(int)$idLanding.'
            ORDER BY c.position ASC
        ');
    }

    protected function getSlidesByLanding($idLanding)
    {
        $id_lang = (int)$this->context->language->id;

        // idioma fallback por si no existe imagen en el idioma actual
        $id_lang_default = 1;

        return Db::getInstance()->executeS('
            SELECT 
                s.id_pslanding_slide, s.position, s.id_product, s.id_category, s.slot, s.active,
                COALESCE(sl.image, sld.image) AS image
            FROM '._DB_PREFIX_.'pslanding_slide s
            LEFT JOIN '._DB_PREFIX_.'pslanding_slide_lang sl
                ON (sl.id_pslanding_slide = s.id_pslanding_slide AND sl.id_lang='.(int)$id_lang.')
            LEFT JOIN '._DB_PREFIX_.'pslanding_slide_lang sld
                ON (sld.id_pslanding_slide = s.id_pslanding_slide AND sld.id_lang='.(int)$id_lang_default.')
            WHERE s.id_pslanding='.(int)$idLanding.' AND s.active=1
            ORDER BY s.position ASC, s.id_pslanding_slide ASC
        ');
    }
/*
    protected function getRelatedProductsByFeatureValue($id_feature_value)
    {
        $id_lang = (int)$this->context->language->id;

        $rows = Db::getInstance()->executeS('
            SELECT fp.id_product
            FROM '._DB_PREFIX_.'feature_product fp
            WHERE fp.id_feature_value = '.(int)$id_feature_value.'
        ');

        if (empty($rows)) {
            return [];
        }

        $assembler = new ProductAssembler($this->context);
        $factory = new ProductPresenterFactory($this->context);
        $presenter = $factory->getPresenter();
        $settings = $factory->getPresentationSettings();

        $products = [];
        foreach ($rows as $r) {
            $id_product = (int)$r['id_product'];
            if ($id_product <= 0) {
                continue;
            }

            // Ensamblado “listing-like”
            $assembled = $assembler->assembleProduct(['id_product' => $id_product]);

            // Presentación (esto genera cover.bySize..., prices, flags, etc.)
            $products[] = $presenter->present(
                $settings,
                $assembled,
                $this->context->language
            );
        }

        return $products;
    }
*/
    protected function getRelatedProductsByFeatureValue($id_feature_value)
    {
        $id_lang = (int) $this->context->language->id;
        $id_shop = (int) $this->context->shop->id;

        $rows = Db::getInstance()->executeS('
            SELECT DISTINCT fp.id_product
            FROM '._DB_PREFIX_.'feature_product fp
            INNER JOIN '._DB_PREFIX_.'product p
                ON p.id_product = fp.id_product
            INNER JOIN '._DB_PREFIX_.'product_shop ps
                ON ps.id_product = p.id_product AND ps.id_shop = '.$id_shop.'
            INNER JOIN '._DB_PREFIX_.'product_lang pl
                ON pl.id_product = p.id_product AND pl.id_shop = '.$id_shop.' AND pl.id_lang = '.$id_lang.'
            WHERE fp.id_feature_value = '.(int)$id_feature_value.'
            AND ps.active = 1
            AND ps.visibility IN ("both","catalog")
        ');

        if (empty($rows)) {
            return [];
        }

        $assembler = new ProductAssembler($this->context);
        $factory = new ProductPresenterFactory($this->context);
        $presenter = $factory->getPresenter();
        $settings = $factory->getPresentationSettings();

        $products = [];
        foreach ($rows as $r) {
            $id_product = (int) $r['id_product'];
            if ($id_product <= 0) {
                continue;
            }

            // Extra seguro: si algo está raro, saltamos el producto
            $productObj = new Product($id_product, false, $id_lang, $id_shop);
            if (!Validate::isLoadedObject($productObj)) {
                continue;
            }

            $assembled = $assembler->assembleProduct([
                'id_product' => $id_product,
                // opcional pero útil en productos con combinaciones:
                'id_product_attribute' => (int) $productObj->cache_default_attribute,
            ]);

            $products[] = $presenter->present($settings, $assembled, $this->context->language);
        }

        return $products;
    }



}
