
$( document ).ready( function () {

    $('.product-table-cell').on('click', function() {
        var productId = $(this).data('product-id');
        var cartId = $(this).data('id-cart');
    
        $.ajax({
            type: 'POST',
            url: $(this).data('ajax-url'),
            data: {
                action: 'addToCart',
                id_product: productId,
                id_cart: cartId
            },
            success: function(response) {
             
                if (response.success) {
                    console.log('Producto agregado al carrito con éxito');
                } else {
                    console.error('Error al agregar el producto al carrito:', response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', error);
            }
        });
    }); 

    document.querySelectorAll('[data-share-button]').forEach(function(button, index) {  
        button.addEventListener('click', function(event) {
            event.stopPropagation();
            document.querySelectorAll('[data-share-menu]').forEach(function(menu, menuIndex) {
                if (index === menuIndex) {
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                } else {
                    menu.style.display = 'none';
                }
            });
        });
    });

    document.addEventListener('click', function(event) {
        document.querySelectorAll('[data-share-menu]').forEach(function(menu) {
            if (!menu.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
    });

    function copyToClipboard(text) {
        var textField = document.createElement('textarea');
        textField.innerText = text;
        document.body.appendChild(textField);
        textField.select();
        document.execCommand('copy');
        textField.remove();
        alert('Enlace copiado al portapapeles');
    }
    
    document.querySelectorAll('[data-share-id]').forEach(function(button) {
        button.addEventListener('click', function(event) {
            var linkElement = button.getAttribute('data-share-id');
            copyToClipboard(linkElement);
        });
    });

    
    if(document.getElementById('openModal')) {

        document.getElementById('openModal').addEventListener('click', function() {
            var videoContainer = document.getElementById('videoContainer');
            
            // Asegúrate de que el video no se inserte más de una vez
            if (videoContainer.innerHTML.trim() === '') { 
                videoContainer.innerHTML = `
                    <video autoplay loop muted playsinline width="100%" src="/themes/child_classic/assets/video/waste-animation.mp4">
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                `;
            }
        });
    
    }

    if ( document.getElementById("insite-form-container")) {
            //Pasarela de pago
        const checkbox = document.getElementById("conditions_to_approve[terms-and-conditions]");
        const redsysWarning = document.getElementById("redsys-warning-checks");
        const insiteFormContainer = document.getElementById("insite-form-container");

        // Función para manejar el cambio de visibilidad
        function toggleVisibilityRedsysBox() {
            if (checkbox.checked) {
                insiteFormContainer.style.display = "block";
                redsysWarning.style.display = "none";
            } else {
                insiteFormContainer.style.display = "none";
                redsysWarning.style.display = "block";
            }
        }
        
        // Inicializa el estado correcto al cargar la página
        if (checkbox) {
            
            // Escucha el cambio en el checkbox
            checkbox.addEventListener("change", toggleVisibilityRedsysBox);

            toggleVisibilityRedsysBox();
        }
    }
      
    /*

    (function () {
  var banner = document.getElementById('bf-banner');
  if (!banner) return;

  // Si ya se cerró, ocúltalo y no toques nada
  if (document.cookie.indexOf('bf_banner_closed=1') !== -1) {
    banner.style.display = 'none';
    return;
  }

  var header      = document.querySelector('#header, .header-container, header, .page-header, .top-menu');
  var menuCeramic = document.getElementById('menu-ceramic');
  var stickyNav   = document.querySelector('nav.header-nav.header-sticky');

  var mobileMenu     = document.getElementById('menu-mobile-list');
  var burgerInnerDiv = document.querySelector('#openMenuButton + div');

  // Guardar originales (1 vez) para evitar acumulación
  var orig = {
    headerMarginStyle: header ? (header.style.marginTop || '') : null,
    headerMarginNum: header ? (parseFloat(window.getComputedStyle(header).marginTop) || 0) : 0,

    menuTopStyle: menuCeramic ? (menuCeramic.style.top || '') : null,
    menuTopNum: menuCeramic ? (parseFloat(window.getComputedStyle(menuCeramic).top) || 0) : 0,

    stickyTopStyle: stickyNav ? (stickyNav.style.top || '') : null,
    stickyTopNum: stickyNav ? (parseFloat(window.getComputedStyle(stickyNav).top) || 0) : 0,

    mobileTopStyle: mobileMenu ? (mobileMenu.style.top || '') : null,
    mobileTopNum: mobileMenu ? (parseFloat(window.getComputedStyle(mobileMenu).top) || 0) : 0,

    burgerPadStyle: burgerInnerDiv ? (burgerInnerDiv.style.paddingTop || '') : null,
    burgerPadNum: burgerInnerDiv ? (parseFloat(window.getComputedStyle(burgerInnerDiv).paddingTop) || 0) : 0,
  };

  function getBannerHeight() {
    // altura real renderizada (mejor en iPhone que offsetHeight)
    return Math.ceil(banner.getBoundingClientRect().height || 0);
  }

  function resetToOriginal() {
    if (header && orig.headerMarginStyle !== null) header.style.marginTop = orig.headerMarginStyle;

    if (menuCeramic && orig.menuTopStyle !== null) menuCeramic.style.top = orig.menuTopStyle;
    if (stickyNav && orig.stickyTopStyle !== null) stickyNav.style.top = orig.stickyTopStyle;

    if (mobileMenu && orig.mobileTopStyle !== null) mobileMenu.style.top = orig.mobileTopStyle;
    if (burgerInnerDiv && orig.burgerPadStyle !== null) burgerInnerDiv.style.paddingTop = orig.burgerPadStyle;
  }

  function applyDesktop(h) {
    // === lo que a ti ya te funcionaba en escritorio ===
    if (header) {
      header.style.marginTop = (orig.headerMarginNum + h) + 'px';
    }

    if (menuCeramic) {
      menuCeramic.style.top = (orig.menuTopNum + h) + 'px';
    }

    if (stickyNav) {
      stickyNav.style.top = (orig.stickyTopNum + h) + 'px';
    }
  }

  function applyMobile(h) {
    // === móvil “iPhone-safe”: empujar solo lo fijo/sticky con TOP ===
    // (si alguno no es fixed/sticky, no lo tocamos para evitar barra gris)
    if (stickyNav) {
      var posNav = window.getComputedStyle(stickyNav).position;
      if (posNav === 'fixed' || posNav === 'sticky') {
        stickyNav.style.top = (orig.stickyTopNum + h) + 'px';
      }
    }

    if (menuCeramic) {
      var posMenu = window.getComputedStyle(menuCeramic).position;
      if (posMenu === 'fixed' || posMenu === 'sticky') {
        menuCeramic.style.top = (orig.menuTopNum + h) + 'px';
      }
    }

    if (header) {
      var posHeader = window.getComputedStyle(header).position;
      // Si tu header móvil realmente es fijo/sticky, empújalo también:
      if (posHeader === 'fixed' || posHeader === 'sticky') {
        // Muchísimos temas usan top:0 aquí; lo empujamos sin acumular
        header.style.marginTop = orig.headerMarginStyle; // por si acaso
        header.style.top = (h) + 'px';
      } else {
        // Si NO es fijo, margin-top funciona bien
        header.style.marginTop = (orig.headerMarginNum + h) + 'px';
      }
    }

    // Menú móvil desplegable: si se te tapa al abrir, aquí sí conviene sumar h
    if (mobileMenu) {
      mobileMenu.style.top = (orig.mobileTopNum + h) + 'px';
    }
    if (burgerInnerDiv) {
      burgerInnerDiv.style.paddingTop = (orig.burgerPadNum + h) + 'px';
    }
  }

  function update() {
    if (banner.style.display === 'none') return;

    var h = getBannerHeight();
    if (!h) return;

    // Reset antes de aplicar para que jamás se acumule
    resetToOriginal();

    if (window.innerWidth >= 1224) {
      applyDesktop(h);
    } else {
      applyMobile(h);
    }
  }

  function scheduleUpdate() {
    requestAnimationFrame(function () {
      update();
      // segundo pase por iOS (fuentes / reflow)
      requestAnimationFrame(update);
    });
  }

  window.addEventListener('load', scheduleUpdate);
  window.addEventListener('resize', scheduleUpdate);
  scheduleUpdate();

  // Botón cerrar
  var closeBtn = banner.querySelector('.bf-banner__close');
  if (closeBtn) {
    closeBtn.addEventListener('click', function () {
      banner.style.display = 'none';
      resetToOriginal();

      var d = new Date();
      d.setDate(d.getDate() + 7);
      document.cookie = 'bf_banner_closed=1; path=/; expires=' + d.toUTCString();
    });
  }
    })();
    */
    
    (function () {
        var banner = document.getElementById('bf-banner');
        if (!banner) return;

        if (document.cookie.indexOf('bf_banner_closed=1') !== -1) {
            banner.style.display = 'none';
            return;
        }

        // Candidatos típicos de header/nav en temas Prestashop
        var selectors = [
            '#header',
            '.header-container',
            'header',
            '.page-header',
            '.top-menu',
            '.header-top',
            '.header-middle',
            '.header-bottom',
            '.header-wrapper',
            '.header-nav',
            'nav.header-nav',
            'nav.header-nav.header-sticky',
            '.header-sticky',
            '.sticky-header',
            '#menu-ceramic'
        ];

        // Recolecta nodos únicos
        var nodes = [];
        selectors.forEach(function (sel) {
            document.querySelectorAll(sel).forEach(function (el) {
            if (nodes.indexOf(el) === -1) nodes.push(el);
            });
        });

        // Guardar originales una sola vez (idempotente)
        function remember(el) {
            if (el.dataset.bfSaved === '1') return;
            var cs = window.getComputedStyle(el);
            el.dataset.bfSaved = '1';
            el.dataset.bfOrigTopNum = String(parseFloat(cs.top) || 0);
            el.dataset.bfOrigTopStyle = el.style.top || '';
            el.dataset.bfOrigPos = cs.position || '';
        }

        function bannerHeight() {
            return Math.ceil(banner.getBoundingClientRect().height || 0);
        }

        function reset() {
            nodes.forEach(function (el) {
            if (el.dataset.bfSaved === '1') {
                el.style.top = el.dataset.bfOrigTopStyle;
            }
            });
        }

        function apply() {
            // Detectar iOS (iPhone/iPad/iPod) + iPadOS (MacIntel con touch)
            var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
            let extraHeigt = 0;
            let closeIconPadding = 58;
            if (banner.style.display === 'none') return;

            // ==== MENÚ MÓVIL ==== 
            var burgerInnerDiv = document.querySelector('#openMenuButton + div'); 
            var originalBurgerPadding = null; 
            console.log('buscando resolución...');
            if (window.innerWidth < 1224) { 

                var bodyId = document.body.id;

                if (bodyId === 'category') {
                    var leftColumn = document.getElementById('left-column');
                    if (leftColumn) {
                        leftColumn.style.marginTop = '55px';
                    }
                }

                if (bodyId === 'product' || bodyId === 'contact') {
                    var contentWrapper = document.getElementById('content-wrapper');
                    if (contentWrapper) {
                        contentWrapper.style.marginTop = '55px';
                    }
                }

                if (!isIOS) {
                    //extraHeigt = 25;
                    //closeIconPadding = 58;
                }
                // div debajo de #openMenuButton → +30 al padding-top (5 → 35) 
                if (burgerInnerDiv) { 
                    var csBurger = window.getComputedStyle(burgerInnerDiv); 
                    originalBurgerPadding = csBurger.paddingTop; // ej. "5px" 
                    burgerInnerDiv.style.paddingTop = closeIconPadding + 'px'; 
                } 
            }else{
                var bodyId = document.body.id;
                
                if (bodyId === 'category') {
                    var section = document.getElementById('wrapper');
                    if (section) {
                        section.style.paddingTop = '78px';
                    }
                }

                if (bodyId === 'product' || bodyId === 'contact') {
                    var section = document.getElementById('main');
                    if (section) {
                        section.style.paddingTop = '30px';
                    }
                }
            }

            var h = bannerHeight() + extraHeigt;
            if (!h) return;

            // Reset antes de aplicar para no acumular
            reset();

            // Empuja TODOS los fixed/sticky (iPhone + Android)
            nodes.forEach(function (el) {
            remember(el);

            var cs = window.getComputedStyle(el);
            var pos = cs.position;

            if (pos === 'fixed' || pos === 'sticky') {
                var baseTop = parseFloat(el.dataset.bfOrigTopNum) || 0;
                el.style.top = (baseTop + h) + 'px';
            }
            });
        }

        function scheduleApply() {
            requestAnimationFrame(function () {
            apply();
            requestAnimationFrame(apply);
            });
        }

        window.addEventListener('load', scheduleApply);
        window.addEventListener('resize', scheduleApply);
        scheduleApply();

        // Cerrar banner
        var closeBtn = banner.querySelector('.bf-banner__close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
            banner.style.display = 'none';
            reset();

            var d = new Date();
            d.setDate(d.getDate() + 7);
            document.cookie = 'bf_banner_closed=1; path=/; expires=' + d.toUTCString();
            });
        }
    })();
            

});


// Escuchar el evento 'updateFacets' de Prestashop para actualizar la meta tag de robots
prestashop.on('updateFacets', function() {
    let metaTag = document.querySelector('meta[name="robots"]');
    metaTag.setAttribute('content', 'noindex, nofollow');
});
    



