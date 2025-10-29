{**
 * Copyright since 2022 Trusted shops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tech@202-ecommerce.com so we can send you a copy immediately.
 *
 * @author 202 ecommerce <tech@202-ecommerce.com>
 * @copyright 2022 Trusted shops
 * @license https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 *
 * This source file is loading the components connector.umd.js and eventsLib.js
 * (itself subject to the Trusted Shops EULA https://policies.etrusted.com/IE/en/plugin-licence.html)  to connect to Trusted Shops. For these components, you will find below a list of the open source libraries we use for our Services.
 * Please note that the following list may be subject to amendments and modifications, and does not thus claim (perpetual) exhaustiveness. You can always refer to the following website for up-to-date information on the open source software Trusted Shops uses:
 * https://policies.etrusted.com/IE/en/plugin-licence.html
 *
 * Name                Licence         Copyright Disclaimer
 * axios               MIT     Copyright (c) 2014-present (Matt Zabriskie)
 * babel               MIT     Copyright (c) 2014-present (Sebastian McKenzie and other Contributors)
 * follow-redirects    MIT     Copyright (c) 2014–present (Olivier Lalonde, James Talmage, Ruben Verborgh)
 * history             MIT     Copyright (c) 2016-2020 (React Training), Copyright (c) 2020-2021 (Remix Software)
 * hookform/resolvers  MIT     Copyright (c) 2019-present (Beier(Bill) Luo)
 * inherits            ISC     Copyright (c) 2011-2022 (Isaac Z. Schlueter)
 * js-tokens           MIT     Copyright (c) 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021 (Simon Lydell)
 * lodash              MIT     Copyright (c) (OpenJS Foundation and other contributors (https://openjsf.org/)
 * lodash-es           MIT     Copyright (c) (OpenJS Foundation and other contributors (https://openjsf.org/)
 * loose-envify        MIT     Copyright (c) 2015 (Andreas Suarez)
 * nanoclone           MIT     Copyright (c) 2017 (Anton Kosykh)
 * path                MIT     Copyright (c) (Joyent, Inc. and other Node contributors.)
 * preact              MIT     Copyright (c) 2015-present (Jason Miller)
 * preact-router       MIT     Copyright (c) 2015 (Jason Miller)
 * process             MIT     Copyright (c) 2013 (Roman Shtylman)
 * property-expr       MIT     Copyright (c) 2014 (Jason Quense)
 * react-hook-form     MIT     Copyright (c) 2019-present (Beier(Bill) Luo)
 * regenerator-runtime MIT     Copyright (c) 2014-present (Facebook, Inc.)
 * resolve-pathname    MIT     Copyright (c) 2016-2018 (Michael Jackson)
 * tiny-invariant      MIT     Copyright (c) 2019 (Alexander Reardon)
 * tiny-warning        MIT     Copyright (c) 2019 (Alexander Reardon)
 * toposort            MIT     Copyright (c) 2012 (Marcel Klehr)
 * types/lodash        MIT     (none)
 * util                MIT     Copyright (c) (Joyent, Inc. and other Node contributors)
 * value-equal         MIT     Copyright (c) 2016-2018 (Michael Jackson)
 * yup                 MIT     Copyright (c) 2014 (Jason Quense)
 * zustand             MIT     Copyright (c) 2019 (Paul Henschel)
 *}

<div id="app">
  <div id="eTrusted-connector"></div>
</div>

<div id="ts_invite_modal" role="dialog" class="modal{if isset($ts_show_modal)} show{/if}">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">
            <h4>{l s='Sending invite for order' mod='trustedshopseasyintegration'}
              <button type="button" class="close close_ts_invites" data-dismiss="modal" aria-label="Fermer">
                <span aria-hidden="true">×</span>
              </button>
            </h4>
        </div>
      </div>
      <div class="modal-body">
        <h4>
          {l s='For debugging of status purpose, this section allow us to send an invite on one order' mod='trustedshopseasyintegration'}<br />
          {l s='without moving one status on the order. We directly show the logs provided.' mod='trustedshopseasyintegration'}
        </h4>
        <div class="col-md-12 form-group">
          <div>
            <label class="control-label">{l s='ID of the order' mod='trustedshopseasyintegration'}</label>
          </div>
          <div><input name="ts_id_order_invite" id="ts_id_order_invite" type="text" value=0 /></div>
        </div>
        <hr />
        <div class="col-md-12 form-group">
          <label>{l s='Result:' mod='trustedshopseasyintegration'}</label><br />
          <textarea disabled id="ts_result_order_sent" style="width:100%;min-height:100px;resize:vertical;"></textarea>
        </div>
      </div>
      <div class="modal-footer">
          <button class="btn btn-primary close_ts_invites" type="button" data-dismiss="modal" aria-label="Fermer">
              <i class="process-icon-cancel"></i>{l s='Cancel' mod='trustedshopseasyintegration'}
          </button>
          <button id="ts_send_order_invite" class="btn btn-primary">
              <i class="process-icon-save mr-2"></i>&nbsp;&nbsp;
              <span>{l s='Send' mod='trustedshopseasyintegration'}</span>
          </button>
      </div>
    </div>
  </div>
</div>