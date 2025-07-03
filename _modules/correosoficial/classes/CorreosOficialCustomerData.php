<?php
if (!defined('_PS_VERSION_')) {
  exit;
}
require_once dirname(__FILE__).'/../vendor/ecommerce_common_lib/Dao/CorreosOficialCustomerDataDao.php';

/**
 * Clase genérica para lo relacionado con CustomerData
 */
class CorreosOficialCustomerData extends CorreosOficialCustomerDataDao {
    public function getDataTableCustomerList($id_shop = null)
    {
        $records = array();

        // Se evita que de error de datatable al cargar la liste de clientes activos en Ajustes
        if (!extension_loaded('soap')) {
            die(json_encode($records));
        }

        // Obtenemos todos los registros de la tabla
        if ($id_shop == null) {
            $records = $this->getRecords('correos_oficial_codes');
        } else {
            $sql = "SELECT * FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_codes 
                WHERE id_shop = " . $id_shop . "";
            $records = $this->getRecordsWithQuery($sql);

        }

        // Recorremos todos los registros, y añadimos una columna status para indicar si la conexión es correcta o no
        foreach ($records as $record) {

            // Si es Correos, hacemos una petición SOAP para comprobar que la conexión es correcta
            if ($record->company=='Correos') {
                $checkCorreosConnection = json_decode((new CorreosSoap())->altaClienteCorreosOpCall($record->id));
                $record->status = $checkCorreosConnection->validacion ? true : false;
            }
            
            if ($record->company=='CEX') {
                $checkCexConnection = json_decode((new CexRest())->altaClienteCEXCall($record->id));
                $record->status = $checkCexConnection->validacion ? true : false;
            }

        }



        die(json_encode($records));
    }

}