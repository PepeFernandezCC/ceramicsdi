<?php
/**
 * Creative Slider - Responsive Slideshow Module
 * https://creativeslider.webshopworks.com
 *
 * @author    WebshopWorks <info@webshopworks.com>
 * @copyright 2015-2025 WebshopWorks
 * @license   One Domain Licence
 *
 * Not allowed to resell or redistribute this software
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

define('LS_CONTENT_DIR', _PS_ROOT_DIR_);
define('LS_VIEWS_URL', _MODULE_DIR_ . 'layerslider/views/');
define('LS_PRIORITY', (int) ls_get_option('ls_scripts_priority', 50));
define('LS_UNPACKED', (int) ls_get_option('ls_load_unpacked', 0));
define('LS_JS_THEME_CACHE', (int) Configuration::get('PS_JS_THEME_CACHE', 0));
define('LS_CSS_THEME_CACHE', (int) Configuration::get('PS_CSS_THEME_CACHE', 0));

function ls_current_user_can($capability)
{
    return !empty($GLOBALS['employee']->id);
}

function ls_plugin_basename($file)
{
    return basename(dirname($file)) . '/' . basename($file);
}

function ls_plugins_url($path = '', $plugin = '')
{
    return rtrim(_MODULE_DIR_ . 'layerslider/base/' . $path, '/');
}

function ls_content_url($path = '')
{
    return rtrim(_MODULE_DIR_ . 'layerslider/base/' . $path, '/');
}

function ls_get_posts($params = null)
{
    $id_lang = $GLOBALS['language']->id;
    $id_shop = $GLOBALS['context']->shop->id;
    $order_by = !empty($params['orderby']) ? $params['orderby'] : 'date_add';
    // compatibility fix
    $order_by === 'date' && $order_by = 'date_add';
    $order_way = !empty($params['order']) ? $params['order'] : 'DESC';
    $limit = !empty($params['limit']) ? $params['limit'] : 50;
    $offset = !empty($params['offset']) ? $params['offset'] : 0;

    if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
        exit(Tools::displayError());
    }

    $query = new DbQuery();
    $query->select('DISTINCT p.`id_product`, p.`date_add`, p.`date_upd`, p.`id_category_default`, pl.`name`, pl.`link_rewrite`, pl.`description`, pl.`description_short`, m.`name` AS manufacturer');
    $query->from('product', 'p')->join(Shop::addSqlAssociation('product', 'p'));
    $query->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.id_shop = ' . (int) $id_shop);
    $query->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');
    $query->leftJoin('category_product', 'c', 'c.`id_product` = p.`id_product`');
    $query->leftJoin('product_tag', 'pt', 'pt.`id_product` = p.`id_product`');
    $query->where('pl.`id_lang` = ' . (int) $id_lang);
    $query->where('product_shop.`active` = 1 AND product_shop.`visibility` IN ("both", "catalog")');
    empty($params['post_type']) || $query->where('m.`id_manufacturer` IN (' . implode(',', array_map('intval', $params['post_type'])) . ')');
    empty($params['category__in']) || $query->where('c.`id_category` IN (' . implode(',', array_map('intval', $params['category__in'])) . ')');
    empty($params['tag__in']) || $query->where('pt.`id_tag` IN (' . implode(',', array_map('intval', $params['tag__in'])) . ')');
    if ('name' === $order_by) {
        $query->orderBy('pl.`name` ' . pSQL($order_way));
    } elseif ('position' === $order_by) {
        $query->orderBy('c.`position` ' . ('ASC' === $order_way ? 'DESC' : 'ASC'));
    } elseif ('quantity' === $order_by) {
        $query->leftJoin('product_sale', 'ps', 'ps.`id_product` = p.`id_product`');
        $query->orderBy('ps.`quantity` ' . pSQL($order_way));
    } elseif ('reduction' === $order_by) {
        $query->innerJoin('specific_price', 'sp', 'sp.`id_product` = p.`id_product`');
        $query->where('sp.`from` <= NOW() AND (sp.`to` >= NOW() OR sp.`to` = "0000-00-00 00:00:00")');
        $query->orderBy('sp.`reduction` ' . pSQL($order_way));
    } elseif ('rand' === $order_by) {
        $query->orderBy('RAND()');
    } else {
        $query->orderBy('p.`' . bqSQL($order_by) . '` ' . pSQL($order_way));
    }
    $query->limit($limit, $offset);

    return Db::getInstance()->executeS($query);
}

function ls_get_post_type_object($post_type)
{
    return (object) [
        'labels' => (object) ['name' => $post_type['name']],
    ];
}

class ManufacturerArray extends ArrayObject
{
    public function __toString()
    {
        return isset($this['slug']) ? (string) $this['slug'] : '';
    }
}

function ls_get_post_types()
{
    $mans = Manufacturer::getManufacturers();
    foreach ($mans as &$man) {
        $man = new ManufacturerArray($man);
        $man['slug'] = $man['id_manufacturer'];
    }
    array_unshift($mans, new ManufacturerArray([
        'name' => ls__("Don't filter manufacturers"),
        'slug' => 0,
    ]));

    return $mans;
}

function ls_get_categories()
{
    require_once _PS_MODULE_DIR_ . 'layerslider/classes/PSOpts.php';

    return PSOpts::getCategoryList();
}

function ls_get_tags()
{
    require_once _PS_MODULE_DIR_ . 'layerslider/classes/PSOpts.php';
    $tags = PSOpts::getTagList();
    foreach ($tags as &$tag) {
        $tag = (object) $tag;
    }

    return $tags;
}

function ls_get_post_thumbnail_id($post_id)
{
    return $post_id;
}

function ls_get_attachment_image($attachment_id, $size = 'thumbnail', $icon = false, $attr = '')
{
    $attrs = [
        'src' => $attachment_id,
        'alt' => 'Slide background',
    ];
    if (is_array($attr)) {
        $attrs = array_merge($attrs, $attr);
    }
    $img = '<img';
    foreach ($attrs as $key => &$value) {
        $img .= ' ' . $key . '="' . $value . '"';
    }
    $img .= '>';

    return $img;
}

$GLOBALS['ls_action'] = [];

function ls_add_action($tag, $func, $p = 10)
{
    if (!isset($GLOBALS['ls_action'][$tag])) {
        $GLOBALS['ls_action'][$tag] = [];
    }
    $GLOBALS['ls_action'][$tag][] = $func;
}

function ls_do_action($tag, $arg = [])
{
    if (isset($GLOBALS['ls_action'][$tag])) {
        foreach ($GLOBALS['ls_action'][$tag] as $func) {
            call_user_func_array($func, $arg);
        }
    }
}

function ls_has_action($tag, $func_to_check = '')
{
    return isset($GLOBALS['ls_action'][$tag]);
}

$GLOBALS['ls_filter'] = [];

function ls_add_filter($tag, $func, $p = 10, $args = 1)
{
    if (!isset($GLOBALS['ls_filter'][$tag])) {
        $GLOBALS['ls_filter'][$tag] = [];
    }
    $GLOBALS['ls_filter'][$tag][] = $func;
}

function ls_apply_filters($tag, $value, $var = null)
{
    if (isset($GLOBALS['ls_filter'][$tag])) {
        foreach ($GLOBALS['ls_filter'][$tag] as $func) {
            if (null === $var) {
                $value = is_string($func) ? call_user_func($func, $value) : $func[0]->{$func[1]}($value);
            } else {
                $value = is_string($func) ? call_user_func($func, $value, $var) : $func[0]->{$func[1]}($value, $var);
            }
        }
    }

    return $value;
}

function ls_has_filter($tag, $func_to_check = '')
{
    return isset($GLOBALS['ls_filter'][$tag]);
}

$GLOBALS['ls_shortcode'] = [];

function ls_add_shortcode($tag, $func)
{
    $GLOBALS['ls_shortcode'][$tag] = $func;
}

function ls_shortcode_parse_atts($text)
{
    $atts = [];
    $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
    $text = preg_replace("/[\x{00a0}\x{200b}]+/u", ' ', $text);
    if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
        foreach ($match as $m) {
            if (!empty($m[1])) {
                $atts[Tools::strtolower($m[1])] = stripcslashes($m[2]);
            } elseif (!empty($m[3])) {
                $atts[Tools::strtolower($m[3])] = stripcslashes($m[4]);
            } elseif (!empty($m[5])) {
                $atts[Tools::strtolower($m[5])] = stripcslashes($m[6]);
            } elseif (isset($m[7]) and Tools::strlen($m[7])) {
                $atts[] = stripcslashes($m[7]);
            } elseif (isset($m[8])) {
                $atts[] = stripcslashes($m[8]);
            }
        }
    } else {
        $atts = ltrim($text);
    }

    return $atts;
}

function ls_do_shortcode_tag($m)
{
    // allow [[foo]] syntax for escaping a tag
    if ('[' == $m[1] && ']' == $m[6]) {
        return substr($m[0], 1, -1);
    }

    $tag = $m[2];
    $attr = ls_shortcode_parse_atts($m[3]);

    if (isset($m[5])) {
        // enclosing tag - extra parameter
        return $m[1] . call_user_func($GLOBALS['ls_shortcode'][$tag], $attr, $m[5], $tag) . $m[6];
    } else {
        // self-closing tag
        return $m[1] . call_user_func($GLOBALS['ls_shortcode'][$tag], $attr, null, $tag) . $m[6];
    }
}

function ls_do_shortcode($content)
{
    if (false === strpos($content, '[')) {
        return $content;
    }

    if (empty($GLOBALS['ls_shortcode'])) {
        return $content;
    }

    $pattern = '\[(\[?)(' . addcslashes(implode('|', array_keys($GLOBALS['ls_shortcode'])), '-') .
        ')(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';

    return preg_replace_callback("/$pattern/s", 'ls_do_shortcode_tag', $content);
}

function ls_shortcode_exists($tag)
{
    return isset($GLOBALS['ls_shortcode'][$tag]);
}

function ls_get_userdata($userid)
{
    $employee = $GLOBALS['employee'];

    return (object) [
        // TODO
        'user_nicename' => $employee->firstname . ' ' . $employee->lastname,
    ];
}

function ls_get_avatar_url($id_or_email, $args = null)
{
    $employee = $GLOBALS['employee'];

    return method_exists($employee, 'getImage') ? $employee->getImage() : 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
}

function ls_add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null)
{
    $screen_id = 'toplevel_page_' . $menu_slug;
    ls_add_action($screen_id, $function);

    return $screen_id;
}

function ls_add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function)
{
    $screen_id = $parent_slug . '_page_' . $menu_slug;
    ls_add_action($screen_id, $function);

    return $screen_id;
}

function ls_menu_page_url($menu_slug, $echo)
{
    return '?controller=AdminLayerSlider' . (!empty($GLOBALS['ls_token']) ? '&token=' . $GLOBALS['ls_token'] : '');
}

defined('MINUTE_IN_SECONDS') or define('MINUTE_IN_SECONDS', 60);
defined('HOUR_IN_SECONDS') or define('HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS);
defined('DAY_IN_SECONDS') or define('DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS);
defined('WEEK_IN_SECONDS') or define('WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS);
defined('MONTH_IN_SECONDS') or define('MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS);
defined('YEAR_IN_SECONDS') or define('YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS);

function ls_human_time_diff($from, $to = '')
{
    $to = $to ?: time();
    $diff = abs($to - $from);

    if ($diff >= YEAR_IN_SECONDS) {
        $count = max(1, round($diff / YEAR_IN_SECONDS));

        return sprintf(ls_n('%s year', '%s years', $count), $count);
    }
    if ($diff >= MONTH_IN_SECONDS) {
        $count = max(1, round($diff / MONTH_IN_SECONDS));

        return sprintf(ls_n('%s month', '%s months', $count), $count);
    }
    if ($diff >= WEEK_IN_SECONDS) {
        $count = max(1, round($diff / WEEK_IN_SECONDS));

        return sprintf(ls_n('%s week', '%s weeks', $count), $count);
    }
    if ($diff >= DAY_IN_SECONDS) {
        $count = max(1, round($diff / DAY_IN_SECONDS));

        return sprintf(ls_n('%s day', '%s days', $count), $count);
    }
    if ($diff >= HOUR_IN_SECONDS) {
        $count = max(1, round($diff / HOUR_IN_SECONDS));

        return sprintf(ls_n('%s hour', '%s hours', $count), $count);
    }
    $count = max(1, round($diff / MINUTE_IN_SECONDS));

    return sprintf(ls_n('%s min', '%s mins', $count), $count);
}

function ls_add_user_meta($user_id, $key, $value, $unique = false)
{
    return ls_add_option('u' . (int) $user_id . '_' . $key, $value);
}

function ls_get_user_meta($user_id, $key = '', $single = false)
{
    return ls_get_option('u' . (int) $user_id . '_' . $key);
}

function ls_update_user_meta($user_id, $key, $value, $prev_value = '')
{
    return ls_update_option('u' . (int) $user_id . '_' . $key, $value);
}

function ls_remote_retrieve_body($response)
{
    return $response['body'];
}

function ls_remote_get($url, $args = [])
{
    $http = array_merge(ls_http(), [
        'method' => 'GET',
        'timeout' => empty($args['timeout']) ? 5 : $args['timeout'],
    ]);

    if (!empty($args['body'])) {
        $url .= (false === strpos($url, '?') ? '?' : '&') . http_build_query($args['body']);
    }

    if (function_exists('curl_version')) {
        curl_setopt_array($ch = curl_init($url), [
            CURLOPT_HTTPHEADER => (array) $http['header'],
            CURLOPT_USERAGENT => $http['user_agent'],
            CURLOPT_MAXREDIRS => $http['max_redirects'],
            CURLOPT_TIMEOUT => $http['timeout'],
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => 1,
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);

        if (false !== $resp) {
            return $resp;
        }
    }

    $http['header'] = implode("\r\n", $http['header']);

    return in_array(ini_get('allow_url_fopen'), ['On', 'on', '1'])
        ? @call_user_func('file_get_contents', $url, false, stream_context_create(['http' => $http]))
        : false;
}

function ls_get_current_screen()
{
    return isset($GLOBALS['ls_screen']) ? $GLOBALS['ls_screen'] : (object) ['id' => '', 'base' => ''];
}

function ls_die($message = '', $title = '', $args = [])
{
    exit($title ? "$title - $message" : $message);
}

if ('en' === substr($GLOBALS['context']->language->language_code, 0, 2) || isset(${'_GET'}['en'])) {
    function ls__($text)
    {
        return $text;
    }
} else {
    function ls__($text)
    {
        static $cache = [];

        isset($GLOBALS['_MODULES']) || $GLOBALS['_MODULES'] = [];
        $_MODULES = &$GLOBALS['_MODULES'];

        if (!$cache) {
            $iso = substr($GLOBALS['context']->language->language_code, 0, 2);
            $translations = [
                _PS_THEME_DIR_ . 'modules/layerslider/translations/' . $iso . '.php',
                _PS_THEME_DIR_ . 'modules/layerslider/' . $iso . '.php',
                _PS_MODULE_DIR_ . 'layerslider/translations/' . $iso . '.php',
            ];
            foreach ($translations as $file) {
                if (file_exists($file)) {
                    include_once $file;
                    $_MODULES = $_MODULES ? array_merge($_MODULES, $GLOBALS['_MODULE']) : $GLOBALS['_MODULE'];
                }
            }
        }
        if (isset($cache[$text])) {
            return $cache[$text];
        }
        $key = '<{layerslider}prestashop>_' . call_user_func('md5', $text);

        return $cache[$text] = !empty($_MODULES[$key]) ? $_MODULES[$key] : $text;
    }
}

function ls_e($text)
{
    echo ls__($text);
}

function ls_n($single, $plural, $number)
{
    return ls__(1 == $number ? $single : $plural);
}

function ls_get_allowed_mime_types()
{
    return [
        // Image formats.
        'webp' => 'image/webp',
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'bmp' => 'image/bmp',
        'tiff|tif' => 'image/tiff',
        'ico' => 'image/x-icon',
        // Video formats.
        'asf|asx' => 'video/x-ms-asf',
        'wmv' => 'video/x-ms-wmv',
        'wmx' => 'video/x-ms-wmx',
        'wm' => 'video/x-ms-wm',
        'avi' => 'video/avi',
        'divx' => 'video/divx',
        'flv' => 'video/x-flv',
        'mov|qt' => 'video/quicktime',
        'mpeg|mpg|mpe' => 'video/mpeg',
        'mp4|m4v' => 'video/mp4',
        'ogv' => 'video/ogg',
        'webm' => 'video/webm',
        'mkv' => 'video/x-matroska',
        '3gp|3gpp' => 'video/3gpp', // Can also be audio
        '3g2|3gp2' => 'video/3gpp2', // Can also be audio
        // Text formats.
        'txt|asc|c|cc|h|srt' => 'text/plain',
        'csv' => 'text/csv',
        'tsv' => 'text/tab-separated-values',
        'ics' => 'text/calendar',
        'rtx' => 'text/richtext',
        'css' => 'text/css',
        'htm|html' => 'text/html',
        'vtt' => 'text/vtt',
        'dfxp' => 'application/ttaf+xml',
        // Audio formats.
        'mp3|m4a|m4b' => 'audio/mpeg',
        'ra|ram' => 'audio/x-realaudio',
        'wav' => 'audio/wav',
        'ogg|oga' => 'audio/ogg',
        'mid|midi' => 'audio/midi',
        'wma' => 'audio/x-ms-wma',
        'wax' => 'audio/x-ms-wax',
        'mka' => 'audio/x-matroska',
        // Misc application formats.
        'rtf' => 'application/rtf',
        'js' => 'application/javascript',
        'pdf' => 'application/pdf',
        'swf' => 'application/x-shockwave-flash',
        'class' => 'application/java',
        'tar' => 'application/x-tar',
        'zip' => 'application/zip',
        'gz|gzip' => 'application/x-gzip',
        'rar' => 'application/rar',
        '7z' => 'application/x-7z-compressed',
        'exe' => 'application/x-msdownload',
        'psd' => 'application/octet-stream',
        'xcf' => 'application/octet-stream',
        // MS Office formats.
        'doc' => 'application/msword',
        'pot|pps|ppt' => 'application/vnd.ms-powerpoint',
        'wri' => 'application/vnd.ms-write',
        'xla|xls|xlt|xlw' => 'application/vnd.ms-excel',
        'mdb' => 'application/vnd.ms-access',
        'mpp' => 'application/vnd.ms-project',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
        'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',
        'oxps' => 'application/oxps',
        'xps' => 'application/vnd.ms-xpsdocument',
        // OpenOffice formats.
        'odt' => 'application/vnd.oasis.opendocument.text',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odf' => 'application/vnd.oasis.opendocument.formula',
        // WordPerfect formats.
        'wp|wpd' => 'application/wordperfect',
        // iWork formats.
        'key' => 'application/vnd.apple.keynote',
        'numbers' => 'application/vnd.apple.numbers',
        'pages' => 'application/vnd.apple.pages',
    ];
}

function ls_check_filetype($filename, $mimes = null)
{
    if (empty($mimes)) {
        $mimes = ls_get_allowed_mime_types();
    }
    $type = false;
    $ext = false;

    foreach ($mimes as $ext_preg => $mime_match) {
        $ext_preg = '!\.(' . $ext_preg . ')$!i';
        if (preg_match($ext_preg, $filename, $ext_matches)) {
            $type = $mime_match;
            $ext = $ext_matches[1];
            break;
        }
    }

    return compact('ext', 'type');
}

function ls_sanitize_file_name($filename)
{
    $filename_raw = $filename;
    $special_chars = ['?', '[', ']', '/', '\\', '=', '<', '>', ':', ';', ',', "'", '"', '&', '$', '#', '*', '(', ')', '|', '~', '`', '!', '{', '}', '%', '+', chr(0)];
    $filename = preg_replace("#\x{00a0}#siu", ' ', $filename);
    $filename = str_replace($special_chars, '', $filename);
    $filename = str_replace(['%20', '+'], '-', $filename);
    $filename = preg_replace('/[\r\n\t -]+/', '-', $filename);
    $filename = trim($filename, '.-_');

    if (false === strpos($filename, '.')) {
        $mime_types = ls_get_allowed_mime_types();
        $filetype = ls_check_filetype('test.' . $filename, $mime_types);
        if ($filetype['ext'] === $filename) {
            $filename = 'unnamed-file.' . $filetype['ext'];
        }
    }

    // Split the filename into a base and extension[s]
    $parts = explode('.', $filename);

    // Return if only one extension
    if (count($parts) <= 2) {
        return $filename;
    }

    // Process multiple extensions
    $filename = array_shift($parts);
    $extension = array_pop($parts);
    $mimes = ls_get_allowed_mime_types();

    // Loop over any intermediate extensions. Postfix them with a trailing underscore
    // if they are a 2 - 5 character long alpha string not in the extension whitelist.
    foreach ((array) $parts as $part) {
        $filename .= '.' . $part;

        if (preg_match("/^[a-zA-Z]{2,5}\d?$/", $part)) {
            $allowed = false;
            foreach ($mimes as $ext_preg => $mime_match) {
                $ext_preg = '!^(' . $ext_preg . ')$!i';
                if (preg_match($ext_preg, $part)) {
                    $allowed = true;
                    break;
                }
            }
            if (!$allowed) {
                $filename .= '_';
            }
        }
    }
    $filename .= '.' . $extension;

    return $filename;
}

// MAGIC QUOTES
function ls_add_magic_quotes($array)
{
    foreach ((array) $array as $k => $v) {
        if (is_array($v)) {
            $array[$k] = ls_add_magic_quotes($v);
        } else {
            $array[$k] = addslashes($v);
        }
    }

    return $array;
}

function ls_magic_quotes()
{
    // Escape with wpdb.
    ${'_GET'} = ls_add_magic_quotes(${'_GET'});
    ${'_POST'} = ls_add_magic_quotes(${'_POST'});
    $_COOKIE = ls_add_magic_quotes($_COOKIE);
    $_SERVER = ls_add_magic_quotes($_SERVER);

    // Force REQUEST to be GET + POST.
    $_REQUEST = array_merge(${'_GET'}, ${'_POST'});
}

function ls_is_admin()
{
    return defined('_PS_BO_ALL_THEMES_DIR_');
}

function ls_get_current_user_id()
{
    return isset($GLOBALS['employee']) ? (int) $GLOBALS['employee']->id : 0;
}

function ls_get_option_key($option)
{
    return strtoupper(str_replace('-', '_', $option));
}

function ls_get_option($option, $default = false)
{
    if ('timezone_string' == $option) {
        return date_default_timezone_get();
    }
    $res = Configuration::get(ls_get_option_key($option));

    return false === $res ? $default : json_decode($res, true);
}

function ls_add_option($option, $value)
{
    return Configuration::updateValue(ls_get_option_key($option), json_encode($value));
}

function ls_update_option($option, $value)
{
    return Configuration::updateValue(ls_get_option_key($option), json_encode($value));
}

function ls_delete_option($option)
{
    return Configuration::deleteByName(ls_get_option_key($option));
}

$GLOBALS['ls_style'] = [];

function ls_enqueue_style($handle, $src = false, $deps = [], $ver = false, $media = 'all')
{
    if ($src) {
        $css = ['src' => $src, 'ver' => $ver, 'media' => $media];
        $GLOBALS['ls_style'][$handle] = $css;
    } elseif (isset($GLOBALS['ls_style'][$handle])) {
        $css = $GLOBALS['ls_style'][$handle];
    }
    if (isset($css)) {
        $ctrl = $GLOBALS['context']->controller;
        $v = $css['ver'] ? '?v=' . $css['ver'] : '';

        if (method_exists($ctrl, 'registerStylesheet')) {
            // @phpstan-ignore-next-line
            if ((LS_CSS_THEME_CACHE || _MEDIA_SERVER_1_) && false === strpos($css['src'], '://')) {
                $src = Tools::substr($css['src'], Tools::strlen(__PS_BASE_URI__));
            } else {
                $css['server'] = 'remote';
                $src = $css['src'] . $v;
            }
            $ctrl->registerStylesheet($handle, $src, $css);
        } else {
            $ctrl->css_files[$css['src'] . $v] = $css['media'];
        }
    }
}

function ls_register_style($handle, $src, $deps = [], $ver = false, $media = 'all')
{
    $GLOBALS['ls_style'][$handle] = ['src' => $src, 'ver' => $ver, 'media' => $media];
}

$GLOBALS['ls_script'] = [];

function ls_enqueue_script($handle, $src = false, $deps = [], $ver = false, $in_footer = false)
{
    if ($src) {
        $js = ['src' => $src, 'ver' => $ver, 'priority' => LS_PRIORITY];
        $GLOBALS['ls_script'][$handle] = $js;
    } elseif (isset($GLOBALS['ls_script'][$handle])) {
        $js = $GLOBALS['ls_script'][$handle];
    }
    if (isset($js) && !isset($js['added'])) {
        $GLOBALS['ls_script'][$handle]['added'] = true;
        $ctrl = $GLOBALS['context']->controller;
        $v = $js['ver'] ? '?v=' . $js['ver'] : '';

        if (LS_UNPACKED && preg_match('`/js/layerslider/(\w+/)*layerslider\.`', $js['src'])) {
            $js['src'] = str_replace('.js', '.unpacked.js', $js['src']);
        }
        if (method_exists($ctrl, 'registerJavascript')) {
            // @phpstan-ignore-next-line
            if (LS_JS_THEME_CACHE || _MEDIA_SERVER_1_) {
                $src = Tools::substr($js['src'], Tools::strlen(__PS_BASE_URI__));
            } else {
                $js['server'] = 'remote';
                $src = $js['src'] . $v;
            }
            $ctrl->registerJavascript($handle, $src, $js);
        } else {
            if (!ls_is_admin() && preg_match('`/(\w+\.)?((?:\w+\.)+js)$`', $js['src'], $match)) {
                foreach ($ctrl->js_files as $jsFile) {
                    if (false !== strpos($jsFile, $match[2])) {
                        return;
                    }
                }
            }
            $ctrl->js_files[] = $js['src'] . $v;
        }
    }
}

function ls_register_script($handle, $src, $deps = [], $ver = false, $in_footer = false)
{
    $GLOBALS['ls_script'][$handle] = ['src' => $src, 'ver' => $ver, 'priority' => LS_PRIORITY];
}

function ls_referer()
{
    $protocol = Tools::getShopProtocol();
    $id_shop = Configuration::getGlobalValue('PS_SHOP_DEFAULT');

    return $protocol . ('http://' === $protocol ? ShopUrl::getMainShopDomain($id_shop) : ShopUrl::getMainShopDomainSSL($id_shop));
}

function ls_admin_url($path = '')
{
    if (preg_match('/[&?]controller=(\w+)/', $path, $match)) {
        $path .= '&token=' . Tools::getAdminTokenLite($match[1]);
    }

    return $path;
}

function ls_add_query_arg(array $args, $url)
{
    $args && $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($args);

    return $url;
}

function ls_redirect($location)
{
    Tools::redirectAdmin(ls_admin_url($location));
}

function ls_http()
{
    $controller = $GLOBALS['context']->controller;
    $module_key = isset($controller->module->module_key) ? $controller->module->module_key : '';

    return [
        'user_agent' => "{$_SERVER['SERVER_SOFTWARE']} PrestaShop/" . _PS_VERSION_,
        'max_redirects' => 5,
        'header' => [
            'Cache-Control: no-cache',
            'Cookie: ' . call_user_func('md5', substr(_COOKIE_KEY_, 0, 24) . $module_key) . '=' . hash('sha256', _COOKIE_IV_ . date('Y-m-d')),
            'Pragma: no-cache',
            'Referer: ' . ls_referer(),
        ],
    ];
}

require_once _PS_MODULE_DIR_ . 'layerslider/classes/LsCache.php';

function ls_set_transient($transient, $value, $expiration = 0)
{
    $expiration = (int) $expiration;
    try {
        $result = LsCache::getInstance()->set($transient, $value, $expiration);
    } catch (Exception $ex) {
        $result = false;
    }

    return $result;
}

function ls_get_transient($transient)
{
    try {
        $result = LsCache::getInstance()->get($transient);
    } catch (Exception $ex) {
        $result = false;
    }

    return $result;
}

function ls_delete_transient($transient)
{
    try {
        $result = LsCache::getInstance()->delete($transient);
    } catch (Exception $ex) {
        $result = false;
    }

    return $result;
}

function ls_upload_dir()
{
    return [
        'basedir' => _PS_IMG_DIR_,
        'baseurl' => _PS_IMG_,
    ];
}

/* ADD ACTIONS / FILTERS */

function ls_get_hook_list()
{
    if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
        $hooks = [
            ['name' => ls__('- None -'), 'value' => ''],
            ['name' => ls__('Home'), 'value' => 'displayHome'],
            ['name' => ls__('Top of pages'), 'value' => 'displayTop'],
            ['name' => ls__('Banner'), 'value' => 'displayBanner'],
            ['name' => ls__('Navigation'), 'value' => 'displayNav'],
            ['name' => ls__('Top column blocks'), 'value' => 'displayTopColumn'],
            ['name' => ls__('Left column blocks'), 'value' => 'displayLeftColumn'],
            ['name' => ls__('Right column blocks'), 'value' => 'displayRightColumn'],
            ['name' => ls__('Footer'), 'value' => 'displayFooter'],
            ['name' => ls__('Creative Slider'), 'value' => 'displayCreativeSlider'],
        ];
    } else {
        $hooks = [
            ['name' => ls__('- None -'), 'value' => ''],
            ['name' => ls__('Home'), 'value' => 'displayHome'],
            ['name' => ls__('Top of pages'), 'value' => 'displayTop'],
            ['name' => ls__('Banner'), 'value' => 'displayBanner'],
            ['name' => ls__('Navigation 1'), 'value' => 'displayNav1'],
            ['name' => ls__('Navigation 2'), 'value' => 'displayNav2'],
            ['name' => ls__('Navigation full-width'), 'value' => 'displayNavFullWidth'],
            ['name' => ls__('Top wrapper'), 'value' => 'displayWrapperTop'],
            ['name' => ls__('Top content wrapper'), 'value' => 'displayContentWrapperTop'],
            ['name' => ls__('Top column blocks'), 'value' => 'displayTopColumn'],
            ['name' => ls__('Left column blocks'), 'value' => 'displayLeftColumn'],
            ['name' => ls__('Right column blocks'), 'value' => 'displayRightColumn'],
            ['name' => ls__('Bottom content wrapper'), 'value' => 'displayContentWrapperBottom'],
            ['name' => ls__('Bottom wrapper'), 'value' => 'displayWrapperBottom'],
            ['name' => ls__('Footer before'), 'value' => 'displayFooterBefore'],
            ['name' => ls__('Footer'), 'value' => 'displayFooter'],
            ['name' => ls__('Footer after'), 'value' => 'displayFooterAfter'],
            ['name' => ls__('After body opening tag'), 'value' => 'displayAfterBodyOpeningTag'],
            ['name' => ls__('Before body closing tag'), 'value' => 'displayBeforeBodyClosingTag'],
            ['name' => ls__('Creative Slider'), 'value' => 'displayCreativeSlider'],
        ];
    }

    return json_encode($hooks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

ls_add_action('wp_ajax_ls_update_hook', function () {
    $db = Db::getInstance();
    $id = Tools::getValue('id');
    $hook = preg_replace('/\W+/', '', Tools::getValue('hook', ''));
    $row = $db->getRow('SELECT `data`, `flag_deleted` FROM ' . _DB_PREFIX_ . 'layerslider WHERE `id` = ' . (int) $id);
    $data = json_decode($row['data'], true);

    if ($data && isset($data['properties'])) {
        $old_hook = isset($data['properties']['hook']) ? preg_replace('/\W+/', '', $data['properties']['hook']) : '';
        $data['properties']['hook'] = $hook;
        $result = $db->update('layerslider', [
            'data' => pSQL(json_encode($data), true),
            'date_m' => (int) time(),
        ], '`id` = ' . (int) $id, 1);

        if ($result) {
            if (!$row['flag_deleted']) {
                if ($old_hook && 1 == $db->getValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'layerslider_module WHERE `id_shop` > -1 AND `hook` LIKE "' . pSQL($old_hook) . '"')) {
                    LayerSlider::$instance->unregisterHook($old_hook);
                }
                if ($hook && 0 == $db->getValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'layerslider_module WHERE `id_shop` > -1 AND `hook` LIKE "' . pSQL($hook) . '"')) {
                    LayerSlider::$instance->registerHook($hook);
                }
            }
            $db->update('layerslider_module', [
                'hook' => pSQL($hook),
            ], '`id_slider` = ' . (int) $id);
            exit(json_encode(['hook' => $hook, 'success' => true]));
        }
    }
    exit(json_encode(['errorMsg' => ls__("Database error, can't update hook!"), 'success' => false]));
});

ls_add_action('wp_ajax_ls_get_shop_params', function () {
    require_once _PS_MODULE_DIR_ . 'layerslider/classes/PSOpts.php';
    $lang = (object) [
        'name' => 'lang',
        'title' => ls__('Language'),
        'desc' => ls__('Slider will appear only on the selected language. (In case of multilanguage)'),
        'opts' => [(object) ['name' => ls__('- All -'), 'id_lang' => 0, 'active' => 1]],
    ];
    $lang->opts = array_merge($lang->opts, Language::getLanguages());
    $shop = (object) [
        'name' => 'shop',
        'title' => ls__('Shop'),
        'desc' => ls__('Slider will appear only on the selected shop. (In case of Multi-shops)'),
        'opts' => [(object) ['name' => ls__('- All -'), 'id_shop' => 0, 'active' => 1]],
    ];
    $shop->opts = array_merge($shop->opts, Shop::getShops());
    $cats = (object) [
        'name' => 'cats',
        'title' => ls__('Show slider on these <br> Categories &amp; Pages'),
        'desc' => ls__('Use Ctrl to select multiple categories or pages.'),
        'opts' => PSOpts::getCategories(),
    ];
    $pages = (object) [
        'name' => 'pages',
        'title' => ls__('Show slider on these <br> Categories &amp; Pages'),
        'desc' => ls__('Use Ctrl to select multiple categories or pages.'),
        'opts' => PSOpts::getCMSCategories(),
    ];
    $position = (object) [
        'name' => 'position',
        'title' => ls__('Ordering'),
        'desc' => ls__('Sliders in the same module position can be ordered with this option. Use lower value if you want to move this slider in above the others.'),
    ];
    $groups = (object) [
        'name' => 'groups',
        'title' => ls__('Groups'),
        'desc' => ls__('PrestaShop has three default customer groups<br><i>Visitor</i> - All persons without a customer account or customers that are not logged in.<br><i>Guest</i> - All persons who placed an order through Guest Checkout.<br><i>Customer</i> - All persons who created an account on this site.'),
        'opts' => [(object) ['name' => ls__('- All -'), 'id_group' => 0]],
    ];
    $groups->opts = array_merge($groups->opts, Group::getGroups($GLOBALS['language']->id));

    $params = new stdClass();
    $params->lang = $lang;
    $params->shop = $shop;
    $params->cats = $cats;
    $params->pages = $pages;
    $params->position = $position;
    $params->groups = $groups;
    exit(json_encode($params));
});

// override post defaults
function ls_override_post_defaults($defaults)
{
    $defaults['slider']['postOrderBy'] = [
        'value' => 'date_add',
        'keys' => 'post_orderby',
        'options' => [
            'date_add' => ls__('Date Created'),
            'date_upd' => ls__('Last Modified'),
            'position' => ls__('Popularity'),
            'quantity' => ls__('Sold quantity'),
            'reduction' => ls__('Special offer'),
            'name' => ls__('Product name'),
            'price' => ls__('Product price'),
            'rand' => ls__('Random'),
        ],
        'props' => ['meta' => 1],
    ];
    // $defaults['slider']['hook'] = array(
    //     'value' => '',
    //     'keys' => 'hook',
    //     'props' => array('meta' => 1),
    // );

    return $defaults;
}
ls_add_filter('layerslider_override_defaults', 'ls_override_post_defaults');

function ls_pre_parse_defaults($data)
{
    $link = $GLOBALS['context']->link;

    if (!empty($data['properties']['yourlogo']) && '/' === $data['properties']['yourlogo'][0]) {
        $data['properties']['yourlogo'] = $link->getMediaLink($data['properties']['yourlogo']);
    }
    if (!empty($data['properties']['backgroundimage']) && '/' === $data['properties']['backgroundimage'][0]) {
        $data['properties']['backgroundimage'] = $link->getMediaLink($data['properties']['backgroundimage']);
    }
    foreach ($data['layers'] as &$slide) {
        if (!empty($slide['properties']['background']) && '/' === $slide['properties']['background'][0]) {
            $slide['properties']['background'] = $link->getMediaLink($slide['properties']['background']);
        }
        if (!empty($slide['properties']['thumbnail']) && '/' === $slide['properties']['thumbnail'][0]) {
            $slide['properties']['thumbnail'] = $link->getMediaLink($slide['properties']['thumbnail']);
        }
        if (!empty($slide['sublayers'])) {
            foreach ($slide['sublayers'] as &$layer) {
                if (!empty($layer['image']) && '/' === $layer['image'][0]) {
                    $layer['image'] = $link->getMediaLink($layer['image']);
                }
                if (!empty($layer['poster']) && '/' === $layer['poster'][0]) {
                    $layer['poster'] = $link->getMediaLink($layer['poster']);
                }
            }
        }
    }

    return $data;
}
ls_add_filter('layerslider_pre_parse_defaults', 'ls_pre_parse_defaults');

function ls_filter_get_template_args($body_args)
{
    if ($license_key = Configuration::getGlobalValue('LS_LICENSE')) {
        $body_args['license'] = $license_key;
        $body_args['domain'] = ShopUrl::{'getMainShopDomain' . ('https://' === Tools::getShopProtocol() ? 'SSL' : '')}(
            Configuration::getGlobalValue('PS_SHOP_DEFAULT')
        );
    }

    return $body_args;
}
ls_add_filter('ls_download_slider/body_args', 'ls_filter_get_template_args');

function ls_post_parse_defaults($data)
{
    if (!empty($data['properties']['attrs'])) {
        $data['properties']['attrs']['skinsPath'] = $GLOBALS['context']->link->getMediaLink(_MODULE_DIR_ . 'layerslider/views/css/layerslider/skins/');
    }

    return $data;
}
ls_add_filter('layerslider_post_parse_defaults', 'ls_post_parse_defaults');

function ls_add_hook($id, $hook, $id_lang, $id_shop, $position, $pages)
{
    $db = Db::getInstance();

    if ($hook) {
        $hook_count = (int) $db->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'layerslider_module WHERE `id_shop` > -1 AND `hook` LIKE "' . pSQL($hook) . '"'
        );
        if (0 === $hook_count) {
            LayerSlider::$instance->registerHook($hook);
        }
    }
    $db->update('layerslider_module', [
        'hook' => pSQL($hook),
        'id_lang' => (int) $id_lang,
        'id_shop' => (int) $id_shop,
        'position' => (int) $position,
        'pages' => pSQL($pages),
    ], '`id_slider` = ' . (int) $id);
}

function ls_remove_hook($id, $hook)
{
    $db = Db::getInstance();
    $old = $db->getRow('
        SELECT h.`id_hook`, m.`hook` FROM ' . _DB_PREFIX_ . 'layerslider_module m
        LEFT JOIN ' . _DB_PREFIX_ . 'hook h ON m.`hook` = h.`name`
        WHERE m.`id_slider` = ' . (int) $id
    );
    if ($old && $old['hook'] && $old['hook'] !== $hook) {
        $old_hook_count = (int) $db->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'layerslider_module WHERE `id_shop` > -1 AND `hook` LIKE "' . pSQL($old['hook']) . '"'
        );
        if (1 === $old_hook_count) {
            LayerSlider::$instance->unregisterHook($old['id_hook']);
        }
    }
}

function ls_get_pages($cats, $pages, $groups)
{
    $obj = [
        'cat' => [],
        'prod' => [],
        'cms' => [],
        'page' => [],
        'groups' => &$groups,
    ];

    if (in_array('all', $cats)) {
        $obj['cat'] = $obj['prod'] = 'all';
    } else {
        foreach ($cats as $val) {
            if (preg_match('/^[cp]-\d+$/', $val)) {
                $v = explode('-', $val);
                $obj['c' == $v[0] ? 'cat' : 'prod'][] = $v[1];
            } elseif ($val) {
                $obj[$val] = 1;
            }
        }
    }

    if (in_array('all', $pages)) {
        $obj['cms'] = $obj['page'] = 'all';
    } else {
        foreach ($pages as $val) {
            if (preg_match('/^(\w+)-\d+$/', $val)) {
                $v = explode('-', $val);
                switch ($v[0]) {
                    case 'c':
                        $obj['cms'][] = $v[1];
                        break;
                    case 'p':
                        $obj['page'][] = $v[1];
                        break;
                    default:
                        $obj[$v[0]][] = $v[1];
                        break;
                }
            } elseif ($val) {
                $obj[$val] = 1;
            }
        }
    }

    return json_encode($obj);
}

ls_add_action('wp_ajax_ls_save_slider', function () {
    $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
    $sd = isset($_REQUEST['sliderData']) ? $_REQUEST['sliderData'] : [];

    if (!$id || empty($sd)) {
        return;
    }
    $props = isset($sd['properties']) ? json_decode(stripslashes(html_entity_decode($sd['properties'])), true) : [];
    $hook = isset($props['hook']) ? $props['hook'] : '';
    $id_lang = isset($props['lang']) ? $props['lang'] : 0;
    $id_shop = isset($props['shop']) ? $props['shop'] : 0;
    $position = isset($props['position']) ? $props['position'] : 10;
    $pages = ls_get_pages(
        isset($props['cats']) ? $props['cats'] : [],
        isset($props['pages']) ? $props['pages'] : [],
        isset($props['groups']) ? $props['groups'] : []
    );
    ls_remove_hook($id, $hook);
    ls_add_hook($id, $hook, $id_lang, $id_shop, $position, $pages);
});

function ls_get_modules()
{
    $modules = [];
    $dirs = glob(_PS_MODULE_DIR_ . '*', GLOB_ONLYDIR);
    foreach ($dirs as $dir) {
        $modules[basename($dir)] = 1;
    }

    return $modules;
}

ls_add_action('wp_ajax_ls_template_store', function () {
    if ($license = Configuration::getGlobalValue('LS_LICENSE')) {
        exit(json_encode(['key' => $license, 'success' => 1]));
    }
    $msg = ls__('Premium start templates are only available after license activation.');
    $btn = ' <a href="' . $GLOBALS['context']->link->getAdminLink('AdminLayerSlider') . '&action=activate" style="float:none; display:inline-block">' . ls__('Activate') . '</a>';
    exit(json_encode(['msg' => $msg . $btn, 'success' => 0]));
});
