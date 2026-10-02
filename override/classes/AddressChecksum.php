<?php

/**
 * El checksum del carrito (CartChecksum, en cada carga del checkout) usa
 * Address::getFields(), que valida todos los campos y lanza PrestaShopException
 * si alguno guardado en BD no cumple su regla (p.ej. un dni con un espacio
 * invisible). Eso tumbaba el checkout con un 500 por un dato de una dirección.
 *
 * Para el checksum solo necesitamos los valores, no validarlos: si la validación
 * falla, se calcula con los valores tal cual y se deja aviso en el log
 * (Parámetros avanzados > Registros) para poder corregir la dirección.
 */
class AddressChecksum extends AddressChecksumCore
{
    public function generateChecksum($address)
    {
        try {
            return parent::generateChecksum($address);
        } catch (PrestaShopException $e) {
            PrestaShopLogger::addLog(
                'AddressChecksum: dirección con datos no válidos (' . $e->getMessage() . ')',
                2,
                null,
                'Address',
                (int) $address->id,
                false
            );
        }

        $uniqId = '';
        foreach (array_keys(Address::$definition['fields']) as $field) {
            $value = isset($address->$field) ? $address->$field : '';
            $uniqId .= (is_scalar($value) ? $value : '') . self::SEPARATOR;
        }
        $uniqId = rtrim($uniqId, self::SEPARATOR);

        return sha1($uniqId);
    }
}
