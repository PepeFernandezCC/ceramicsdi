<?php
/**
 * Tipo de incidencia: el CRUD que gestiona el equipo web sin tocar
 * codigo. Cada fila tiene:
 *  - code: el valor EXACTO que viaja como "tipo:" en el correo (ver
 *    apartado 2 y 7 del PDF "Formulario - CC"). Se normaliza siempre a
 *    mayusculas. OJO: si cambias el codigo de un tipo que el sistema de
 *    incidencias ya reconoce, hay que avisar antes al responsable de
 *    ese sistema (apartado 11, regla 01) o dejara de reconocerlo.
 *  - descripcion: texto traducido (uno por idioma) que ve el cliente
 *    en el desplegable del formulario.
 *  - email: direccion a la que se envia el correo para ese tipo. Vacio
 *    = se usa el email general del modulo (CCINCIDENCIAS_TO_EMAIL).
 *  - active / position: visibilidad y orden en el formulario publico.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class CcIncidenciasTipo extends ObjectModel
{
    public $code;
    public $email;
    public $active;
    public $position;

    /** @var string traducible */
    public $descripcion;

    public static $definition = array(
        'table' => 'ccincidencias_tipo',
        'primary' => 'id_ccincidencias_tipo',
        'multilang' => true,
        'fields' => array(
            'code' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 32,
            ),
            'email' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail',
                'size' => 150,
            ),
            'active' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'position' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ),
            'descripcion' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'required' => true,
                'size' => 255,
            ),
        ),
    );

    public function add($autoDate = true, $nullValues = false)
    {
        $this->code = self::normalizeCode($this->code);

        if (!Validate::isLoadedObject($this) && (int) $this->position <= 0) {
            $this->position = (int) Db::getInstance()->getValue(
                'SELECT MAX(position) + 1 FROM `' . _DB_PREFIX_ . 'ccincidencias_tipo`'
            );
        }

        return parent::add($autoDate, $nullValues);
    }

    public function update($nullValues = false)
    {
        $this->code = self::normalizeCode($this->code);

        return parent::update($nullValues);
    }

    /**
     * Mismo saneamiento que la referencia del pedido: trim, mayusculas
     * y espacios interiores colapsados. El "tipo" NUNCA se traduce y
     * SIEMPRE viaja en mayusculas (apartado 2 del PDF).
     */
    public static function normalizeCode($code)
    {
        $code = trim((string) $code);
        $code = Tools::strtoupper($code);
        $code = preg_replace('/\s+/', ' ', $code);

        return $code;
    }

    /**
     * Tipos activos para pintar el desplegable del formulario publico,
     * ya con la descripcion en el idioma indicado.
     */
    public static function getActiveForFront($idLang)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT t.id_ccincidencias_tipo, t.code, t.email, tl.descripcion
             FROM `' . _DB_PREFIX_ . 'ccincidencias_tipo` t
             INNER JOIN `' . _DB_PREFIX_ . 'ccincidencias_tipo_lang` tl
                ON tl.id_ccincidencias_tipo = t.id_ccincidencias_tipo AND tl.id_lang = ' . (int) $idLang . '
             WHERE t.active = 1
             ORDER BY t.position ASC, t.id_ccincidencias_tipo ASC'
        );

        return $rows ?: array();
    }
}
