<?php

require_once __DIR__ . '/config/config.inc.php';
require_once __DIR__ . '/init.php';

/**
 * Seguridad básica:
 * Cambia este token antes de subirlo.
 * Ejecutar en navegador:
 * https://ceramicconnection.com/fix_old_phones.php?token=PFZ678CCE
 */
$token = 'PFZ678CCE';

if (php_sapi_name() !== 'cli') {
    if (!isset($_GET['token']) || $_GET['token'] !== $token) {
        die('Acceso denegado');
    }
}

/**
 * Modo simulación.
 * true  = no actualiza, solo muestra qué cambiaría.
 * false = actualiza la base de datos.
 */
$dryRun = false;

/**
 * Prefijos por ISO.
 * Así no dependes del id_country, que puede cambiar entre tiendas.
 */
$prefixesByIso = [
    'DE' => '+49',
    'AT' => '+43',
    'BE' => '+32',
    'CA' => '+1',
    'ES' => '+34',
    'FI' => '+358',
    'FR' => '+33',
    'GR' => '+30',
    'IT' => '+39',
    'LU' => '+352',
    'NL' => '+31',
    'PL' => '+48',
    'PT' => '+351',
    'CZ' => '+420',
    'GB' => '+44',
    'SE' => '+46',
    'CH' => '+41',
    'DK' => '+45',
    'NO' => '+47',
    'IE' => '+353',
    'IL' => '+972',
    'RO' => '+40',
    'SK' => '+421',
    'AD' => '+376',
    'BY' => '+375',
    'HR' => '+385',
    'CY' => '+357',
    'EE' => '+372',
    'GE' => '+995',
    'GI' => '+350',
    'VA' => '+379',
    'LV' => '+371',
    'LI' => '+423',
    'LT' => '+370',
    'MT' => '+356',
    'HU' => '+36',
    'MD' => '+373',
    'MC' => '+377',
    'ME' => '+382',
    'RS' => '+381',
    'SI' => '+386',
    'BA' => '+387',
    'BG' => '+359',
];

function normalizePhoneWithPrefix($rawPhone, $prefix)
{
    $phone = preg_replace('/\D+/', '', (string) $rawPhone);
    $prefixDigits = preg_replace('/\D+/', '', (string) $prefix);

    if ($phone === '') {
        return '';
    }

    // Si viene como 0049..., quitar 0049
    if ($prefixDigits && strpos($phone, '00' . $prefixDigits) === 0) {
        $phone = substr($phone, strlen('00' . $prefixDigits));
    }

    // Si viene como 49..., quitar 49
    if ($prefixDigits && strpos($phone, $prefixDigits) === 0) {
        $phone = substr($phone, strlen($prefixDigits));
    }

    // Quitar ceros iniciales nacionales: 0176... -> 176...
    $phone = preg_replace('/^0+/', '', $phone);

    if ($phone === '') {
        return '';
    }

    return $prefix . $phone;
}

$sql = '
    SELECT 
        a.id_address,
        a.phone,
        a.phone_mobile,
        c.iso_code
    FROM ' . _DB_PREFIX_ . 'address a
    INNER JOIN ' . _DB_PREFIX_ . 'country c 
        ON c.id_country = a.id_country
    WHERE a.deleted = 0
      AND (
            a.phone IS NOT NULL 
            OR a.phone_mobile IS NOT NULL
      )
';

$rows = Db::getInstance()->executeS($sql);

$total = 0;
$updated = 0;
$skipped = 0;
$errors = 0;

echo '<pre>';

foreach ($rows as $row) {
    $total++;

    $idAddress = (int) $row['id_address'];
    $isoCode = strtoupper(trim($row['iso_code']));

    if (!isset($prefixesByIso[$isoCode])) {
        $skipped++;
        echo "SKIP #{$idAddress} - ISO sin prefijo: {$isoCode}\n";
        continue;
    }

    $prefix = $prefixesByIso[$isoCode];

    $oldPhone = (string) $row['phone'];
    $oldMobile = (string) $row['phone_mobile'];

    $newPhone = $oldPhone;
    $newMobile = $oldMobile;

    if (trim($oldPhone) !== '') {
        $newPhone = normalizePhoneWithPrefix($oldPhone, $prefix);
    }



    if ($newPhone === $oldPhone && $newMobile === $oldMobile) {
        $skipped++;
        continue;
    }

    echo "UPDATE #{$idAddress} [{$isoCode}]\n";
    echo "  phone:        {$oldPhone} => {$newPhone}\n";


    if (!$dryRun) {
        $result = Db::getInstance()->update(
            'address',
            [
                'phone' => pSQL($newPhone),
                'date_upd' => date('Y-m-d H:i:s'),
            ],
            'id_address = ' . (int) $idAddress
        );

        if ($result) {
            $updated++;
        } else {
            $errors++;
            echo "  ERROR actualizando #{$idAddress}\n";
        }
    } else {
        $updated++;
    }

    echo "\n";
}

echo "-----------------------------\n";
echo "Total revisadas: {$total}\n";
echo $dryRun ? "Cambiarían: {$updated}\n" : "Actualizadas: {$updated}\n";
echo "Saltadas: {$skipped}\n";
echo "Errores: {$errors}\n";
echo $dryRun ? "MODO DRY RUN: no se ha actualizado nada.\n" : "MODO REAL: base de datos actualizada.\n";
echo '</pre>';