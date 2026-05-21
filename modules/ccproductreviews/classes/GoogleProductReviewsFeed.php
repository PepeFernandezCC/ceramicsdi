<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class CcprGoogleProductReviewsFeed
{
    const CACHE_DIR = 'cache';
    const CACHE_FILE = 'google-product-reviews.xml';
    const CACHE_TTL = 3600;

    public static function getCachePath()
    {
        return _PS_MODULE_DIR_.'ccproductreviews/'.self::CACHE_DIR.'/'.self::CACHE_FILE;
    }

    public static function getFeedUrl(Context $context)
    {
        return $context->link->getModuleLink('ccproductreviews', 'googlefeed', [], true);
    }

    public static function shouldRegenerate()
    {
        $path = self::getCachePath();
        return !is_file($path) || (time() - (int)filemtime($path)) > self::CACHE_TTL;
    }

    public static function generate(Context $context, $force = false)
    {
        $path = self::getCachePath();

        if (!$force && is_file($path) && !self::shouldRegenerate()) {
            return [
                'ok' => true,
                'path' => $path,
                'url' => self::getFeedUrl($context),
                'count' => self::countApprovedReviews($context),
                'cached' => true,
            ];
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (!is_writable($dir)) {
            return [
                'ok' => false,
                'error' => 'Cache directory is not writable: '.$dir,
                'path' => $path,
                'url' => self::getFeedUrl($context),
                'count' => 0,
                'cached' => false,
            ];
        }

        $tmpPath = $path.'.tmp';
        $writer = new XMLWriter();
        if (!$writer->openURI($tmpPath)) {
            return [
                'ok' => false,
                'error' => 'Could not open temporary feed file: '.$tmpPath,
                'path' => $path,
                'url' => self::getFeedUrl($context),
                'count' => 0,
                'cached' => false,
            ];
        }

        $writer->startDocument('1.0', 'UTF-8');
        $writer->setIndent(true);
        $writer->setIndentString('  ');

        $writer->startElement('feed');
        $writer->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'http://www.google.com/shopping/reviews/schema/product/2.3/product_reviews.xsd');
        $writer->writeElement('version', '2.3');

        $writer->startElement('publisher');
        $writer->writeElement('name', self::cleanText(Configuration::get('PS_SHOP_NAME')));
        $favicon = self::getFaviconUrl($context);
        if ($favicon) {
            $writer->writeElement('favicon', $favicon);
        }
        $writer->endElement();

        $writer->startElement('reviews');

        $count = 0;
        $reviews = self::getApprovedReviews($context);
        foreach ($reviews as $review) {
            if (empty($review['product_url'])) {
                continue;
            }

            $writer->startElement('review');
            $writer->writeElement('review_id', 'ccpr-'.$review['id_review']);

            $writer->startElement('reviewer');
            $name = self::cleanText($review['customer_name']);
            if ($name !== '') {
                $writer->writeElement('name', $name);
            } else {
                $writer->writeElement('is_anonymous', 'true');
            }
            $writer->endElement();

            $writer->writeElement('review_timestamp', self::formatTimestamp($review['date_add']));

            $content = self::cleanText($review['comment']);
            if ($content === '') {
                $content = 'Valoracion del producto';
            }
            $writer->writeElement('content', $content);

            $writer->startElement('review_url');
            $writer->writeAttribute('type', 'group');
            $writer->text($review['product_url']);
            $writer->endElement();

            $writer->startElement('ratings');
            $writer->startElement('overall');
            $writer->writeAttribute('min', '1');
            $writer->writeAttribute('max', '5');
            $writer->text((string)max(1, min(5, (int)$review['rating'])));
            $writer->endElement();
            $writer->endElement();

            $writer->startElement('products');
            $writer->startElement('product');
            $writer->startElement('product_ids');

            if (!empty($review['gtin'])) {
                $writer->startElement('gtins');
                $writer->writeElement('gtin', self::cleanText($review['gtin']));
                $writer->endElement();
            }

            if (!empty($review['mpn']) && !empty($review['brand'])) {
                $writer->startElement('mpns');
                $writer->writeElement('mpn', self::cleanText($review['mpn']));
                $writer->endElement();
            }

            if (!empty($review['sku'])) {
                $writer->startElement('skus');
                $writer->writeElement('sku', self::cleanText($review['sku']));
                $writer->endElement();
            }

            if (!empty($review['brand'])) {
                $writer->startElement('brands');
                $writer->writeElement('brand', self::cleanText($review['brand']));
                $writer->endElement();
            }

            $writer->endElement();

            if (!empty($review['product_name'])) {
                $writer->writeElement('product_name', self::cleanText($review['product_name']));
            }
            $writer->writeElement('product_url', $review['product_url']);

            $writer->endElement();
            $writer->endElement();

            $writer->writeElement('is_spam', 'false');
            $writer->writeElement('collection_method', 'post_fulfillment');

            $writer->endElement();
            $count++;
        }

        $writer->endElement();
        $writer->endElement();
        $writer->endDocument();
        $writer->flush();

        @chmod($tmpPath, 0644);
        if (!@rename($tmpPath, $path)) {
            @unlink($tmpPath);
            return [
                'ok' => false,
                'error' => 'Could not move temporary feed into place.',
                'path' => $path,
                'url' => self::getFeedUrl($context),
                'count' => $count,
                'cached' => false,
            ];
        }

        return [
            'ok' => true,
            'path' => $path,
            'url' => self::getFeedUrl($context),
            'count' => $count,
            'cached' => false,
        ];
    }

    public static function output(Context $context)
    {
        $result = self::generate($context, false);
        if (!$result['ok']) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: text/plain; charset=utf-8');
            die($result['error']);
        }

        header('Content-Type: application/xml; charset=utf-8');
        header('Cache-Control: public, max-age='.self::CACHE_TTL);
        header('X-Robots-Tag: noindex');
        readfile($result['path']);
        exit;
    }

    private static function getApprovedReviews(Context $context)
    {
        $idLang = (int)Configuration::get('PS_LANG_DEFAULT');
        if ($context->language && (int)$context->language->id > 0) {
            $idLang = (int)$context->language->id;
        }
        $idShop = (int)$context->shop->id;

        $productColumns = self::getTableColumns('product');
        $hasEan13 = in_array('ean13', $productColumns);
        $hasMpn = in_array('mpn', $productColumns);
        $hasReference = in_array('reference', $productColumns);
        $hasIdManufacturer = in_array('id_manufacturer', $productColumns);

        $select = [
            'r.id_review',
            'r.id_product',
            'r.id_customer',
            'r.customer_name',
            'r.rating',
            'r.comment',
            'r.date_add',
            'pl.name AS product_name',
        ];

        $select[] = $hasEan13 ? 'p.ean13 AS gtin' : "'' AS gtin";
        $select[] = $hasMpn ? 'p.mpn AS mpn' : "'' AS mpn";
        $select[] = $hasReference ? 'p.reference AS sku' : "'' AS sku";
        $select[] = $hasIdManufacturer ? 'm.name AS brand' : "'' AS brand";

        $joinManufacturer = $hasIdManufacturer ? 'LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.id_manufacturer = p.id_manufacturer)' : '';

        $sql = '
            SELECT '.implode(', ', $select).'
            FROM `'._DB_PREFIX_.'product_review` r
            INNER JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = r.id_product)
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (pl.id_product = r.id_product AND pl.id_lang = '.(int)$idLang.' AND pl.id_shop = '.(int)$idShop.')
            '.$joinManufacturer.'
            WHERE r.active = 1
            ORDER BY r.date_add DESC, r.id_review DESC
        ';

        $rows = Db::getInstance()->executeS($sql);
        if (!is_array($rows)) {
            return [];
        }

        foreach ($rows as &$row) {
            $row['product_url'] = $context->link->getProductLink((int)$row['id_product'], null, null, null, $idLang, $idShop);
            $row['gtin'] = self::cleanIdentifier($row['gtin']);
            $row['mpn'] = self::cleanIdentifier($row['mpn']);
            $row['sku'] = self::cleanIdentifier($row['sku']);
            $row['brand'] = self::cleanText($row['brand']);
        }

        return $rows;
    }

    private static function countApprovedReviews(Context $context)
    {
        return (int)Db::getInstance()->getValue('
            SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'product_review`
            WHERE active = 1
        ');
    }

    private static function getTableColumns($table)
    {
        static $columns = [];
        if (isset($columns[$table])) {
            return $columns[$table];
        }

        $rows = Db::getInstance()->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.pSQL($table).'`');
        $columns[$table] = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $columns[$table][] = $row['Field'];
            }
        }

        return $columns[$table];
    }

    private static function cleanIdentifier($value)
    {
        return trim(self::cleanText($value));
    }

    private static function cleanText($value)
    {
        $value = (string)$value;
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = strip_tags($value);
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value);
        return trim($value);
    }

    private static function formatTimestamp($date)
    {
        $timezone = Configuration::get('PS_TIMEZONE');
        if (!$timezone) {
            $timezone = @date_default_timezone_get();
        }
        if (!$timezone) {
            $timezone = 'Europe/Madrid';
        }

        try {
            $dt = new DateTime((string)$date, new DateTimeZone($timezone));
        } catch (Exception $e) {
            $dt = new DateTime('now', new DateTimeZone($timezone));
        }

        return $dt->format(DateTime::ATOM);
    }

    private static function getFaviconUrl(Context $context)
    {
        $favicon = Configuration::get('PS_FAVICON');
        if (!$favicon) {
            return '';
        }
        return $context->link->getMediaLink(_PS_IMG_.$favicon);
    }
}
