{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{extends file='page.tpl'}

{block name="breadcrumb"}{/block}

{block name='page_title'}

{/block}
        
{capture assign="errorContent"}
<div class="error-container">
  <div class="error-message">
    <h2>{l s='This page could not be found' d='Shop.Theme.Global'}</h2>
  </div>

  <div class="error-content">

    <div class="error-notfound-text">
      <p>{l s='Try to search our catalog, you may find what you are looking for!' d='Shop.Theme.Global'}</p>
      <div style="padding-top: 15px; text-align:center">
        <a href="https://ceramicconnection.com/es/baldosas-ceramicas" class="catalog-button">&gt; {l s='Catalog' d='Admin.Navigation.Menu'}</a>
      </div>
    </div>
    
    <div class="error-image">
      <img src="https://ceramicconnection.com/img/tmp/category_2.jpg" alt="404 not found image">
    </div>
  
  </div>


</div>



{/capture}

{block name='page_content_container'}
  {include file='errors/not-found.tpl' errorContent=$errorContent}
{/block}