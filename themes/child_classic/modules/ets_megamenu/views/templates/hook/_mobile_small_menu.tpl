                    {* Menú idiomas en mobile *}

                    <div class="float-xs-right hidden-md-up" id="mobile_language_selector">

                        <div id="mobile_top_menu_wrapper" class="hidden-md-up" style="padding: 0;">

                            <div class="js-top-menu mobile" id="_mobile_top_menu"></div>

                            <div class="js-top-menu-bottom">

                                <div id="_mobile_currency_selector"></div>

                                <div id="_mobile_language_selector">

                                    <div class="language-selector-wrapper" style="padding: 0;">

                                        <div class="language-selector dropdown js-dropdown" style="display: flex;">

                                            <ul class="dropdown-menu" aria-labelledby="language-selector-label">

                                                {foreach from=Language::getLanguages(true) item=my_lang}

                                                    <li>

                                                        <a href="{$link->getLanguageLink($my_lang.id_lang)}">

                                                            {$my_lang.name|substr:0:($my_lang.name|strpos:'(')}

                                                        </a>

                                                    </li>

                                                {/foreach}

                                            </ul>

                                            <select class="link hidden-md-up" aria-labelledby="language-selector-label">

                                                {foreach from=Language::getLanguages(true) item=my_lang}

                                                    <option value="{$link->getLanguageLink($my_lang.id_lang)}"

                                                            {if $my_lang.id_lang == $language.id}selected="selected"{/if}

                                                            data-iso-code="es">

                                                        {$my_lang.name|substr:0:($my_lang.name|strpos:'(')}

                                                    </option>

                                                {/foreach}

                                            </select>

                                            <i class="material-icons expand-more">&#xE5C5;</i>

                                        </div>

                                    </div>

                                </div>

                                <div id="_mobile_contact_link"></div>

                            </div>

                        </div>

                        {*<ul>

                            {foreach from=Language::getLanguages(true) item=my_lang}

                                    <li>

                                        <a href="{$link->getLanguageLink($my_lang.id_lang)}">{$my_lang.name|substr:0:($my_lang.name|strpos:'(')}</a>

                                    </li>

                            {/foreach}

                        </ul>*}

                    </div>