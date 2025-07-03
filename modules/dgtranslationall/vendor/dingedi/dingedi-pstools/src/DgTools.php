<?php
/**
 * License limited to a single site, for use on another site please purchase a license for this module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Dingedi.com
 * @copyright Copyright 2020 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */

namespace Dingedi\PsTools;

use Dingedi\PsTools\Exception\MissingParametersException;

class DgTools
{

    /**
     * @param string $controller
     * @param mixed[] $params
     * @return string
     */
    static function getAdminLink($controller, $params = array())
    {
        $link = \Context::getContext()->link;

        if ($link === null) {
            return '';
        }

        if (DgShopInfos::isPrestaShop16()) {
            $link = $link->getAdminLink($controller, true);

            foreach ($params as $k => $v) {
                $link .= "&" . $k . "=" . $v;
            }
        } else {
            $link = $link->getAdminLink($controller, true, [], $params);
        }

        return $link;
    }

    /**
     * @param mixed $array
     * @deprecated
     * @param string $key
     * @param string $value
     * @return mixed[]
     */
    static function searchArray($array, $key, $value)
    {
        $results = array();
        if (is_array($array)) {
            if (@$array[$key] == $value) {
                $results[] = $array;
            } else {
                foreach ($array as $subarray) {
                    $results = array_merge($results, self::searchArray($subarray, $key, $value));
                }
            }
        }

        return $results;
    }

    /**
     * @deprecated
     * @param mixed[] $array
     * @param string $key
     * @param string $value
     * @return mixed[]|null
     */
    static function searchSubArray($array, $key, $value)
    {
        foreach ($array as $subarray) {
            if (isset($subarray[$key]) && $subarray[$key] == $value)
                return $subarray;
        }

        return null;
    }

    /**
     * @deprecated
     * @param mixed[] $data
     * @param string $by_column
     * @return mixed[]
     */
    static function arrayGroup($data, $by_column)
    {
        $result = array();

        foreach ($data as $item) {
            $column = $item[$by_column];
            if (isset($result[$column])) {
                $result[$column][] = $item;
            } else {
                $result[$column] = array($item);
            }
        }

        return $result;
    }

    /**
     * @throws \JsonException
     * @param string|mixed[] $data
     * @param int $responseCode
     * @return void
     */
    static function jsonResponse($data = [], $responseCode = 200, $key = 'success')
    {
        if (is_string($data)) {
            $data = array(
                $key      => true,
                'message' => $data
            );
        }

        if (!isset($data['error']) && $key === 'error') {
            $data['error'] = true;
        }

        ob_end_clean();
        header('Content-type: application/json');
        http_response_code($responseCode);
        echo json_encode($data, 0);
        die();
    }

    /**
     * @throws \JsonException
     * @param string|mixed[] $data
     * @param int $httpCode
     * @return void
     */
    static function jsonError($data = [], $httpCode = 400)
    {
        self::jsonResponse($data, $httpCode, 'error');
    }

    /**
     * @throws \Exception
     * @param mixed[]|int $language
     * @return string
     */
    static function getLocale($language)
    {
        if (is_int($language)) {
            $language = \Language::getLanguage($language);
        }

        if (isset($language['iso_code'])) {
            return $language['iso_code'];
        }

        if (isset($language['locale'])) {
            if (\Tools::strlen($language['locale']) > 2) {
                return \Tools::substr($language['locale'], 0, 2);
            }

            return $language['locale'];
        }

        throw new \Exception('Error while detecting iso code for language ' . $language['id_lang']);
    }

    /**
     * @param string $string
     * @return bool
     */
    static function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * @throws MissingParametersException
     * @param string|mixed[] $queryParameters
     * @param mixed[] $needle
     * @return bool
     */
    static function hasParameters($queryParameters, $needle)
    {
        if (!is_array($queryParameters)) {
            $queryParameters = array($queryParameters);
        }

        $missing = array();

        foreach ($needle as $i) {
            if (!array_key_exists($i, $queryParameters)) {
                $missing[] = $i;
            }
        }

        if (!empty($missing)) {
            throw new \Dingedi\PsTools\Exception\MissingParametersException('Some parameters are missing in the query: ' . implode(', ', $missing));
        }

        return true;
    }

    /**
     * @param mixed $default_value
     * @return mixed
     * @param string $key
     */
    public static function getValue($key, $default_value = '')
    {
        if (\Dingedi\PsTools\DgShopInfos::isPrestaShop17()) {
            return \Configuration::get($key, null, null, null, $default_value);
        } else {
            $value = \Configuration::get($key, null, null, null);

            if ($value === false) {
                return $default_value;
            }

            return $value;
        }
    }
}
