<section class="cc-contact">
  <div class="cc-contact__container">

    <header class="cc-contact__header">
      <h1>{l s='Contact Information' d='Shop.Theme.Global'}</h1>
      <span class="cc-contact__divider"></span>
    </header>

    <div class="cc-contact__panel">

      <h2 class="cc-contact__company">
        {l s='Ceramic Connection Logistic' d='Shop.Theme.Global'}
      </h2>

      <div class="cc-contact__info-grid">

        <article class="cc-info-card">
          <div class="cc-info-card__icon">
            <i class="fas fa-map-marker-alt"></i>
          </div>

          <div class="cc-info-card__content">

            <p>
              Avenida Real de Extremadura, 9 Onda 12200<br>
              Castellón, {l s='Spain' d='Shop.Theme.Global'}
            </p>

            <a
              class="cc-link"
              href="https://www.google.com/maps/place/Ceramic+Connection/@39.9523827,-0.1899342,17z/data=!3m1!4b1!4m6!3m5!1s0xd60099c1b0adb19:0xc3c8af6c0bbf8d47!8m2!3d39.9523827!4d-0.1877402!16s%2Fg%2F11m79wxtc_"
              target="_blank"
              rel="noopener"
            >
              {l s='View on Google Maps' d='Shop.Theme.Global'}
            </a>
          </div>
        </article>

        <article class="cc-info-card">
          <div class="cc-info-card__icon">
            <i class="far fa-clock"></i>
          </div>

          <div class="cc-info-card__content">
            <h3>{l s='Horario de atención al cliente' d='Shop.Theme.Global'}:</h3>

            <div class="cc-schedule">
              <span>{l s='From Monday to Friday' d='Shop.Theme.Global'}:</span>
              <strong>{l s='From 9:00 to 14:00' d='Shop.Theme.Global'}</strong>
            </div>
          </div>
        </article>

      </div>

      <section class="cc-language-section">
        <h2>{l s='Contact us in the language you prefer:' d='Shop.Theme.Global'}</h2>

        <div class="cc-language-grid">

          <article class="cc-language-card">
            <div class="cc-language-card__flags">
              <span>
                <img src="/themes/child_classic/assets/img/web/spain.png" alt="Spain flag" loading="lazy">
                ES
              </span>

              <span>
                <img src="/themes/child_classic/assets/img/web/germany.png" alt="Germany flag" loading="lazy">
                DE
              </span>
            </div>

            <ul class="cc-contact-list">
              <li>
                <i class="fas fa-at"></i>
                <a href="mailto:info@ceramicconnection.es">info@ceramicconnection.es</a>
              </li>

              <li>
                <i class="fas fa-phone-alt"></i>
                <a href="tel:+34964188917">+34 964 188 917</a>
              </li>

              <li>
                <i class="fa-brands fa-whatsapp"></i>
                <a href="https://wa.me/34623240148" target="_blank" rel="noopener">
                  +34 623 240 148
                </a>
              </li>
            </ul>
          </article>

          <article class="cc-language-card">
            <div class="cc-language-card__flags">
              <span>
                <img src="/themes/child_classic/assets/img/web/france.png" alt="France flag" loading="lazy">
                FR
              </span>

              <span>
                <img src="/themes/child_classic/assets/img/web/united-kingdom.png" alt="United Kingdom flag" loading="lazy">
                EN
              </span>
            </div>

            <ul class="cc-contact-list">
              <li>
                <i class="fas fa-at"></i>
                <a href="mailto:info@ceramicconnection.es">info@ceramicconnection.es</a>
              </li>

              <li>
                <i class="fas fa-phone-alt"></i>
                <a href="tel:+34964188917">+34 964 188 917</a>
              </li>

              <li>
                <i class="fa-brands fa-whatsapp"></i>
                <a href="https://wa.me/34623240148" target="_blank" rel="noopener">
                  +34 623 240 148
                </a>
              </li>
            </ul>
          </article>

        </div>
      </section>

      <div class="cc-whatsapp-widget">
        {hook h="displayWhatsAppChat" id_whatsappchat="4"}
      </div>

      {block name='ps_social_follow'}
        <footer class="cc-social">
          <h2>{l s='Follow us' d='Shop.Theme.Global'}:</h2>

          {include file="themes/child_classic/modules/ps_socialfollow/ps_socialfollow.tpl" social_links=$socialLinksFooter title=false}
        </footer>
      {/block}

    </div>
  </div>
</section>