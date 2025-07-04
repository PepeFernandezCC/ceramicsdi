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
{block name='step'}
    <section  id    = "{$identifier}"
              class = "{[
              'checkout-step'   => true,
              '-current'        => $step_is_current,
              '-reachable'      => $step_is_reachable,
              '-complete'       => $step_is_complete,
              'js-current-step' => $step_is_current
              ]|classnames}"
    >
        <h1 class="step-title js-step-title h3" {* PLANATEC *}style="display: none;" {* END PLANATEC *}>
            <i class="material-icons rtl-no-flip done">&#xE876;</i>
            <span class="step-number">{$position}</span>
            {$title}
            <span class="step-edit text-muted"><i class="material-icons edit">mode_edit</i> {l s='Edit' d='Shop.Theme.Actions'}</span>
        </h1>

        <div class="content">
            {block name='step_content'}DUMMY STEP CONTENT{/block}
        </div>
    </section>
{/block}
