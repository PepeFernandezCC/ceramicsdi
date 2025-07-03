{*
* 2007-2020 PrestaShop
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
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="task_frequency_info_block">
    <div class="title">
        {l s='Crontab commands & examples' mod='exportproducts'}
    </div>
    <div class="row_info">
    <span class="command">
      */5 * * * *
    </span>
        <span class="example">
      {l s='at every 5th minute' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      30 4 1 * 0,6
    </span>
        <span class="example">
      {l s='At 4:30 on the 1st day of every month, plus on Sun and Sat' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      *
    </span>
        <span class="example">
      {l s='any value' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      ,
    </span>
        <span class="example">
      {l s='value list separator' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      -
    </span>
        <span class="example">
      {l s='range of values' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      /
    </span>
        <span class="example">
      {l s='step values' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      @yearly
    </span>
        <span class="example">
      {l s='(non-standard)' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      @annually
    </span>
        <span class="example">
      {l s='(non-standard)' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      @monthly
    </span>
        <span class="example">
      {l s='(non-standard)' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      @weekly
    </span>
        <span class="example">
      {l s='(non-standard)' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      @daily
    </span>
        <span class="example">
      {l s='(non-standard)' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      @hourly
    </span>
        <span class="example">
      {l s='(non-standard)' mod='exportproducts'}
    </span>
    </div>
    <div class="row_info">
    <span class="command">
      @reboot
    </span>
        <span class="example">
      {l s='(non-standard)' mod='exportproducts'}
    </span>
    </div>
</div>