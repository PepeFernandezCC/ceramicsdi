<?php

class CorreosOficialShippingNumber
{

    public static function updateShipingNumberInOrder($order_id, $shipping_number)
    {
        $order = new Order($order_id);
        $tracking_number = $order->getWsShippingNumber();

        $record = self::getOrderCarrier($order->id, $order->id_carrier);

        if (empty($tracking_number) || $tracking_number != $shipping_number) {

            /*
              Si hay $record, es un transportista actual
              Si no es un transportista eliminado, lo restauramos al nuevo transportista para que pueda valer en tras
              haber desinstalado el módulo
             */
            if (count($record)) {
                $orderCarrier = new OrderCarrier($record[0]['id_order_carrier']);
            } else {

                //Transportista que ha sido eliminado
                $orderCarrier=self::getOrderCarrier($order->id);

                // El pedido puede haber sido eliminado a mano en la tabla order_carrier
                if (empty($orderCarrier)) {
                    throw new LogicException('ERROR CORREOS OFICIAL 19021: No existe el pedido ' . $order->id . ' en la tabla order_carrier. Es posible que el pedido se haya eliminado directamente de la base de datos. Necesita restauración manual');
                }
                
                // Transpotista antiguo
                $oldCarrier=Db::getInstance()->getRow('SELECT id_reference FROM '._DB_PREFIX_."carrier WHERE id_carrier=".$orderCarrier['id_carrier']);
                
                // Transportista nuevo que ha reemplazado el anterior
                $newCarrier=Db::getInstance()->getRow('SELECT id_carrier FROM '._DB_PREFIX_."carrier WHERE id_reference = ".$oldCarrier['id_reference']." ORDER BY id_carrier DESC");
                
                // Relación antiguo->nuevo
                $oldAndNewIDCarrierArray[$orderCarrier['id_carrier']]=$newCarrier['id_carrier'];
                
                // Restauramos el id_de los transportistas en order_carriers para poder seguir funcionando con los transportistas eliminados.
                self::restoreLostCarriers($oldAndNewIDCarrierArray);

                // Conseguimos el último transportista modificado
                $modified_order_carrier = Db::getInstance()->getRow('SELECT id_order_carrier FROM '._DB_PREFIX_."order_carrier WHERE id_order = ".$order->id." ORDER BY id_order_carrier DESC");
                

                // El objeto orderCarrier ahora va con el nuevo transportista
                $orderCarrier = new OrderCarrier($modified_order_carrier['id_order_carrier']);
            }
            $orderCarrier->date_add = date('Y-m-d H:i:s');
            $orderCarrier->id_order = $order->id;
            $orderCarrier->id_carrier = $order->id_carrier;
            $orderCarrier->tracking_number = $shipping_number;
            $orderCarrier->update();
        }
    }

    private static function getOrderCarrier($id_order, $id_carrier=null)
    {
        if (is_null($id_carrier)) {
            return Db::getInstance()->getRow('SELECT id_carrier FROM '._DB_PREFIX_."order_carrier WHERE id_order=$id_order");
        }
        else {
            $sql = "SELECT id_order_carrier, id_order, id_carrier FROM " . _DB_PREFIX_ . "order_carrier where id_order='$id_order' AND id_carrier='$id_carrier'";
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    /**
     * Método para restaurar el id_carrier de la tabla order_carriers
     * Ejemplo $carriers_relation = array('147' => '180', '155' => '183');
     * @param $carriers_relation array clave=>valor, donde la clave es el antiguo, y el valor es el nuevo id_carrier.
     * @return void
     */
    public static function restoreLostCarriers($carriers_relation)
    {
        $in = implode(', ', array_keys($carriers_relation)) ;

        $sql = "UPDATE " . _DB_PREFIX_ . "order_carrier SET id_carrier = CASE";
        
        foreach ($carriers_relation as $oldCarrier => $newCarrier) {
            $sql .= " WHEN id_carrier = $oldCarrier THEN $newCarrier";
        }
        
        $sql .= " ELSE id_carrier END WHERE tracking_number='' AND id_carrier IN (" . $in. ")";
        
        Db::getInstance()->execute($sql);
    }
}
