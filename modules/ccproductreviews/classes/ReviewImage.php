<?php

class ReviewImage extends ObjectModel
{
    public $id_image;
    public $id_review;
    public $file_name;
    public $date_add;

    public static $definition = [
        'table' => 'product_review_image',
        'primary' => 'id_image',
        'fields' => [
            'id_review' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'file_name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'date_add'  => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public static function getByReview($idReview)
    {
        $idReview = (int)$idReview;
        if ($idReview <= 0) {
            return [];
        }

        return Db::getInstance()->executeS('
            SELECT `id_image`, `id_review`, `file_name`, `date_add`
            FROM `'._DB_PREFIX_.'product_review_image`
            WHERE `id_review`='.(int)$idReview.'
            ORDER BY `id_image` ASC
        ');
    }
}