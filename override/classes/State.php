<?php
class State extends StateCore
{
    /**
     * Obtiene las provincias (estados) para un país específico.
     *
     * @param int $id_country ID del país
     * @return array Provincias del país
     */
    public static function getProvincesByCountry($id_country)
    {
        if (!(int)$id_country) {
            return [];
        }

        $sql = 'SELECT `id_state`, `name` 
                FROM `' . _DB_PREFIX_ . 'state` 
                WHERE `id_country` = ' . (int)$id_country . ' 
                AND `active` = 1 
                ORDER BY `name` ASC';

        return Db::getInstance()->executeS($sql);
    }
}