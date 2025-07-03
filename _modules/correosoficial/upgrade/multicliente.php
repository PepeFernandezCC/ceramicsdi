<?php

$tableSender = CorreosOficialUtils::getPrefix() . "correos_oficial_senders";
$tableCodes = CorreosOficialUtils::getPrefix() . "correos_oficial_codes";
$toUpdate = [
    'correos_code' => null,
    'cex_code' => null
];

$query4 = "SHOW INDEX FROM " . $tableCodes ." WHERE Key_name = 'company';";
$record4 = Db::getInstance()->executeS($query4);
if($record4) {

    // Eliminamos la clave primaria de Codes
    Db::getInstance()->execute("ALTER TABLE " . $tableCodes . " DROP INDEX company;");

    // Añadimos columnas a tabla senders
    $senderTableCheck = Db::getInstance()->executeS(
        "SHOW COLUMNS FROM " . $tableSender . " WHERE Field = 'correos_code';"
    );
    if (!$senderTableCheck) {
        Db::getInstance()->execute("
        ALTER TABLE " . $tableSender . "
            ADD COLUMN
                `correos_code` INT(11),
            ADD COLUMN
                `cex_code` INT(11)
        ");
    }
    unset($senderTableCheck);

    // Código de Correos
    $correosCode = Db::getInstance()->getRow(
        "SELECT `id` FROM " . $tableCodes . " WHERE `company` = 'Correos'"
    );
    if ($correosCode) {
        $toUpdate['correos_code'] = $correosCode['id'];
        unset($correosCode);
    }

    // Código de CEX
    $cexCode = Db::getInstance()->getRow(
        "SELECT `id` FROM " . $tableCodes . " WHERE `company` = 'CEX'"
    );
    if ($cexCode) {
        $toUpdate['cex_code'] = $cexCode['id'];
        unset($cexCode);
    }

    // Actualizamos tabla senders
    Db::getInstance()->update(
        'correos_oficial_senders',
        [
            'correos_code' => $toUpdate['correos_code'],
            'cex_code' => $toUpdate['cex_code']
        ]
    );

}