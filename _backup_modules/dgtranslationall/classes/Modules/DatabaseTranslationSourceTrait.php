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
 * @copyright Copyright 2023 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

trait DatabaseTranslationSourceTrait
{
    /**
     * @param mixed[] $domainTranslations
     * @return mixed[]|null
     */
    protected function extractDomainTranslationTranslations($domainTranslations)
    {
        if (!isset($domainTranslations['data'])) {
            return null;
        }

        $defaults = array();
        $translations = array();

        foreach ($domainTranslations['data'] as $domainTranslation) {
            $domain = implode('', $domainTranslation['tree_domain']);
            $original = $domainTranslation['default'];

            $translation = null;
            if (isset($domainTranslation['database'])) {
                $translation = $domainTranslation['database'];
            } elseif (isset($domainTranslation['project'])) {
                $translation = $domainTranslation['project'];
            } elseif (isset($domainTranslation['user'])) {
                $translation = $domainTranslation['user'];
            }

            $key = "<$domain>" . md5($original);

            $translations[$key] = $translation;
            $defaults[$key] = $original;
        }

        return array(
            'defaults' => $defaults,
            'translations' => $translations
        );
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    protected function getContainer()
    {
        if (class_exists('\PrestaShop\PrestaShop\Adapter\SymfonyContainer')) {
            return \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance();
        }

        return Context::getContext()->controller->getContainer();
    }
}
