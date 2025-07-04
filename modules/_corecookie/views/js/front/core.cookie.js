/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License and is not open source.
 * Each license that you purchased is only available for 1 website only.
 * You can't distribute, modify or sell this code.
 * If you want to use this file on more websites, you need to purchase additional licenses.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * If you need help please contact <attechteams@gmail.com>
 *
 * @author    Alpha Tech <attechteams@gmail.com>
 * @copyright 2022 Alpha Tech
 * @license   opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

$(document).ready(function() {
    var corecookie_global_settings = core_cookie_settings.global_settings;

    var SVG = {
        'close': `<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'><path fill-rule='evenodd' d='M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z'/><path fill-rule='evenodd' d='M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z'/></svg>`,
        'cookie_icon_default': `<svg width="45" height="45" viewBox="0 0 45 45" xmlns="http://www.w3.org/2000/svg"><path d="M45 24.2271C45 26.8163 43.8203 31.0317 41.3966 34.8405C38.9728 38.6494 33.1387 44.277 23.5296 44.9618C19.0682 45.2185 14.8642 44.17 11.0678 41.8377C4.22553 37.6223 0.514865 31.4597 0.0429878 23.4568C-0.2144 19.006 0.665007 14.6837 3.19598 11.0246C10.8318 0.00466089 21.1702 -0.123727 23.8728 0.0260585C25.8032 0.154446 27.7765 0.988967 27.8408 1.6523C27.9052 2.40123 27.5191 2.85059 26.8971 3.17156C25.8461 3.7493 25.3099 4.64802 25.3957 5.82491C25.4815 7.02319 26.1464 7.81492 27.2617 8.26427C28.2484 8.64944 28.5272 9.16299 28.3127 10.2115C27.562 14.0631 30.9509 17.4868 34.8332 16.7593C35.9914 16.5453 36.4204 16.8021 36.8708 17.872C37.3213 18.9419 38.1149 19.5624 39.2731 19.648C40.4528 19.7336 41.3322 19.1986 41.9328 18.1929C42.0615 17.9789 42.2116 17.7436 42.3832 17.551C42.7693 17.1658 43.2626 17.1016 43.7774 17.2728C44.378 17.4654 44.6568 17.9361 44.7426 18.5139C44.8499 19.434 45 21.638 45 24.2271ZM42.1902 21.8306C41.7183 22.0018 41.2679 22.2157 40.796 22.3227C38.3723 22.9005 36.1845 22.0018 34.7474 19.9262C34.6616 19.7978 34.4257 19.6908 34.2541 19.6908C33.7178 19.648 33.1602 19.6694 32.624 19.6052C28.3985 19.0702 25.0954 15.1544 25.3742 10.9604C25.3957 10.5967 25.2884 10.4041 25.0096 10.2329C23.1221 8.9918 22.1783 6.68082 22.6931 4.49823C22.8218 3.96328 23.0363 3.42833 23.2079 2.89338C16.9662 2.48682 8.81561 5.80351 4.80466 13.9347C0.986742 21.6594 2.76701 31.0103 9.22314 37.0017C15.5077 42.8434 24.9667 43.8705 32.5167 39.4625C40.0453 35.0759 42.6192 27.1373 42.1902 21.8306Z" ></path><path d="M16.8805 8.47878C18.4248 8.45738 19.7332 9.74126 19.7332 11.2819C19.7332 12.8012 18.4677 14.0636 16.9448 14.085C15.4005 14.1064 14.1135 12.8226 14.1135 11.2819C14.0921 9.74126 15.3576 8.47878 16.8805 8.47878Z" ></path><path d="M11.2611 25.3184C9.71682 25.2971 8.42989 24.0132 8.47278 22.4725C8.49423 20.9319 9.78117 19.6908 11.304 19.7122C12.8484 19.7336 14.1353 21.0175 14.0924 22.5581C14.071 24.0774 12.784 25.3398 11.2611 25.3184Z" ></path><path d="M25.353 36.573C23.8087 36.573 22.5217 35.2677 22.5432 33.727C22.5646 32.1864 23.8301 30.9453 25.3744 30.9453C26.9188 30.9453 28.2057 32.2506 28.1842 33.7912C28.1628 35.3105 26.8759 36.573 25.353 36.573Z" ></path><path d="M24.2378 25.2974C23.2511 25.49 22.3717 24.6341 22.5648 23.6497C22.672 23.0934 23.101 22.644 23.6587 22.5371C24.6453 22.3445 25.5033 23.2004 25.3317 24.1847C25.2244 24.741 24.7955 25.1904 24.2378 25.2974Z" ></path><path d="M36.6344 29.5548C36.6344 30.3252 35.9909 30.9457 35.2188 30.9457C34.4251 30.9457 33.8031 30.2824 33.8246 29.512C33.846 28.7417 34.4895 28.1426 35.2617 28.1426C36.0124 28.164 36.6344 28.7845 36.6344 29.5548Z" ></path><path d="M16.9024 32.3581C16.9024 33.1284 16.2804 33.749 15.5083 33.749C14.7146 33.749 14.0712 33.107 14.0926 32.3153C14.1141 31.545 14.7575 30.9245 15.5083 30.9458C16.2804 30.9672 16.9024 31.5878 16.9024 32.3581Z" ></path></svg>`,
    };
    function renderCookie(canvas) {
        var contents = getContent();
        if (localStorage.getItem('core_cookieconsent_status') && !shoudResetCustomerConsent()) {
            return shouldRenderReopenCookieBtn(canvas, contents);
        }
        var html = '';
        var html_refenece = '';
        html_refenece = renderReference(contents);
        if (isMobile()) {
            html = renderCookieMobile(contents);
        } else {
            html = renderCookieDesktop(contents);
            html += renderReopenCookieBtn(contents);
        }
        var force_choice_accepting_cookies = corecookie_global_settings.force_choice_accepting_cookies == 1;
        if (force_choice_accepting_cookies) {
            html = renderCookieDesktop(contents);
            $(canvas).html(`
                <div class='core-cookie-bar-wrapper -force-choice'>
                    ${html}
                    <div class='core-cookie-bar-backdrop -force-choice-fade'></div>
                </div>
                ${renderReopenCookieBtn(contents)}
            `);
        } else {
            $(canvas).html(html);
        }
        $("#core-js-cookie-references").html(html_refenece);
        if (core_cookie_settings.is_demo == 1 || isSandboxMode()) {
            return bindEventsPreview();
        }
        if (force_choice_accepting_cookies) {
            setTimeout(function() {
                $(".core-cookie-bar-wrapper.-force-choice").find(".core-cookie-bar-backdrop").addClass('show');
            });
        }
        bindEventsProduct();
    }

    function shouldRenderReopenCookieBtn(canvas, contents) {
        var status = localStorage.getItem('core_cookieconsent_status');

        if (core_cookie_settings.design.hide_reopen_btn_when_accepted_cookies == 1) {
            if ([STATUS_ACCEPT_ALL, STATUS_ACCEPT_SELECTED].includes(core_cookieconsent_status)) {
                return '';
            }
        }

        $(canvas).html(renderReopenCookieBtn(contents)).removeClass("core-cookie-appear");
        bindEventsReopenBtn();
    }

    function isSandboxMode() {
        var settings = core_cookie_settings;
        if (typeof settings?.global_settings === 'undefined'
        || typeof settings?.global_settings?.sandbox_mode == 'undefined') {
            return false
        }
        if (settings?.global_settings?.sandbox_mode == 1
        && Array.isArray(settings.global_settings.whitelist_ips)
        && settings.global_settings.whitelist_ips.includes(settings.visitor_ip)) {
            return true;
        }

        return false;
    }

    function shoudResetCustomerConsent() {
        var settings = core_cookie_settings;
        var cookie_since = localStorage.getItem("core_cookieconsent_since");
        if (!cookie_since
        || typeof settings?.global_settings === 'undefined'
        || typeof settings?.global_settings?.reset_customer_consent === 'undefined'
        ) {
            return false;
        }
        if (cookie_since > Number(settings?.global_settings?.reset_customer_consent || 0)) {
            return false;
        }
        localStorage.removeItem('core_cookieconsent_status');
        localStorage.removeItem('core_cookieconsent_preferences_disabled');
        return true;
    }

    function renderReference(contents) {
        var settings = core_cookie_settings;
        var is_mobile = isMobile();
        var tab_html = '';
        var input_analytics_checked = '';
        var input_marketing_checked = '';
        var input_functional_checked = '';
        if (settings.initial_state_cookie_bar.length == 1 && settings.initial_state_cookie_bar[0] == 'keep_all_store_cookies') {
            input_analytics_checked = 'checked="checked"';
            input_marketing_checked = 'checked="checked"';
            input_functional_checked = 'checked="checked"';
        }
        if (!settings.initial_state_cookie_bar.includes(block_analytics_cookies)) {
            input_analytics_checked = 'checked="checked"';
        }
        if (!settings.initial_state_cookie_bar.includes(block_marketing_cookies)) {
            input_marketing_checked = 'checked="checked"';
        }
        if (!settings.initial_state_cookie_bar.includes(block_functionality_cookies)) {
            input_functional_checked = 'checked="checked"';
        }
        if (settings.references_display_cookie_categories == 1) {
            tab_html = `
                <div class='core-settings-tabs'>
                    <div class='core-settings-tablist-wrap'>
                        <ul>
                            <li class='core-settings-tab core-active' data-tab='settings'>
                                <a href="#-core-settings" class='core-select-tab'>${contents.tab_setting_name || '---'}</a>
                            </li>
                            <li class='core-settings-tab' data-tab='declaration'>
                                <a href="#-core-declaration" class='core-select-tab'>${contents.tab_declaration_name || '---'}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            `;
        }

        return `
            <div class='core-reference-container ${is_mobile ? "-mobile" : ""}'>
                <div class='core-reference-settings'>
                    <div class='core-header'>
                        <div class='core-title'>${contents.preferences_popup_header_title}</div>
                        <div class='core-btn-close'>${SVG.close}</div>
                    </div>
                    ${tab_html}
                    <div class='core-content'>
                        <div class='-core-settings'>
                            <p class='core-reference-desc'>${contents.preferences_popup_header_desc}</p>
                            <div class='core-settings-options'>
                                <div class='core-settings-option'>
                                    <div class='core-switch'>
                                        <input type="checkbox" class='core-switch-value' id="core-setting-strict" checked="checked" disabled>
                                        <label for="core-setting-strict"></label>
                                    </div>
                                    <div class='core-setting-option-detail'>
                                        <p class='-core-title'>${contents.strict_cookie_title}</p>
                                        <p>${contents.strict_cookie_desc}</p>
                                    </div>
                                </div>
                                <div class='core-settings-option'>
                                    <div class='core-switch'>
                                        <input type="checkbox" class='core-switch-value' id="core-setting-analytics" ${input_analytics_checked}>
                                        <label for="core-setting-analytics"></label>
                                    </div>
                                    <div class='core-setting-option-detail'>
                                        <p class='-core-title'>${contents.analytics_cookie_title}</p>
                                        <p>${contents.analytics_cookie_desc}</p>
                                    </div>
                                </div>
                                <div class='core-settings-option'>
                                    <div class='core-switch'>
                                        <input type="checkbox" class='core-switch-value' id="core-setting-marketing" ${input_marketing_checked}>
                                        <label for="core-setting-marketing"></label>
                                    </div>
                                    <div class='core-setting-option-detail'>
                                        <p class='-core-title'>${contents.marketing_cookie_title}</p>
                                        <p>${contents.marketing_cookie_desc}</p>
                                    </div>
                                </div>
                                <div class='core-settings-option'>
                                    <div class='core-switch'>
                                        <input type="checkbox" class='core-switch-value' id="core-setting-functional" ${input_functional_checked}>
                                        <label for="core-setting-functional"></label>
                                    </div>
                                    <div class='core-setting-option-detail'>
                                        <p class='-core-title'>${contents.functional_cookie_title}</p>
                                        <p>${contents.functional_cookie_desc}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='-core-declaration core-hidden'>
                            <div class='core-declaration-category'>
                                <div class='core-declaration-category-header'>${contents.strict_cookie_title}</div>
                                <div class='core-declaration-category-cookies'>${renderDeclaration(category_strictly)}</div>
                            </div>
                            <div class='core-declaration-category'>
                                <div class='core-declaration-category-header'>${contents.analytics_cookie_title}</div>
                                <div class='core-declaration-category-cookies'>${renderDeclaration(category_report_analytics)}</div>
                            </div>
                            <div class='core-declaration-category'>
                                <div class='core-declaration-category-header'>${contents.marketing_cookie_title}</div>
                                <div class='core-declaration-category-cookies'>${renderDeclaration(category_marketing)}</div>
                            </div>
                            <div class='core-declaration-category'>
                                <div class='core-declaration-category-header'>${contents.functional_cookie_title}</div>
                                <div class='core-declaration-category-cookies'>${renderDeclaration(category_functional)}</div>
                            </div>
                        </div>
                    </div>
                    <div class='core-actions'>
                        <div class='-core-btn core-btn-save-settings'><span>${contents.accept_selected_button}</span></div>
                        <div class='core-accept-reject-container'>
                            <div class="-core-btn core-btn-reject-all"><span>${contents.reject_button_text}</span></div>
                            <div class="-core-btn core-btn-accept-all"><span>${contents.accept_all_selected_button}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function renderDeclaration(category) {
        var settings = core_cookie_settings;
        if (typeof settings.cookies === 'undefined') {
            return '';
        }
        var html = '';
        for (var i=0; i<settings.cookies.length; i++) {
            var cookie = settings.cookies[i];
            if (cookie.category == category) {
                html += `
                    <div class='core-declaration-category-cookie'>
                        <div class="-core-header">${cookie.name}</div>
                        <div class="-core-content">${cookie.content}</div>
                    </div>
                `;
            }
        }

        return html;
    }

    function closeCookieConsent() {
        $("#core-js-cookie").removeClass("core-cookie-appear");
        $('.core-cookie-bar').hide();
        $('.core-cookie-bar-wrapper').hide();
        closePopupReference();

        if (core_cookie_settings.design.hide_reopen_btn_when_accepted_cookies == 1) {
            var core_cookieconsent_status = localStorage.getItem('core_cookieconsent_status');
            if ([STATUS_ACCEPT_ALL, STATUS_ACCEPT_SELECTED].includes(core_cookieconsent_status)) {
                $(".core-cookie-bar-reopen-btn").remove();
            }
        }
    }

    function savePolicyAccepted() {
        var settings = core_cookie_settings;
        if (typeof settings.link_process === 'undefined') {
            return;
        }
        var given_consent_analytics = 0;
        var given_consent_marketing = 0;
        var given_consent_functionality = 0;
        var interaction = localStorage.getItem('core_cookieconsent_status');
        if (!interaction) {
            return;
        }
        interaction = JSON.parse(interaction).value;
        var core_cookieconsent_preferences_disabled = localStorage.getItem('core_cookieconsent_preferences_disabled');
        if (!core_cookieconsent_preferences_disabled) {
            return;
        }
        core_cookieconsent_preferences_disabled = JSON.parse(core_cookieconsent_preferences_disabled).value;
        var split_core_cookieconsent_preferences_disabled = core_cookieconsent_preferences_disabled.split(" ");
        if (split_core_cookieconsent_preferences_disabled.includes(block_analytics_cookies)) {
            given_consent_analytics = 1;
        }
        if (split_core_cookieconsent_preferences_disabled.includes(block_marketing_cookies)) {
            given_consent_marketing = 1;
        }
        if (split_core_cookieconsent_preferences_disabled.includes(block_functionality_cookies)) {
            given_consent_functionality = 1;
        }

        $.ajax({
            url: settings.link_process,
            method: "POST",
            data: {
                _a: 'save_policy_accepted',
                _r: 1,
                id_customer: settings.id_customer,
                given_consent_analytics: given_consent_analytics,
                given_consent_marketing: given_consent_marketing,
                given_consent_functionality: given_consent_functionality,
                interaction: interaction,
                accepted_page: window.location.href
            },
            success: function(res) {}
        });
    }

    function saveAcceptCookies(key, value) {
        localStorage.setItem(key, JSON.stringify({
            value: value,
            expire: 365,
            from: timeNow()
        }));
    }

    function timeNow() {
        return Math.floor((new Date()).getTime() / 1000);
    }

    function timeDifference(from, to) {
        var difference = from - to;
        var days_difference = Math.floor(difference/60/60/24);
        difference -= days_difference*1000*60*60*24

        var hours_difference = Math.floor(difference/60/60);
        difference -= hours_difference*1000*60*60

        var minutes_difference = Math.floor(difference/60);
        difference -= minutes_difference*1000*60

        var seconds_difference = Math.floor(difference);

        return {
            days: days_difference,
            hours: hours_difference,
            minutes: minutes_difference,
            seconds: seconds_difference
        };
    }

    function bindEventsProduct() {
        var settings = core_cookie_settings;
        var design = settings.design;
        saveAcceptCookies('core_cookieconsent_preferences_disabled', settings.initial_state_cookie_bar.join(" "));
        $(".core-cookie-bar").find(".button.agree").click(function () {
            if (settings.regard_initial_state_when_accept_cookie_bar == 1) {
                saveAcceptCookies('core_cookieconsent_preferences_disabled', settings.initial_state_cookie_bar.join(" "));
            }
            saveAcceptCookies('core_cookieconsent_status', STATUS_ACCEPT_ALL);
            setCookieSince();
            closeCookieConsent();
            savePolicyAccepted();
            reloadPageAfterAccecptCookie();
        });
        $(".core-cookie-bar").find(".button.reject").click(function () {
            saveAcceptCookies('core_cookieconsent_status', STATUS_DENY);
            setCookieSince();
            closeCookieConsent();
            savePolicyAccepted();

            if (settings.global_settings.redirect_if_refuse_cookie != 1) {
                return;
            }
            window.location.href = settings.global_settings.reject_redirect_url;
        });
        $(".core-cookie-bar").find(".button-close").click(function () {
            $("#core-js-cookie").removeClass("core-cookie-appear");
            $(".core-cookie-bar").hide();
            closeCookieConsent();
            if (settings.states_when_close_cookie_bar.length == 1 && settings.states_when_close_cookie_bar[0] == 'keep_all_store_cookies') {
                return;
            }
            saveAcceptCookies('core_cookieconsent_preferences_disabled', settings.initial_state_cookie_bar.join(" "));
            savePolicyAccepted();
        });
        $(".core-cookie-bar").find(".button.preferences").click(function () {
            openPopupReference();
        });
        $(".core-reference-container .core-btn-close").click(function () {
            closePopupReference();
        });
        $(".core-reference-container .core-btn-save-settings").click(function () {
            saveAcceptCookies('core_cookieconsent_status', STATUS_ACCEPT_SELECTED);
            setCookieSince();
            updateCookiePreferenceDisabled();
            closeCookieConsent();
            closePopupReference();
            savePolicyAccepted();
            reloadPageAfterAccecptCookie();
        });
        $(".core-reference-container .core-btn-accept-all").click(function () {
            saveAcceptCookies('core_cookieconsent_status', STATUS_ACCEPT_ALL);
            setCookieSince();
            updateCookiePreferenceDisabled(STATUS_ACCEPT_ALL);
            closeCookieConsent();
            closePopupReference();
            savePolicyAccepted();
            reloadPageAfterAccecptCookie();
        });
        $(".core-reference-container .core-btn-reject-all").click(function () {
            saveAcceptCookies('core_cookieconsent_status', STATUS_DENY);
            setCookieSince();
            updateCookiePreferenceDisabled(STATUS_DENY);
            closeCookieConsent();
            closePopupReference();
            savePolicyAccepted();
        });
        $(".core-settings-tabs").find(".core-settings-tab").each(function() {
            $(this).click(function(e) {
                e.preventDefault();
                $(".core-reference-settings .core-content > div").addClass("core-hidden");
                $(".core-settings-tabs").find(".core-settings-tab").removeClass("core-active");
                $(this).addClass("core-active");
                $(".core-reference-settings .core-content > .-core-"+$(this).data('tab')).removeClass("core-hidden");
            });
        });

        bindEventsReopenBtn();
    }

    function bindEventsReopenBtn() {
        var settings = core_cookie_settings;
        var design = settings.design;
        var trigger_reeopen_btn = function(ref) {
            var desktop_display_type = design.desktop_display_type;
            var desktop_display_position = design.desktop_display_position;
            var cookie_main_wrapper = $(ref).closest("#core-js-cookie");
            var cookie_bar_wrapper = cookie_main_wrapper.find(".core-cookie-bar");
            if (cookie_bar_wrapper.hasClass('mobile') || isMobile()) {
                return;
            }
            if (!cookie_bar_wrapper.length) {
                var contents = getContent();
                $("#core-js-cookie").append(renderCookieDesktop(contents));
                $("#core-js-cookie-references").html(renderReference(contents));
                cookie_bar_wrapper = cookie_main_wrapper.find(".core-cookie-bar");
                bindEventsProduct();
            }

            var animation_disappear_class = '';
            var animation_disappear_after_class = '';
            var animation_appear_class = '';
            if (desktop_display_type == 'full_bar' && desktop_display_position == 'full_bar_bottom') {
                animation_appear_class = 'cookie-bar-appear-bottom';
                animation_disappear_class = 'cookie-bar-disappear-bottom';
                animation_disappear_after_class = 'cookie-bar-after-disappear-bottom';
            }

            if (desktop_display_type == 'full_bar' && desktop_display_position == 'full_bar_top') {
                animation_appear_class = 'cookie-bar-appear-top';
                animation_disappear_class = 'cookie-bar-disappear-top';
                animation_disappear_after_class = 'cookie-bar-after-disappear-top';
            }

            if (desktop_display_type == 'float_bar' && ['float_bar_bl', 'float_bar_tl'].includes(desktop_display_position)) {
                animation_appear_class = 'cookie-bar-appear-left';
                animation_disappear_class = 'cookie-bar-disappear-left';
                animation_disappear_after_class = 'cookie-bar-after-disappear-left';
            }

            if (desktop_display_type == 'float_bar' && ['float_bar_br', 'float_bar_tr'].includes(desktop_display_position)) {
                animation_appear_class = 'cookie-bar-appear-right';
                animation_disappear_class = 'cookie-bar-disappear-right';
                animation_disappear_after_class = 'cookie-bar-after-disappear-right';
            }

            if (!cookie_main_wrapper.hasClass('core-cookie-appear')) {
                if (!cookie_bar_wrapper.is(':visible')) {
                    cookie_bar_wrapper.removeAttr("style");
                }
                cookie_bar_wrapper.removeClass("core-hidden");
                cookie_main_wrapper.removeClass(animation_disappear_class).addClass(animation_appear_class);
                return setTimeout(function() {
                    cookie_main_wrapper.addClass('core-cookie-appear');
                }, 410);
            }
            if (cookie_main_wrapper.hasClass('core-cookie-appear')) {
                cookie_main_wrapper.removeClass(animation_appear_class).addClass(animation_disappear_class);
                setTimeout(function() {
                    cookie_main_wrapper.removeClass('core-cookie-appear');
                    cookie_bar_wrapper.addClass("core-hidden");
                }, 350);
            }
        }

        $(".core-cookie-bar-reopen-btn").click(function() {
            trigger_reeopen_btn($(this));
        });
    }

    function reloadPageAfterAccecptCookie() {
        if (core_cookie_settings.global_settings.reload_page_after_accept_cookies != 1) {
            return;
        }
        location.reload();
    }

    function setCookieSince() {
        localStorage.setItem("core_cookieconsent_since", timeNow());
    }

    function updateCookiePreferenceDisabled(status) {
        var settings = core_cookie_settings;
        var preferences_disabled = [];
        var is_accept_all = (status == STATUS_ACCEPT_ALL);
        var is_deny = (status == STATUS_DENY);
        if (is_accept_all) {
            $("#core-setting-analytics").prop("checked", true);
            $("#core-setting-marketing").prop("checked", true);
            $("#core-setting-functional").prop("checked", true);
        }
        if (is_deny) {
            $("#core-setting-analytics").prop("checked", false);
            $("#core-setting-marketing").prop("checked", false);
            $("#core-setting-functional").prop("checked", false);
        }
        if ($("#core-setting-analytics").is(":checked")) {
            preferences_disabled.push(block_analytics_cookies);
        }
        if ($("#core-setting-marketing").is(":checked")) {
            preferences_disabled.push(block_marketing_cookies);
        }
        if ($("#core-setting-functional").is(":checked")) {
            preferences_disabled.push(block_functionality_cookies);
        }
        saveAcceptCookies('core_cookieconsent_preferences_disabled', !preferences_disabled.length && !is_deny ? settings.initial_state_cookie_bar.join(" ") : preferences_disabled.join(" "));
    }

    function bindEventsPreview() {
        $(".core-cookie-bar").find(".button.agree").click(function () {
            $(".core-cookie-bar").hide();
        });
        $(".core-cookie-bar").find(".button.reject").click(function () {
            $(".core-cookie-bar").hide();
        });
        $(".core-cookie-bar").find(".button-close").click(function () {
            $(".core-cookie-bar").hide();
        });
        $(".core-cookie-bar").find(".button.preferences").click(function () {
            openPopupReference();
        });
        $(".core-reference-container .core-btn-close").click(function () {
            closePopupReference();
        });
        $(".core-reference-container .-core-btn").click(function () {
            closePopupReference();
        });
    }

    function renderCookieMobile(contents) {
        var settings = core_cookie_settings;
        var design = settings.design;
        var client = core_cookie_client;
        var reject_button = `<div class='button reject'>${contents.reject_button_text}</div>`;
        var close_button = `<div class='button-close'>${SVG.close}</div>`;
        var icon_display = '';
        if (design.show_reject_btn == 0) {
            reject_button = '';
        }
        if (design.show_close_btn == 0) {
            close_button = '';
        }
        if (design.show_icon_mobile == 1) {
            var icon_img = `<img src='${client.base_link}modules/${client.name_module}/views/img/${design.cookie_icon}'/>`;
            if (design.cookie_icon == COOKIE_ICON_DEFAULT) {
                icon_img = SVG.cookie_icon_default;
            }
            if (design.cookie_icon.startsWith("blob:")) {
                icon_img = `<img src='${design.cookie_icon}'/>`;
            }
            icon_display = `
                <div class='cookie-icon'>
                    ${icon_img}
                </div>
            `;
        }
        var no_icon_cookie = '';
        if (design.show_icon_mobile == 0) {
            no_icon_cookie = '-no-c-icon';
        }
        return `
            <div class='${no_icon_cookie} core-cookie-bar mobile position-${design.mobile_display_position}'>
                ${icon_display}
                <div class='side'>
                    ${close_button}
                </div>
                <div class='message'>
                    <div class='-cookie-consent-text-wrapper'>${contents.cookie_consent_text}</div>
                    <a target="_blank" href="${contents.privacy_policy_link}">${contents.privacy_policy_text}</a>
                </div>
                <div class='buttons'>
                    ${reject_button}
                    <div class='button agree'>${contents.accept_button_text}</div>
                    <div class='wrap-preferences'>
                        <div class='button preferences'>${contents.preferences_button_text}</div>
                    </div>
                </div>
            </div>
        `;
    }

    function renderReopenCookieBtn(contents) {
        var designs = core_cookie_settings.design;
        var client = core_cookie_client;
        var label_reopen_btn = corecookie_global_settings.label_reopen_btn;

        if (designs.show_reopen_cookie_banner_btn == 0) {
            return '';
        }
        if (designs.hide_reopen_btn_when_accepted_cookies == 1) {
            var core_cookieconsent_status = localStorage.getItem('core_cookieconsent_status');
            if ([STATUS_ACCEPT_ALL, STATUS_ACCEPT_SELECTED].includes(core_cookieconsent_status)) {
                return '';
            }
        }

        var label_html = '';
        if (contents.label_reopen_btn != '') {
            label_html = `<span class='-label'>${contents.label_reopen_btn}</span>`;
        }
        var image_html = '';
        if (designs.reopen_btn_image != '' && designs.reopen_btn_use_image == 1) {
            image_html = `<img class='-image' src='${client.base_link}modules/${client.name_module}/views/img/${designs.reopen_btn_image}'/>`;
        }
        var btn_position_class = '-left-position';
        var btn_box_shadow = '-box-shadow';
        if (designs.reopen_btn_position == 'right') {
            btn_position_class = '-right-position';
        }
        if (designs.reopen_btn_active_box_shadow == 0) {
            btn_box_shadow = '';
        }
        return `
            <div class='core-cookie-bar-reopen-btn ${btn_position_class} ${btn_box_shadow}'>
                ${image_html}
                ${label_html}
            </div>
        `;
    }

    function renderCookieDesktop(contents) {
        var settings = core_cookie_settings;
        var design = settings.design;
        var client = core_cookie_client;
        var reject_button = `<div class='button reject'>${contents.reject_button_text}</div>`;
        var close_button = `<div class='button-close'>${SVG.close}</div>`;
        var icon_display = '';
        if (design.show_reject_btn == 0) {
            reject_button = '';
        }
        if (design.show_close_btn == 0) {
            close_button = '';
        }
        if (design.show_icon_desktop == 1) {
            var icon_img = `<img src='${client.base_link}modules/${client.name_module}/views/img/${design.cookie_icon}'/>`;
            if (design.cookie_icon == COOKIE_ICON_DEFAULT) {
                icon_img = SVG.cookie_icon_default;
            }
            if (design.cookie_icon.startsWith("blob:")) {
                icon_img = `<img src='${design.cookie_icon}'/>`;
            }
            icon_display = `
                <div class='cookie-icon'>
                    ${icon_img}
                </div>
            `;
        }
        var no_icon_cookie = '';
        if (design.show_icon_desktop == 0) {
            no_icon_cookie = '-no-c-icon';
        }
        return `
            <div class='${no_icon_cookie} core-cookie-bar desktop type-${design.desktop_display_type} position-${design.desktop_display_position}'>
                ${icon_display}
                <div class='side'>
                    ${close_button}
                </div>
                <div class='message'>
                    <div class='-cookie-consent-text-wrapper'>${contents.cookie_consent_text}</div>
                    <a target="_blank" href="${contents.privacy_policy_link}">${contents.privacy_policy_text}</a>
                </div>
                <div class='buttons'>
                    <div class='wrap-preferences'>
                        <div class='button preferences'>${contents.preferences_button_text}</div>
                    </div>
                    ${reject_button}
                    <div class='button agree'>${contents.accept_button_text}</div>
                </div>
            </div>
        `;
    }

    renderCookie("#core-js-cookie");

    function getContent() {
        var settings = core_cookie_settings;
        var lang_id = settings.lang_id;
        return {
            'cookie_consent_text': settings.cookie_consent_text[lang_id],
            'privacy_policy_text': settings.privacy_policy_text[lang_id],
            'privacy_policy_link': settings.privacy_policy_link[lang_id],
            'preferences_button_text': settings.preferences_button_text[lang_id],
            'reject_button_text': settings.reject_button_text[lang_id],
            'accept_button_text': settings.accept_button_text[lang_id],
            'preferences_popup_header_title': settings.preferences_popup_header_title[lang_id],
            'preferences_popup_header_desc': settings.preferences_popup_header_desc[lang_id],
            'strict_cookie_title': settings.strict_cookie_title[lang_id],
            'strict_cookie_desc': settings.strict_cookie_desc[lang_id],
            'analytics_cookie_title': settings.analytics_cookie_title[lang_id],
            'analytics_cookie_desc': settings.analytics_cookie_desc[lang_id],
            'marketing_cookie_title': settings.marketing_cookie_title[lang_id],
            'marketing_cookie_desc': settings.marketing_cookie_desc[lang_id],
            'functional_cookie_title': settings.functional_cookie_title[lang_id],
            'functional_cookie_desc': settings.functional_cookie_desc[lang_id],
            'accept_selected_button': settings.accept_selected_button[lang_id],
            'accept_all_selected_button': settings.accept_all_selected_button[lang_id],
            'tab_setting_name': settings.tab_setting_name,
            'tab_declaration_name': settings.tab_declaration_name,
            'label_reopen_btn': settings.label_reopen_btn[lang_id],
        };
    }

    function isMobile() {
        let check = false;
        (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
        return check;
    }

    function openPopupReference() {
        $(".core-reference-container").show();
        $("body").css("overflow", "hidden");
    }

    function closePopupReference() {
        $(".core-reference-container").hide();
        $("body").removeAttr('style');
    }
});
