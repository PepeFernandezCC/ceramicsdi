<?php

class Inspirationcards extends ObjectModel
{
    public $id_inspiration;
    public $active;
    public $date_add;
    public $date_upd;
    public $image;

    // multilang
    public $name;
    public $slug;

    public static $definition = [
        'table' => 'inspirationcards',
        'primary' => 'id_inspiration',
        'multilang' => true,

        'fields' => [
            'active' => ['type' => self::TYPE_BOOL],
            'date_add' => ['type' => self::TYPE_DATE],
            'date_upd' => ['type' => self::TYPE_DATE],

            // lang
            'name' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'required' => false,
                'size' => 255
            ],
            'slug' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'required' => true,
                'size' => 255
            ],
            //image
            'image' => [
                'type' => self::TYPE_STRING,
                'size' => 255,
            ],
        ],
    ];
}