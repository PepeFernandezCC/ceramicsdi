<?php
class InspirationCategoryProduct extends ObjectModel
{
    public $id; // alias de la PK
    public $id_inspiration;
    public $id_category;
    public $id_product;
    public $id_image;
    public $position;

    public static $definition = [
        'table'   => 'inspiration_category_product',
        'primary' => 'id_inspiration',
        'fields'  => [
            'id_category' => ['type' => self::TYPE_INT, 'required' => true],
            'id_product'  => ['type' => self::TYPE_INT, 'required' => true],
            'id_image'    => ['type' => self::TYPE_INT],
            'position'    => ['type' => self::TYPE_INT],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->id = $this->id_inspiration;
    }
}
