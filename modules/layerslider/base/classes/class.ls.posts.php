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

class LsPosts
{
    // Stores the last query results
    public $post;
    public $posts;
    public $args;

    /**
     * Returns posts that matches the query params.
     *
     * @param array $args Array of WP_Query attributes
     *
     * @return self
     */
    public static function find($args = [])
    {
        // Crate new instance
        $instance = new self();
        $instance->args = $args;
        if ($instance->posts = ls_get_posts($args)) {
            $instance->post = $instance->posts[0];
        }

        return $instance;
    }

    public static function getPostTypes()
    {
        // Get post types
        $postTypes = ls_get_post_types();

        // Remove some defalt post types
        if (isset($postTypes['revision'])) {
            unset($postTypes['revision']);
        }
        if (isset($postTypes['nav_menu_item'])) {
            unset($postTypes['nav_menu_item']);
        }

        // Convert names to plural
        foreach ($postTypes as $key => $item) {
            if (!empty($item)) {
                $postTypes[$key] = [];
                $postTypes[$key]['slug'] = $item;
                $postTypes[$key]['obj'] = ls_get_post_type_object($item);
                $postTypes[$key]['name'] = $postTypes[$key]['obj']->labels->name;
            }
        }

        return $postTypes;
    }

    public function getParsedObject()
    {
        if (!$this->posts) {
            return [];
        }
        $context = $GLOBALS['context'];
        if (version_compare(_PS_VERSION_, '1.7.6', '<')) {
            $formatPrice = ['Tools', 'displayPrice'];
            $currency = $context->currency;
        } else {
            $formatPrice = [Tools::getContextLocale($context), 'formatPrice'];
            $currency = $context->currency->iso_code;
        }
        $link = $context->link;
        $small = ImageType::{method_exists('ImageType', 'getFormattedName') ? 'getFormattedName' : 'getFormatedName'}('small');
        $ret = [];
        foreach ($this->posts as $key => $val) {
            $ret[$key] = [];
            $ret[$key]['id'] = $val['id_product'];
            $ret[$key]['url'] = $link->getProductLink($val['id_product'], $val['link_rewrite']);
            $ret[$key]['date-published'] = $val['date_add'];
            $ret[$key]['date-modified'] = $val['date_upd'];
            if ($image = Image::getCover($val['id_product'])) {
                $ret[$key]['thumbnail'] = $link->getImageLink($val['link_rewrite'], $image['id_image'], $small);
                $ret[$key]['image-url'] = $link->getImageLink($val['link_rewrite'], $image['id_image'], $this->args['img_size']);

                if (empty($ret[$key]['thumbnail'])) {
                    $ret[$key]['thumbnail'] = $ret[$key]['image-url'];
                }
                $ret[$key]['image'] = '<img src="' . $ret[$key]['image-url'] . '" alt="">';
            }
            $ret[$key]['price'] = $formatPrice(Product::getPriceStatic($val['id_product']), $currency);
            $ret[$key]['old-price'] = $formatPrice(Product::getPriceStatic($val['id_product'], true, null, 6, null, false, false), $currency);
            if ($ret[$key]['price'] === $ret[$key]['old-price']) {
                $ret[$key]['old-price'] = '';
            }
            $ret[$key]['name'] = $val['name'];
            $ret[$key]['title'] = $ret[$key]['name'] . ' ' . $ret[$key]['price'];
            $ret[$key]['description'] = strip_tags($val['description']);
            $ret[$key]['description-short'] = strip_tags($val['description_short']);
            $ret[$key]['author'] = $val['manufacturer'];
            $ret[$key]['manufacturer'] = $val['manufacturer'];

            $catlinks = [];
            $cats = Product::getProductCategoriesFull($val['id_product'], $context->language->id);
            foreach ($cats as &$cat) {
                array_unshift($catlinks, '<a href="' . $link->getCategoryLink($cat['id_category'], $cat['link_rewrite']) . '">' . $cat['name'] . '</a>');
            }
            $ret[$key]['breadcrumbs'] = '<div>' . implode(' / ', $catlinks) . '</div>';
            $ret[$key]['category'] = array_pop($catlinks);

            // $taglinks = array();
            // $tags = Tag::getProductTags($val['id_product']);
            // foreach ($tags[$context->language->id] as $tag) {
            //     $taglinks[] = '['.$tag.']';
            // }
            // $ret[$key]['tags'] = implode(' ', $taglinks);
        }

        return $ret;
    }

    public function getWithFormat($str, $textlength = 0)
    {
        if (!is_array($this->post)) {
            return $str;
        }
        $context = $GLOBALS['context'];

        // Post ID
        if (false !== strpos($str, '[id]')) {
            $str = str_replace('[id]', $this->post['id_product'], $str);
        }
        // Post URL
        if (false !== strpos($str, '[url]')) {
            $url = $context->link->getProductLink($this->post['id_product'], $this->post['link_rewrite']);
            $str = str_replace('[url]', $url, $str);
        }
        // Date published
        if (false !== strpos($str, '[date-published]')) {
            $str = str_replace('[date-published]', date(ls_get_option('date_format'), strtotime($this->post['date_add'])), $str);
        }
        // Date modified
        if (false !== strpos($str, '[date-modified]')) {
            $str = str_replace('[date-modified]', date(ls_get_option('date_format'), strtotime($this->post['date_upd'])), $str);
        }
        // Featured image
        if (false !== strpos($str, '[image]')) {
            $cover = Image::getCover($this->post['id_product']);
            $image = $context->link->getImageLink($this->post['link_rewrite'], $cover['id_image'], $this->args['img_size']);
            if (!empty($image)) {
                $str = str_replace('[image]', '<img src="' . $image . '" alt="' . $this->post['name'] . '">', $str);
            }
        }
        // Featured image URL
        if (false !== strpos($str, '[image-url]')) {
            $cover = Image::getCover($this->post['id_product']);
            $image = $context->link->getImageLink($this->post['link_rewrite'], $cover['id_image'], $this->args['img_size']);
            if (!empty($image)) {
                $str = str_replace('[image-url]', $image, $str);
            }
        }
        // Name
        if (false !== strpos($str, '[name]')) {
            $str = str_replace('[name]', $this->getTitle($textlength), $str);
        }
        // Price & old price
        $priceTag = false !== strpos($str, '[price]');
        $oldPriceTag = false !== strpos($str, '[old-price]');

        if ($priceTag || $oldPriceTag) {
            if (version_compare(_PS_VERSION_, '1.7.6', '<')) {
                $formatPrice = ['Tools', 'displayPrice'];
                $currency = $context->currency;
            } else {
                $formatPrice = [Tools::getContextLocale($context), 'formatPrice'];
                $currency = $context->currency->iso_code;
            }
            $price = $formatPrice(Product::getPriceStatic($this->post['id_product']), $currency);

            if ($priceTag) {
                $str = str_replace('[price]', $price, $str);
            }
            if ($oldPriceTag) {
                $oldPrice = $formatPrice(Product::getPriceStatic($this->post['id_product'], true, null, 6, null, false, false), $currency);

                if ($price === $oldPrice) {
                    $oldPrice = '';
                }
                $str = str_replace('[old-price]', $oldPrice, $str);
            }
        }
        // Description
        if (false !== strpos($str, '[description]')) {
            $str = str_replace('[description]', $this->getDescription($textlength), $str);
        }
        // Description short
        if (false !== strpos($str, '[description-short]')) {
            $str = str_replace('[description-short]', $this->getDescriptionShort($textlength), $str);
        }
        // Manufacturer
        if (false !== strpos($str, '[manufacturer]')) {
            $str = str_replace('[manufacturer]', $this->post['manufacturer'], $str);
        }
        // Category
        if (false !== strpos($str, '[category]')) {
            $str = str_replace('[category]', $this->getCategory(), $str);
        }
        // Category list
        if (false !== strpos($str, '[breadcrumbs]')) {
            $str = str_replace('[breadcrumbs]', $this->getCategoryList(), $str);
        }
        // Tags list
        // if (strpos($str, '[tags]') !== false) {
        //     $str = str_replace('[tags]', $this->getTagList(), $str);
        // }

        return $str;
    }

    /**
     * Returns the lastly selected post's title.
     *
     * @return string The title of the post
     */
    public function getTitle($length = 0)
    {
        if (!is_array($this->post)) {
            return '';
        }

        $title = $this->post['name'];
        if (!empty($length)) {
            $title = Tools::substr($title, 0, $length);
        }

        return $title;
    }

    public function getCategory($post = null)
    {
        if (!$post) {
            $post = $this->post;
        }

        if ($cats = Product::getProductCategoriesFull($this->post['id_product'], $GLOBALS['language']->id)) {
            $cat = array_pop($cats);

            return '<a href="' . $GLOBALS['context']->link->getCategoryLink($cat['id_category'], $cat['link_rewrite']) . '">' . $cat['name'] . '</a>';
        }

        return '';
    }

    public function getCategoryList($post = null)
    {
        if (!$post) {
            $post = $this->post;
        }

        $link = $GLOBALS['context']->link;

        if ($cats = Product::getProductCategoriesFull($this->post['id_product'], $GLOBALS['language']->id)) {
            $list = [];
            foreach ($cats as &$cat) {
                $list[] = '<a href="' . $link->getCategoryLink($cat['id_category'], $cat['link_rewrite']) . '">' . $cat['name'] . '</a>';
            }

            return '<div>' . implode(' / ', $list) . '</div>';
        }

        return '';
    }

    /*
    public function getTagList($post = null)
    {

        if (!empty($post)) {
            $post = $this->post;
        }

        if (has_tag(false, $this->post->ID)) {
            $tags = wp_get_post_tags($this->post->ID);
            $list = array();
            foreach ($tags as $val) {
                $list[] = '<a href="/tag/'.$val->slug.'/">'.$val->name.'</a>';
            }
            return '<div>'.implode(', ', $list).'</div>';
        } else {
            return '';
        }
    }
    */

    /**
     * Returns a subset of the post's content,
     * or the first paragraph if isn't specified.
     *
     * @param int $length The subset's length
     *
     * @return string The content
     */
    public function getDescription($length = 0)
    {
        if (!is_array($this->post)) {
            return '';
        }

        $content = strip_tags($this->post['description']);

        return $length ? Tools::substr($content, 0, $length) : $content;
    }

    public function getDescriptionShort($length = 0)
    {
        if (!is_array($this->post)) {
            return '';
        }

        $content = strip_tags(ls__($this->post['description_short']));

        return $length ? Tools::substr($content, 0, $length) : $content;
    }
}
