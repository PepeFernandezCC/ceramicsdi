<?php
/*
 * Since 2007 PayPal
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author Since 2007 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  @copyright PayPal
 *
 */

namespace PaypalAddons\classes\API;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HttpResponse
{
    /** @var int */
    protected $code;
    /** @var mixed */
    protected $content;
    /** @var array */
    protected $headers = [];

    /** @return int|null*/
    public function getCode()
    {
        return $this->code;
    }

    /** @return self*/
    public function setCode(int $code)
    {
        $this->code = $code;

        return $this;
    }

    /** @return mixed*/
    public function getContent()
    {
        return $this->content;
    }

    /** @return self*/
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /** @return array*/
    public function getHeaders()
    {
        return $this->headers;
    }

    /** @return self*/
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }
}
