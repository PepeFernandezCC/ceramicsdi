<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class PsLandingModel extends ObjectModel
{
    public $id_pslanding;
    public $active;
    public $template;
    public $id_feature_value_collection;

    public $date_add;
    public $date_upd;

    public $title;
    public $slug;
    public $external_url;
    public $hero_title;
    public $hero_subtitle;
    public $hero_media;
    public $hero_media_mobile;

    public $block2_title;
    public $block2_text;
    public $block2_image;
    public $products_title;
    public $products_subtitle;

    public $hero2_title;
    public $hero2_button;
    public $hero2_media;
    public $hero2_product;

    public $block3_title;
    public $block3_text;
    public $block3_image;

    public $block4_title;
    public $block4_text;
    public $block4_image;

    public $block5_title;
    public $block5_text;
    public $block5_image;

    public $block6_title;
    public $block6_text;
    public $block6_image;

    public $block7_title;
    public $block7_text;
    public $block7_image;


    public static $definition = [
        'table' => 'pslanding',
        'primary' => 'id_pslanding',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'template' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64],
            'id_feature_value_collection' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],

            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'validate' => 'isGenericName', 'size' => 255],
            'slug' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'validate' => 'isLinkRewrite', 'size' => 255],

            'external_url' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrl', 'size' => 255],

            'hero_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'hero_subtitle' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'hero_media'   => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],
            'hero_media_mobile'   => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],
    
            'hero2_button' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'hero2_title' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'hero2_media'   => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],
            'hero2_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],

            'block2_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'block2_text' => ['type' => self::TYPE_HTML,    'lang' => true, 'validate' => 'isCleanHtml'],
            'block2_image' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],

            'block3_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'block3_text'  => ['type' => self::TYPE_HTML,   'lang' => true, 'validate' => 'isCleanHtml'],
            'block3_image' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],

            'block4_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'block4_text'  => ['type' => self::TYPE_HTML,   'lang' => true, 'validate' => 'isCleanHtml'],
            'block4_image' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],

            'block5_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'block5_text' => ['type' => self::TYPE_HTML,    'lang' => true, 'validate' => 'isCleanHtml'],
            'block5_image' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],

            'block6_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'block6_text'  => ['type' => self::TYPE_HTML,   'lang' => true, 'validate' => 'isCleanHtml'],
            'block6_image' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],

            'block7_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'block7_text'  => ['type' => self::TYPE_HTML,   'lang' => true, 'validate' => 'isCleanHtml'],
            'block7_image' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],


            'products_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'products_subtitle' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];
}
