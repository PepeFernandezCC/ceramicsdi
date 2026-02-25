<?php

class Review
{
    public static function customerCanReview($idCustomer, $idProduct)
    {
        $delivered = (int)Configuration::get('CCPR_DELIVERED_STATE');

        // ¿Ha comprado ese producto en un pedido en estado "entregado"?
        $sql = '
            SELECT 1
            FROM `'._DB_PREFIX_.'orders` o
            INNER JOIN `'._DB_PREFIX_.'order_detail` od ON od.id_order = o.id_order
            WHERE o.id_customer = '.(int)$idCustomer.'
              AND od.product_id = '.(int)$idProduct.'
              AND o.current_state = '.(int)$delivered.'
        ';
        return (bool)Db::getInstance()->getValue($sql);
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
}