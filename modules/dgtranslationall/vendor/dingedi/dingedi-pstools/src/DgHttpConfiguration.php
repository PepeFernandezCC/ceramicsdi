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

abstract class DgHttpConfiguration extends \Dingedi\PsTools\DgConfiguration
{
    const LAST_CHECKED = "last_checked_date";
    /**
     * @var int
     */
    public $expireInMinutes;

    /**
     * @param string $key
     */
    public function __construct($key = '', array $params = [], $expireInMinutes = 1)
    {
        $key = (string) $key;
        $this->expireInMinutes = $expireInMinutes;

        $params[self::LAST_CHECKED] = '';

        parent::__construct($key, $params);
    }

    /**
     * @return string
     */
    public function refresh()
    {
        $last_checked = date('Y-m-d H:i:s');
        $this->update(self::LAST_CHECKED, $last_checked);

        return $last_checked;
    }

    /**
     * @param mixed $value
     * @return mixed
     * @param string $key
     */
    public function get($key, $value = false)
    {

        if ($key !== self::LAST_CHECKED) {
            $last_checked = self::get(self::LAST_CHECKED);

            if ($last_checked === '' || !\Validate::isDate($last_checked)) {
                $this->refresh();
            }


            if (\Validate::isDate($last_checked)) {
                $now = date('Y-m-d H:i:s');
                $d1 = new \DateTime($now);
                $d2 = new \DateTime($last_checked);

                if ($d1->diff($d2)->i >= $this->expireInMinutes) {
                    $this->refresh();
                }
            }
        }


        return parent::get($key, $value);
    }
}
