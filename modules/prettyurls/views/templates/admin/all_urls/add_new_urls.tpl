{**
* prettyurls
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @author    FMM Modules
* @copyright Copyright 2023 © FMM Modules
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* @category  FMM Modules
* @package   prettyurls
*}
<div class="bootstrap">
    <div class="panel">
        <div class="panel-heading" style="background:#008abd;color:white;text-align:center">
            {l s='Update All urls at once' mod='prettyurls'}
            </hr>
        </div>
        <form method="post" id ="submit-new-urls" action="{$action_url|escape:'htmlall':'UTF-8'}">
            <input type="hidden" name="ajax" value="1">
            <input type="hidden" name="action" value="addNewUrlForAllPnf">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <label class="col-sm-3">{l s='New Url' mod='prettyurls'}</label>
                            <div class="col-sm-9">
                                <input type="text" id= "new-url" name="new_url" class="form-control required" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group row">
                            <label class="col-sm-3">{l s='Redirection Type' mod='prettyurls'}</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="redirection_type" id="redirection-type">
                                    <option value="301">{l s='301 Moved Permanently' mod='prettyurls'}</option>
                                    <option value="302">{l s='302 Moved Permanently' mod='prettyurls'}</option>
                                    <option value="303">{l s='303 Other Source' mod='prettyurls'}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <input type="submit" id="save-new-urls" class="col-sm-offset-5 btn btn-primary btn-lg submit_setting"  value="{l s='Update' mod= 'prettyurls'}">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>   