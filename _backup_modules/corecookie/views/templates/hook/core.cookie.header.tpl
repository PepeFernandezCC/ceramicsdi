{**
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
*}

<div id="core-js-cookie" class='core-cookie-appear'></div>
<div id="core-js-cookie-references"></div>

<script type="text/javascript">
    var core_cookie_settings = {$settings nofilter}; {* This is html code so no need to escape *}
    var core_cookie_client = {$client nofilter}; {* This is html code so no need to escape *}
	var COOKIE_ICON_DEFAULT = 'module/cookie.svg';
	var STATUS_ACCEPT_ALL = 'accept_all';
	var STATUS_ACCEPT_SELECTED = 'accept_selected';
	var STATUS_DENY = 'deny';

	var category_strictly = 'strictly';
	var category_report_analytics = 'report_analytics';
	var category_marketing = 'marketing';
	var category_functional = 'functional';

	var block_functionality_cookies = 'block_functionality_cookies';
	var block_marketing_cookies = 'block_marketing_cookies';
	var block_analytics_cookies = 'block_analytics_cookies';

	setInterval(function() {
		corecookieBlockCookies();
	});

    function corecookieBlockCookies() {
	    var initial_state_cookie_bar = core_cookie_settings.initial_state_cookie_bar;
	    var core_cookieconsent_status = localStorage.getItem('core_cookieconsent_status');
	    if (core_cookieconsent_status) {
			core_cookieconsent_status = JSON.parse(core_cookieconsent_status).value;
		}
	    var cookies = core_cookie_settings.cookies;
	    if (STATUS_DENY == core_cookieconsent_status || !core_cookieconsent_status) {
		    for (var i=0; i<cookies.length; i++) {
			    var cookie = cookies[i];
			    if (cookie.status !== 'active') {
			    	continue;
				}
			    if ((cookie.category == category_report_analytics
				    && initial_state_cookie_bar.includes(block_analytics_cookies))
				    || (cookie.category == category_marketing
					    && initial_state_cookie_bar.includes(block_marketing_cookies))
				    || (cookie.category == category_functional
					    && initial_state_cookie_bar.includes(block_functionality_cookies))) {
			    	if (typeof Cookies !== 'undefined') {
						Cookies.remove(cookie.name);
					}
				    document.cookie = cookie.name + '=; Path=/; Domain=.'+window.location.hostname.replace('www.', '')+'; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
				    document.cookie = cookie.name + '=; Path=/; Domain=.'+window.location.hostname+'; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
				    document.cookie = cookie.name + '=; Path=/; Domain='+window.location.hostname+'; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
			    }
		    }
	    }
    }
</script>
