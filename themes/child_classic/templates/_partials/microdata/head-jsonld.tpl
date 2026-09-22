
{if $language.iso_code == 'fr'}
  {assign var="pinterest_url" value="https://www.pinterest.fr/ceramicconnectionofficial/"}
  {assign var="org_description" value="Boutique en ligne de carreaux, dalles céramiques et autres matériaux de construction, avec distribution multilingue en Espagne, France, Royaume-Uni, Allemagne, Portugal, Pays-Bas et Belgique."}
{elseif $language.iso_code == 'de'}
  {assign var="pinterest_url" value="https://www.pinterest.de/ceramicconnectionofficial/"}
  {assign var="org_description" value="Online-Shop für Fliesen, Keramikböden und andere Baumaterialien mit mehrsprachigem Vertrieb in Spanien, Frankreich, dem Vereinigten Königreich, Deutschland, Portugal, den Niederlanden und Belgien."}
{elseif $language.iso_code == 'pt'}
  {assign var="pinterest_url" value="https://www.pinterest.pt/ceramicconnectionofficial/"}
  {assign var="org_description" value="Loja online de azulejos, ladrilhos cerâmicos e outros materiais de construção, com distribuição multilíngue em Espanha, França, Reino Unido, Alemanha, Portugal, Países Baixos e Bélgica."}
{elseif $language.iso_code == 'nl'}
  {assign var="pinterest_url" value="https://www.pinterest.com/ceramicconnectionofficial/"}
  {assign var="org_description" value="Online winkel voor tegels, keramische vloertegels en andere bouwmaterialen, met meertalige distributie in Spanje, Frankrijk, het Verenigd Koninkrijk, Duitsland, Portugal, Nederland en België."}
{elseif $language.iso_code == 'en'}
  {assign var="pinterest_url" value="https://pinterest.com/ceramicconnectionofficial/"}
  {assign var="org_description" value="Online store for tiles, ceramic flooring and other construction materials, with multilingual distribution across Spain, France, the United Kingdom, Germany, Portugal, the Netherlands and Belgium."}
{else}
  {assign var="pinterest_url" value="https://www.pinterest.es/ceramicconnectionofficial/"}
  {assign var="org_description" value="Tienda online de azulejos, baldosas cerámicas y otros materiales de construcción, con distribución multiidioma en España, Francia, Reino Unido, Alemania, Portugal, Países Bajos y Bélgica."}
{/if}
<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "OnlineStore",
    "@id": "https://ceramicconnection.com/#organization",
    "name": "Ceramic Connection",
    "legalName": "Ceramic Connection Shop S.L.",
    "url": "{$urls.pages.index}",
    {if $shop.logo_details}
      "logo": {
        "@type": "ImageObject",
        "url": "{$shop.logo_details.src|replace:'http://':'https://'}",
        "width": 200,
        "height": 100
      },
    {/if}
    "description": "{$org_description}",
    "telephone": "+34623240148",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Avenida Real de Extremadura, 9",
      "postalCode": "12200",
      "addressLocality": "Onda",
      "addressRegion": "Castellón",
      "addressCountry": "ES"
    },
    "sameAs": [
      "https://www.wikidata.org/wiki/Q141430369",
      "https://www.facebook.com/profile.php?id=100087192722741",
      "https://www.instagram.com/ceramicconnection",
      "{$pinterest_url}",
      "https://www.tiktok.com/@ceramicconnection",
      "https://www.youtube.com/channel/UCQ9V46QZ1KdM6E1gF1AqPQA"
    ],
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+34623240148",
      "contactType": "customer service",
      "areaServed": ["ES", "FR", "GB", "DE", "PT", "NL", "BE"],
      "availableLanguage": ["es", "fr", "en", "de", "pt", "nl"]
    },
    "hasCertification": {
      "@type": "Certification",
      "name": "Trusted Shops Trustmark",
      "certificationIdentification": "https://www.trustedshops.es/evaluacion/ceramicconnection-com",
      "certificationStatus": "https://schema.org/CertificationActive",
      "url": "https://www.trustedshops.es/evaluacion/ceramicconnection-com",
      "validFrom": "2023-11-10T14:52:28+01:00",
      "dateModified": "2026-09-11",
      "issuedBy": {
        "@type": "Organization",
        "@id": "https://www.trustedshops.com/#organization",
        "name": "Trusted Shops",
        "url": "https://www.trustedshops.com",
        "sameAs": [
          "https://business.trustedshops.com/",
          "https://www.trstd.com/"
        ]
      }
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": 4.4,
      "ratingCount": 269,
      "bestRating": 5,
      "worstRating": 1
    }
  }
</script>

{if $page.page_name == 'category'}

  {assign var="title_cleaned" value=$page.meta.title|regex_replace:"/\|.*$/":""|trim}
  
  <script type="application/ld+json">
    {

      "@context": "http://schema.org",
      "@type": "Product",
      "name": "{$title_cleaned}",
      "image": "",
      "url":  "{$urls.current_url}",
      "offers": {
        "@type": "AggregateOffer",
        "offerCount": "{$schemaCategoryData.total_items}",
        "priceCurrency": "EUR",
        "lowPrice": "{$schemaCategoryData.min_price}",
        "highPrice": "{$schemaCategoryData.max_price}"
      }

    }
  </script>

{else}
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "isPartOf": {
        "@type": "WebSite",
        "url":  "{$urls.pages.index}",
        "name": "{$shop.name}"
      },
      "name": "{$page.meta.title}",
      "url":  "{$urls.current_url}"
    }
  </script>
{/if}



{if $page.page_name == 'index'}
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "url" : "{$urls.pages.index}",
      {if $shop.logo_details}
        "image": {
          "@type": "ImageObject",
          "url":"{$shop.logo_details.src|replace:'http://':'https://'}"
        },
      {/if}
      "potentialAction": {
        "@type": "SearchAction",
        "target": "{'--search_term_string--'|str_replace:'{search_term_string}':$link->getPageLink('search',true,null,['search_query'=>'--search_term_string--'])}",
        "query-input": "required name=search_term_string"
      }
    }
  </script>
{/if}

{if isset($categoria_faq_pairs) && $categoria_faq_pairs|@count > 0}
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        {foreach from=$categoria_faq_pairs item=faq name=faqLoop}
          {
            "@type": "Question",
            "name": "{$faq.question|escape:'html':'UTF-8'}",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "{$faq.answer|escape:'html':'UTF-8'}"
            }
          }{if !$smarty.foreach.faqLoop.last},{/if}
        {/foreach}
      ]
    }
  </script>
{/if}

{if isset($breadcrumb.links[1])}
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        {foreach from=$breadcrumb.links item=path name=breadcrumb}
          {
            "@type": "ListItem",
            "position": {$smarty.foreach.breadcrumb.iteration},
            "name": "{$path.title}",
            "item": "{$path.url}"
          }{if !$smarty.foreach.breadcrumb.last},{/if}
        {/foreach}
      ]
    }
  </script>
{/if}
