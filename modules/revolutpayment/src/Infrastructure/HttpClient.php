<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Revolut
 * @copyright Since 2020 Revolut
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */

namespace Revolut\PrestaShop\Infrastructure;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Revolut\Plugin\Services\Http\HttpClientInterface;

class HttpClient implements HttpClientInterface
{
    public function request(string $method, string $url, array $options = []): array
    {
        $ch = curl_init();

        $headers = [];

        if (!empty($options['headers'])) {
            foreach ($options['headers'] as $key => $value) {
                $headers[] = "$key: $value";
            }
        }

        if (!empty($options['query'])) {
            $query = http_build_query($options['query']);
            $url .= ((strpos($url, '?') !== false) ? '&' : '?') . $query;
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => self::withUserAgent($headers),
            CURLOPT_TIMEOUT => 10,
        ]);

        if (empty($options['body'])) {
            $options['body'] = [];
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);

        $body = curl_exec($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($error) {
            return [
                'status' => 0,
                'body' => $error,
                'error' => true,
            ];
        }

        return [
            'status' => $status,
            'body' => $body,
        ];
    }

    public static function withUserAgent(array $headers)
    {
        $headers[] = 'User-Agent: Revolut Payment Gateway/' . REVOLUT_MODULE_VERSION . ' PrestaShop/' . _PS_VERSION_ . ' PHP/' . PHP_VERSION;

        return $headers;
    }

    public function getStatusCode(array $response): int
    {
        return $response['status'] ?? 0;
    }

    public function getBody(array $response): array
    {
        return json_decode($response['body'], true) ?? [];
    }

    public function isError(array $response): bool
    {
        return $response['error'] ?? false;
    }

    public function getErrorMessage(array $response): ?string
    {
        return $response['body'] ?? null;
    }
}
