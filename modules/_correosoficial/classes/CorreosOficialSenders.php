<?php
if (!defined('_PS_VERSION_')) {
  exit;
}
require_once dirname(__FILE__).'/../vendor/ecommerce_common_lib/Dao/CorreosOficialSendersDao.php';

/**
 * Clase genérica para lo relacionado con Senders
 */
class CorreosOficialSenders extends CorreosOficialSendersDao {

  private $table='correos_oficial_senders';

  public static function getDefaultSender($id_sender = false, $id_shop = null){
    $context = Context::getContext();
    $id_shop = ($id_shop !== null) ? $id_shop : $context->shop->id;

    $query="SELECT * FROM "._DB_PREFIX_."correos_oficial_senders WHERE sender_default='1' AND id_shop = ". $id_shop;
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
  }
  
  // id_shop para multitienda PS
  public static function getSenders($company = false, $id_shop = null){
    $context = Context::getContext();
    $id_shop = ($id_shop !== null) ? $id_shop : $context->shop->id;
    $dao = new CorreosOficialDAO();

    if (!$company) {
        return $dao->readRecord('correos_oficial_senders', 'WHERE id_shop = ' . $id_shop , null, true);
    }
    $where = 'WHERE ' . strtolower($company) . '_code <> 0 AND id_shop = ' . $id_shop . ' ORDER BY sender_default desc';

    return $dao->readRecord('correos_oficial_senders', $where, null, true);
  }

  // id_shop para multitienda PS
  public static function getSendersWithCodes($id_shop = null){
    $context = Context::getContext();
    $id_shop = ($id_shop !== null) ? $id_shop : $context->shop->id;
    $class = new self();
    $senders = $class->getSenders(false, $id_shop);

    foreach($senders as $key => $value){

        if($senders[$key]['correos_code'] != 0){
            $resultCorreos = $class->readRecord('correos_oficial_codes', 'WHERE id = '. $senders[$key]['correos_code'].' AND id_shop ='. $id_shop, null, true);
            $senders[$key]['correos_code'] = $resultCorreos[0]['CorreosCustomer'];
        }

        if($senders[$key]['cex_code'] != 0){
            $resultCEX = $class->readRecord('correos_oficial_codes', 'WHERE id = '. $senders[$key]['cex_code'], null, true);
            $resultCEX = $class->readRecord('correos_oficial_codes', 'WHERE id = '. $senders[$key]['cex_code'].' AND id_shop ='. $id_shop, null, true);
            $senders[$key]['cex_code'] = $resultCEX[0]['CEXCustomer'];
        }

    }

    return $senders;
}
  
  public static function getSendersWithCodesById($sender_id){
      $query="SELECT * FROM "._DB_PREFIX_."correos_oficial_senders WHERE id='$sender_id'";
      $sender = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

      if($sender['correos_code'] != 0){
        $resultCorreosRow = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
          "SELECT * FROM "._DB_PREFIX_."correos_oficial_codes WHERE id=" . $sender['correos_code']
        );
        $sender['correos_code'] = $resultCorreosRow['CorreosCustomer'];
      }

      if($sender['cex_code'] != 0){
          $resultCEXRow = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            "SELECT * FROM "._DB_PREFIX_."correos_oficial_codes WHERE id=" . $sender['cex_code']
          );
          $sender['cex_code'] = $resultCEXRow['CEXCustomer'];
      }

      return $sender;
  }

  public static function getDefaultTime(){
        $query = "SELECT * FROM ". _DB_PREFIX_ ."correos_oficial_senders WHERE sender_default='1'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
  }

  //Actualiza default_sender
  public function updateFieldSetRecord($field){
      $this->saveSenderDefaultRecord($field);
  }

  public function saveSenderDefaultRecord($data)
  {
      $context = Context::getContext();
      $query = "UPDATE ".CorreosOficialUtils::getPrefix().$this->table." SET sender_default='0' WHERE id_shop = ".$context->shop->id;
      $query2 = "UPDATE ".CorreosOficialUtils::getPrefix().$this->table." SET sender_default='1' WHERE id=". $data;

      $this->executeQuery($query);
      $this->executeQuery($query2);
  }
}
