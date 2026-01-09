{literal}
<script>
(function () {
  var CLARITY_ID = "{/literal}{$cc_clarity_id|escape:'javascript'}{literal}";
  var REQUIRED_CATEGORY = "{/literal}{$cc_cf_category|escape:'javascript'}{literal}"; // performance|functional|advertising
  var FIRST_DELAY_MS = parseInt("{/literal}{$cc_first_delay_ms|intval}{literal}", 10) || 0;
  var BOT_REGEX_SRC = "{/literal}{$cc_bot_regex|escape:'javascript'}{literal}";

  function isBot() {
    try {
      if (!BOT_REGEX_SRC) return false;
      var re = new RegExp(BOT_REGEX_SRC, 'i');
      var ua = navigator.userAgent || '';
      return re.test(ua);
    } catch (e) { return false; }
  }

  function hasConsentForCategory(consentObj) {
    if (!consentObj || typeof consentObj !== 'object') return false;
    return !!consentObj[REQUIRED_CATEGORY];
  }

  function injectClarity() {
    if (!CLARITY_ID) return;
    if (window.__ccClarityInjected) return;
    window.__ccClarityInjected = true;

    // stub (cola) oficial
    window.clarity = window.clarity || function () {
      (window.clarity.q = window.clarity.q || []).push(arguments);
    };

    var t = document.createElement('script');
    t.async = true;
    t.src = "https://www.clarity.ms/tag/" + CLARITY_ID;
    document.head.appendChild(t);
  }

  function loadClarityWithDelay() {
    var clarityScriptLoaded = false;
    try { clarityScriptLoaded = localStorage.getItem('clarityScriptLoaded'); } catch (e) {}

    var delay = clarityScriptLoaded ? 0 : FIRST_DELAY_MS;

    setTimeout(function () {
      try {
        injectClarity();
        try {
          if (!clarityScriptLoaded) localStorage.setItem('clarityScriptLoaded', 'true');
        } catch (e) {}
      } catch (err) {
        console.error('Error loading Microsoft Clarity:', err);
      }
    }, delay);
  }

  function maybeLoad(consentObj) {
    if (isBot()) return;
    if (hasConsentForCategory(consentObj)) loadClarityWithDelay();
  }

  // Eventos CookieFirst
  window.addEventListener('cf_init', function () {
    try {
      if (window.CookieFirst && window.CookieFirst.consent) maybeLoad(window.CookieFirst.consent);
    } catch (e) {}
  });

  window.addEventListener('cf_consent_loaded', function (event) {
    try { maybeLoad((event && event.detail) || (window.CookieFirst && window.CookieFirst.consent)); } catch (e) {}
  });

  window.addEventListener('cf_consent', function (event) {
    try { maybeLoad((event && event.detail) || (window.CookieFirst && window.CookieFirst.consent)); } catch (e) {}
  });

  // Fallback
  document.addEventListener('DOMContentLoaded', function () {
    try {
      if (window.CookieFirst && window.CookieFirst.consent) maybeLoad(window.CookieFirst.consent);
    } catch (e) {}
  });
})();
</script>
{/literal}
