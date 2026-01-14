<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class PsLandingModel extends ObjectModel
{
    public $id_pslanding;
    public $active;
    public $template;
    public $id_category;
    public $id_feature_value_collection;

    public $date_add;
    public $date_upd;

    public $title;
    public $slug;
    public $hero_title;
    public $hero_subtitle;
    public $hero_media;
    public $block2_title;
    public $block2_text;
    public $block2_image;
    public $products_title;
    public $products_subtitle;

    public $block3_title;
    public $block3_text;
    public $block3_image;

    public $block4_title;
    public $block4_text;
    public $block4_image;


    public static $definition = [
        'table' => 'pslanding',
        'primary' => 'id_pslanding',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'template' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64],
            'id_category' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_feature_value_collection' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],

            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'validate' => 'isGenericName', 'size' => 255],
            'slug' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'validate' => 'isLinkRewrite', 'size' => 255],

            'hero_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'hero_subtitle' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'hero_media'   => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],

            'block2_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'block2_text' => ['type' => self::TYPE_HTML,    'lang' => true, 'validate' => 'isCleanHtml'],
            'block2_image' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],

            'block3_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'block3_text'  => ['type' => self::TYPE_HTML,   'lang' => true, 'validate' => 'isCleanHtml'],
            'block3_image' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],

            'block4_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'block4_text'  => ['type' => self::TYPE_HTML,   'lang' => true, 'validate' => 'isCleanHtml'],
            'block4_image' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 255],


            'products_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
            'products_subtitle' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];
}
