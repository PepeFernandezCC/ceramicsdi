<?php
/**
 * Ficheros de log por importación, guardados en modules/ccproducteditor/logs (no accesibles por URL directa).
 */
class CcpeLog
{
    const TYPE_VALIDATION = 'validacion';
    const TYPE_UPDATE = 'actualizacion';

    public static function directory()
    {
        return _PS_MODULE_DIR_ . 'ccproducteditor/logs/';
    }

    public static function ensureDirectory()
    {
        $dir = self::directory();
        if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
            return false;
        }
        if (!file_exists($dir . '.htaccess')) {
            file_put_contents($dir . '.htaccess', "<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Deny from all\n</IfModule>\n");
        }
        if (!file_exists($dir . 'index.php')) {
            file_put_contents($dir . 'index.php', "<?php\nheader('Location: ../');\nexit;\n");
        }

        return true;
    }

    /**
     * @param string $type self::TYPE_*
     * @param string[] $header líneas de cabecera (fichero, idioma, empleado...)
     * @param string[] $lines una línea por error
     *
     * @return string nombre del fichero creado
     */
    public static function write($type, array $header, array $lines)
    {
        self::ensureDirectory();
        $file = 'import_' . date('Ymd_His') . '_' . Tools::passwdGen(6, 'NUMERIC') . '_' . $type . '.log';
        $content = implode(PHP_EOL, $header) . PHP_EOL . str_repeat('-', 80) . PHP_EOL
            . implode(PHP_EOL, $lines) . PHP_EOL;
        file_put_contents(self::directory() . $file, "\xEF\xBB\xBF" . $content);

        return $file;
    }

    /**
     * @return string|null ruta absoluta si el nombre es un log válido
     */
    public static function path($file)
    {
        $file = basename((string) $file);
        if (!preg_match('/^import_[0-9_]+_(' . self::TYPE_VALIDATION . '|' . self::TYPE_UPDATE . ')\.log$/', $file)) {
            return null;
        }
        $path = self::directory() . $file;

        return is_file($path) ? $path : null;
    }
}
