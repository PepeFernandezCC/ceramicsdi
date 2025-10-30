<?php
/**
 * UrlRedirects
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2023 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class CheckStatus
{
    public static function sendResponse($response, $status_code = 200)
    {
        if (!headers_sent()) {
            self::statusResponseCode($status_code);
            header('Content-Type: application/json');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
        }
        if (!empty($response)) {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                exit(json_encode($response));
            } else {
                exit(json_encode($response));
            }
        } elseif (trim($response) != '') {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                exit(json_encode($response));
            } else {
                exit(json_encode($response));
            }
        } elseif (!is_null($response)) {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                exit(json_encode($response));
            } else {
                exit(json_encode($response));
            }
        } else {
            exit;
        }
    }

    protected static function statusResponseCode($status_code = null)
    {
        if (!function_exists('http_response_code')) {
            if ($status_code !== null) {
                switch ($status_code) {
                    case 100:
                        $msg = 'Continue';
                        break;
                    case 101:
                        $msg = 'Switching Protocols';
                        break;
                    case 200:
                        $msg = 'OK';
                        break;
                    case 201:
                        $msg = 'Created';
                        break;
                    case 202:
                        $msg = 'Accepted';
                        break;
                    case 203:
                        $msg = 'Non-Authoritative Information';
                        break;
                    case 204:
                        $msg = 'No Content';
                        break;
                    case 205:
                        $msg = 'Reset Content';
                        break;
                    case 206:
                        $msg = 'Partial Content';
                        break;
                    case 300:
                        $msg = 'Multiple Choices';
                        break;
                    case 301:
                        $msg = 'Moved Permanently';
                        break;
                    case 302:
                        $msg = 'Moved Temporarily';
                        break;
                    case 303:
                        $msg = 'See Other';
                        break;
                    case 304:
                        $msg = 'Not Modified';
                        break;
                    case 305:
                        $msg = 'Use Proxy';
                        break;
                    case 400:
                        $msg = 'Bad Request';
                        break;
                    case 401:
                        $msg = 'Unauthorized';
                        break;
                    case 402:
                        $msg = 'Payment Required';
                        break;
                    case 403:
                        $msg = 'Forbidden';
                        break;
                    case 404:
                        $msg = 'Not Found';
                        break;
                    case 405:
                        $msg = 'Method Not Allowed';
                        break;
                    case 406:
                        $msg = 'Not Acceptable';
                        break;
                    case 407:
                        $msg = 'Proxy Authentication Required';
                        break;
                    case 408:
                        $msg = 'Request Time-out';
                        break;
                    case 409:
                        $msg = 'Conflict';
                        break;
                    case 410:
                        $msg = 'Gone';
                        break;
                    case 411:
                        $msg = 'Length Required';
                        break;
                    case 412:
                        $msg = 'Precondition Failed';
                        break;
                    case 413:
                        $msg = 'Request Entity Too Large';
                        break;
                    case 414:
                        $msg = 'Request-URI Too Large';
                        break;
                    case 415:
                        $msg = 'Unsupported Media Type';
                        break;
                    case 500:
                        $msg = 'Internal Server Error';
                        break;
                    case 501:
                        $msg = 'Not Implemented';
                        break;
                    case 502:
                        $msg = 'Bad Gateway';
                        break;
                    case 503:
                        $msg = 'Service Unavailable';
                        break;
                    case 504:
                        $msg = 'Gateway Time-out';
                        break;
                    case 505:
                        $msg = 'HTTP Version not supported';
                        break;
                    default:
                        $msg = 'Unknown http status code "' . htmlentities($status_code) . '"';
                        break;
                }
                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' ' . $status_code . ' ' . $msg);
                $GLOBALS['http_response_code'] = $status_code;
            } else {
                $status_code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
            }

            return $status_code;
        } else {
            http_response_code($status_code);
        }
    }
}
