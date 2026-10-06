<?php
/**
 * Definición única de las columnas editables (listado, CSV y validación usan esta tabla).
 */
class CcpeFields
{
    const TYPE_BOOL = 'bool';
    const TYPE_TEXT = 'text';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_INT = 'int';
    const TYPE_CHOICE = 'choice';
    const TYPE_LINK_REWRITE = 'link_rewrite';
    const TYPE_EAN13 = 'ean13';

    const CSV_ID_HEADER = 'ID';
    const CSV_NULL = 'NULL';
    const CSV_MULTI_SEPARATOR = '|';

    /** Disponibilidad (stock_available.out_of_stock) */
    const OUT_OF_STOCK_DENY = 0;
    const OUT_OF_STOCK_ALLOW = 1;
    const OUT_OF_STOCK_DEFAULT = 2;

    /**
     * Orden = orden de columnas en el listado y en el CSV.
     * 'lang' => true indica que el campo se guarda en product_lang (idioma seleccionado).
     */
    public static function all()
    {
        return [
            'active' => ['label' => 'Activo', 'type' => self::TYPE_BOOL],
            'reference' => ['label' => 'Referencia', 'type' => self::TYPE_TEXT, 'max' => 64, 'validate' => 'isReference'],
            'name' => ['label' => 'Nombre', 'type' => self::TYPE_TEXT, 'max' => 128, 'validate' => 'isCatalogName', 'required' => true, 'lang' => true],
            'price' => ['label' => 'Precio', 'type' => self::TYPE_DECIMAL],
            'id_tax_rules_group' => ['label' => 'Regla de impuestos', 'type' => self::TYPE_CHOICE, 'choices' => 'taxes'],
            'quantity' => ['label' => 'Cantidad', 'type' => self::TYPE_INT],
            'id_manufacturer' => ['label' => 'Marca', 'type' => self::TYPE_CHOICE, 'choices' => 'manufacturers'],
            'id_supplier' => ['label' => 'Proveedor', 'type' => self::TYPE_CHOICE, 'choices' => 'suppliers'],
            'out_of_stock' => ['label' => 'Preferencia de disponibilidad', 'type' => self::TYPE_CHOICE, 'choices' => 'availability'],
            'available_now' => ['label' => 'Etiqueta en stock', 'type' => self::TYPE_TEXT, 'max' => 255, 'validate' => 'isGenericName', 'lang' => true],
            'available_later' => ['label' => 'Etiqueta sin stock con pedidos permitidos', 'type' => self::TYPE_TEXT, 'max' => 255, 'validate' => 'isGenericName', 'lang' => true],
            'weight' => ['label' => 'Peso', 'type' => self::TYPE_DECIMAL],
            'link_rewrite' => ['label' => 'URL amigable', 'type' => self::TYPE_LINK_REWRITE, 'max' => 128, 'required' => true, 'lang' => true],
            'ean13' => ['label' => 'EAN13', 'type' => self::TYPE_EAN13],
        ];
    }

    public static function availabilityChoices()
    {
        return [
            self::OUT_OF_STOCK_DENY => 'Denegar pedidos',
            self::OUT_OF_STOCK_ALLOW => 'Permitir pedidos',
            self::OUT_OF_STOCK_DEFAULT => 'Usar comportamiento por defecto',
        ];
    }

    /** Cabecera CSV de una característica: "Nombre [id]" para poder identificarla aunque cambie el nombre */
    public static function featureHeader($idFeature, $name)
    {
        return trim($name) . ' [' . (int) $idFeature . ']';
    }

    /** @return int|null id de característica si la cabecera tiene el formato "Nombre [id]" */
    public static function parseFeatureHeader($header)
    {
        return preg_match('/\[(\d+)\]\s*$/', $header, $m) ? (int) $m[1] : null;
    }

    /** Número sin ceros sobrantes: 12.500000 -> 12.5 */
    public static function formatDecimal($value)
    {
        $formatted = rtrim(rtrim(number_format((float) $value, 6, '.', ''), '0'), '.');

        return $formatted === '-0' ? '0' : $formatted;
    }
}
