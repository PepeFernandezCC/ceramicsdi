<?php

class Review extends ObjectModel
{

    public $id_review;
    public $id_product;
    public $id_customer;
    public $customer_name;
    public $rating;
    public $comment;
    public $active;
    public $date_add;

    public static $definition = [
        'table' => 'product_review',
        'primary' => 'id_review',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'customer_name' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'rating' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'comment' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public static function customerCanReview($idCustomer, $idProduct)
    {
        $delivered = (int)Configuration::get('CCPR_DELIVERED_STATE');

        // ¿Ha comprado ese producto en un pedido en estado "entregado"?
        $sql_orders = '
            SELECT 1
            FROM `'._DB_PREFIX_.'orders` o
            INNER JOIN `'._DB_PREFIX_.'order_detail` od ON od.id_order = o.id_order
            WHERE o.id_customer = '.(int)$idCustomer.'
              AND od.product_id = '.(int)$idProduct.'
              AND o.current_state = '.(int)$delivered.'
        ';

        $sql_review = '
            SELECT 1
            FROM `'._DB_PREFIX_.'product_review`
            WHERE id_customer = '.(int)$idCustomer.'
              AND id_product = '.(int)$idProduct.'
        ';
        $customerHaveThisProduct = (bool)Db::getInstance()->getValue($sql_orders);
        $customerHaveThisReview = (bool)Db::getInstance()->getValue($sql_review);

        if($customerHaveThisProduct && !$customerHaveThisReview) {
            return true;
        }

        return false;
    }

    public static function getByProduct($idProduct)
    {
        $sql = '
            SELECT r.*, 
                   (SELECT GROUP_CONCAT(i.file_name SEPARATOR ",")
                    FROM `'._DB_PREFIX_.'product_review_image` i
                    WHERE i.id_review = r.id_review) AS images
            FROM `'._DB_PREFIX_.'product_review` r
            WHERE r.id_product = '.(int)$idProduct.' AND r.active = 1
            ORDER BY r.date_add DESC
        ';
        $rows = Db::getInstance()->executeS($sql);

        foreach ($rows as &$row) {
            $row['images'] = $row['images'] ? explode(',', $row['images']) : [];
        }
        return $rows;
    }

    public static function getAverageByProduct($idProduct)
    {
        $sql = '
            SELECT AVG(rating)
            FROM `'._DB_PREFIX_.'product_review`
            WHERE id_product='.(int)$idProduct.' AND active=1
        ';
        $avg = (float)Db::getInstance()->getValue($sql);
        return round($avg, 1);
    }

    public static function getCountByProduct($idProduct)
    {
        $sql = '
            SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'product_review`
            WHERE id_product='.(int)$idProduct.' AND active=1
        ';
        return (int)Db::getInstance()->getValue($sql);
    }

    public static function bulkDelete(array $ids)
    {
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, function ($v) { return $v > 0; });

        if (empty($ids)) {
            return false;
        }

        $in = implode(',', $ids);

        // 1) Borrar imágenes (BD) en bloque
        Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'product_review_image`
            WHERE `id_review` IN ('.$in.')
        ');

        // 2) Borrar email log (si aplica) en bloque
        Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'product_review_email_log`
            WHERE `id_review` IN ('.$in.')
        ');

        // 3) Borrar reviews en bloque
        return Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'product_review`
            WHERE `id_review` IN ('.$in.')
        ');
    }
}