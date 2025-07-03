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

namespace Dingedi\PsTools;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DgConfiguration implements \JsonSerializable
{
    /**
     * @var string
     */
    public $key;
    /**
     * @var mixed[]
     */
    public $params;

    /**
     * @param string $key
     */
    public function __construct($key = '', array $params = [])
    {
        $key = (string) $key;
        $this->key = $key;
        $this->params = $params;
    }

    /**
     * @param mixed $value
     * @param string $key
     */
    private function getDefaultValue($key, $value = false)
    {
        $key = (string) $key;
        return isset($this->params[$key]) ? $this->params[$key] : $value;
    }

    /**
     * @param string $key
     * @return string
     */
    private function buildKey($key)
    {
        $key = (string) $key;
        return 'dingedi_' . $this->key . '_' . $key;
    }

    /**
     * @param mixed $value
     * @return mixed
     * @param string $key
     */
    public function get($key, $value = false)
    {
        $defaultValue = $this->getDefaultValue($key, $value);
        $defaultType = gettype($defaultValue);

        $configValue = DgTools::getValue($this->buildKey($key), $defaultValue);

        if ($defaultType === 'boolean') {
            $configValue = filter_var($configValue, FILTER_VALIDATE_BOOLEAN);
        } else {
            settype($configValue, $defaultType);
        }

        return $configValue;
    }

    /**
     * @param mixed $value
     * @param string $key
     */
    public function update($key, $value)
    {
        $ckey = $this->buildKey($key);

        if (\Shop::isFeatureActive() && \Shop::getContextShopGroup()->id === null) {
            return \Configuration::updateGlobalValue($ckey, $value);
        }

        return \Configuration::updateValue($ckey, $value);
    }

    /**
     * @param mixed[] $data
     * @return void
     */
    public function updateFromArray($data)
    {
        $data = $this->beforeSave($data);

        foreach ($data as $k => $v) {
            if (isset($this->params[$k])) {
                $this->update($k, $v);
            }
        }
    }

    /**
     * @return void
     */
    public function install()
    {
        foreach ($this->params as $key => $value) {
            $this->update($key, $this->get($key));
        }
    }

    /**
     * @return void
     */
    public function uninstall()
    {
        foreach ($this->params as $key => $value) {
            \Configuration::deleteByName($this->buildKey($key));
        }
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [];

        foreach ($this->params as $k => $v) {
            $data[$k] = $this->get($k);
        }

        $data = $this->beforeSerialize($data);

        return $data;
    }

    /**
     * @return mixed[]
     */
    protected function beforeSave($params)
    {
        return $params;
    }

    /**
     * @return mixed[]
     */
    protected function beforeSerialize($params)
    {
        return $params;
    }
}
