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



{if isset($listing.rendered_facets)}

    {* PLANATEC *}

    {$condition = false}

    {if $category.id != $CATEGORY_CERAMICA_ID and $category.id != $CATEGORY_INSTALACION_Y_MONTAJE_ID and $category.id != $CATEGORY_AZULEJOS and $category.id != $CATEGORY_OTROS_MATERIALES_ID}

        {$condition = true}

    {/if}

    {* END PLANATEC *}

    <div id="search_filters_wrapper" style="display: flex;flex-direction: column-reverse;">

        <div {* PLANATEC *}class="custom-filter-mobile"{* END PLANATEC *}>

            {$listing.rendered_facets nofilter}

        </div>

        

        <div class="hidden-filters">

            <div id="search_filters">

                <div id="custom-filter-wrapper">

                    <span>{l s='Filter' d='Shop.Theme.Actions'}</span>

                    <button class="btn">

                        <i class="material-icons d-inline">tune</i>

                    </button>

                </div>

            </div>

        </div>

       

    </div>

{/if}

