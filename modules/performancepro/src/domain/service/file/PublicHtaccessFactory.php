<?php
/**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Academic Free License (AFL 3.0)
 *
 * Additionally, this module is subject to a proprietary End User License Agreement (EULA).
 * For the full copyright, open source license, and EULA information, please view the LICENSE
 * that were distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\Module\PerformancePro\domain\service\file;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class PublicHtaccessFactory
{
    private $editHtaccess;

    public function __construct($editHtaccess)
    {
        $this->editHtaccess = $editHtaccess;
    }

    public function create(): void
    {
        $cacheControl = \Configuration::get('PP_HTACCESS_CACHE_CONTROL');

        $deflate = \Configuration::get('PP_HTACCESS_DEFLATE');

        if ($cacheControl || $deflate) {
            $this->editHtaccess->setContent('<IfModule mod_headers.c>');

            if ($cacheControl) {
                $this->editHtaccess->setContent(
                    '    <FilesMatch "\\.(ttf|woff2?|css|js|gif|png|jpe?g|webp|ico|svgz?|pdf)$">
    Header set Cache-Control "max-age=31536000, public"
    </FilesMatch>'
                );
            }

            if ($deflate) {
                $this->editHtaccess->setContent('    <FilesMatch "\\.(ttf|woff2?|css|js|xml|gz|html)$">
    Header append Vary: Accept-Encoding
    </FilesMatch>');
            }

            $this->editHtaccess->setContent('</IfModule>');

            if ($deflate) {
                $this->editHtaccess->setContent('<IfModule mod_deflate.c>
    SetOutputFilter DEFLATE
    SetEnvIfNoCase Request_URI \\.(?:gif|png|jpe?g|webp)$ no-gzip dont-vary
    Header append Vary User-Agent env=!dont-vary
</IfModule>');
            }

            $this->editHtaccess->replaceContent();
        } else {
            $this->editHtaccess->reset();
        }
    }
}
