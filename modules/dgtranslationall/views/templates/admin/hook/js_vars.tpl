{**
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
 *}
<script type="text/javascript">
  if (typeof dg_base_url === 'undefined') {
    var dg_base_url = "{$dg_base_url}";
  }
  if (typeof ps_base_uri === 'undefined') {
    var ps_base_uri = "{$ps_base_uri}";
  }

  if (typeof ps_id_shop === 'undefined') {
    var ps_id_shop = "{$ps_id_shop}";
  }

  {if isset($dgTranslateModal)}
  if(typeof dgTranslateModal === 'undefined') {
    var dgTranslateModal = {
      "tableName": "{$dgTranslateModal.tableName}",
      "id": {$dgTranslateModal.id}
    };
  }
  {/if}
</script>
