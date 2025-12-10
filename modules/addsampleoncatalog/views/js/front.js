
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

    
    function convertSlugToTitle(slug) {
        // Eliminar la extensión .html
        let stringWithoutExtension = slug.replace('.html', '');
        
        // Reemplazar guiones por espacios
        let stringWithSpaces = stringWithoutExtension.replace(/-/g, ' ');
        
        // Capitalizar cada palabra
        let capitalizedString = stringWithSpaces.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
        
        return capitalizedString;
    }

    function getColorPath(url, color, lang) {

        let originalColorName = color;
        // Reemplazar caracteres con acentos por su equivalente sin acento
        color = color.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        
        color = color.toLowerCase();

        color = color.replace(/\s+/g, '-');

        let path = {'url' : url};
        if(lang == 'es') {
            path.url += '/azulejos/color/' + color;
            path.name = 'Azulejos Color ' + originalColorName;
        }
        
        if(lang == 'fr') {
            path.url += '/carrelage/couleur/' + color;
            path.name = 'Carrelage Couleur ' + originalColorName;
        }
        
        if(lang == 'en') {
            path.url += '/tiles/color/' + color;
            path.name = originalColorName + ' Tiles';
        }
        
        if(lang == 'de') {
            path.url += '/fliesen/farbe/' + color;
            path.name = originalColorName + ' Fliesen';
        }
        
        if(lang == 'pt') {
            path.url += '/azulejos/cor/' + color;
            path.name = 'Azulejos Cor ' + originalColorName;
        }
        
        if(lang == 'nl') {
            path.url += '/tegels/kleur/' + color;
            path.name = originalColorName + ' Tegels';
        }
        
        return path;
    }

    if (document.getElementById('bread-crumps-container')) {
        const currentUrl = window.location.href;

        // Parsear la URL para obtener las partes necesarias
        const url = new URL(currentUrl);
        const pathSegments = url.pathname.split('/').filter(segment => segment.length > 0);

        // Base URL (ceramic/lang)
        const baseUrl = url.origin + '/' + pathSegments[0] ;

        let homeText = 'azulejos';
        let tileText = "Azulejos";
        let bold = 'style="font-weight:bolder; color:black"';
                
        // si color != '' o color != 'none' breadcrump de producto, sino, de categoría
        let color = document.getElementById('bread-crumps-container').dataset.color;
        let location = document.getElementById('bread-crumps-container').dataset.location;
        let metatitle = document.getElementById('bread-crumps-container').dataset.title;
        let fatherTitle = document.getElementById('bread-crumps-container').dataset.father;
        let fatherCategory = document.getElementById('bread-crumps-container').dataset.dadcategory;
        let breadCrumpsHtml = ''; 
        
    
        //textos base
        if (pathSegments[0] == 'fr') {
            homeText = 'carrelage';
            tileText = 'Carrelage';
        }

        if (pathSegments[0] == 'en') {
            homeText = 'tiles';
            tileText = 'Tiles';
        }

        if (pathSegments[0] == 'pt') {
            homeText = 'azulejos';
            tileText = 'Azulejos';
        }
        if (pathSegments[0] == 'de') {
            tileText = 'Fliesen';
            homeText = 'fliesen';
        }

        if (pathSegments[0] == 'nl') {
            tileText = 'Tegels';
            homeText = 'tegels';
        }

        let currentPath = baseUrl;
        let ignore = false;

        pathSegments.forEach((segment, index) => {

            //DETERMINA CUANDO IGNORA UNA MIGA EN CATEGORÍAS
            ignore = false;
            
            if (fatherCategory != 88 && index == 2) {
                ignore = true;
            }
            if (pathSegments.length >= 5 && index == 1){
                ignore = true;
            }
            if (fatherCategory == 88 && index == 1) {
                ignore = true;
            }
            
            if(index != 0 ){

                if (index != 1) {
                    //Si index es distinto de (azulejos, otros materiales, etc...) quita negrita
                    bold = '';
                }
                if (pathSegments.length >= 5 && index == 3){
                    bold = 'style="font-weight:bolder; color:black"';
                }
                if (fatherCategory == 88 && index == 2){
                    bold = 'style="font-weight:bolder; color:black"';
                }

                // Actualizar la ruta actual
                currentPath += '/' + segment ;

                if (index === pathSegments.length - 1) {
                    // Último segmento, solo texto
                    if(!document.getElementById('aspecto-link')) { //breadcrum en categoria

                        if (color != 'none') {

                            path = getColorPath(baseUrl, color, pathSegments[0]);
                            breadCrumpsHtml += '<a href="' + path.url +'" data-index="'+ index +'" '+ bold +'>' + path.name + '</a> > ';
                  
                        }

                    }else{ //breadcrum en producto
                        let aspectoLinkDiv = document.getElementById('aspecto-link');
                        let linkElement = aspectoLinkDiv.querySelector('a');
                        let linkHref = linkElement.getAttribute('href');
                        let linkText = linkElement.innerHTML;
                        
                        breadCrumpsHtml += '<a href="' + linkHref + '" data-index="'+ index +'" '+ bold +'>' + tileText + ' ' + linkText + '</a> > ';
                        
                    }

                    if (location == 'category') {

                        breadCrumpsHtml += metatitle;

                    }else{

                        breadCrumpsHtml += convertSlugToTitle(segment);

                    }
                    

                } else {
                    // Segmetos intermedios, añadir enlace

                    if (location == 'category') { //breadcrum en categoria

                        if (!ignore) {  
                            if (bold != '' && index == 3) {
                                breadCrumpsHtml += ' <a href="' + currentPath +'" '+ bold +' data-index="'+ index +'">' + fatherTitle + '</a> > ';
                            }else{
                                breadCrumpsHtml += ' <a href="' + currentPath +'" '+ bold +' data-index="'+ index +'">' + convertSlugToTitle(segment) + '</a> > '; 
                            }  
                             
                        }

                    }else{ //breadcrum en producto 
                         
                        let categoryLinkDiv = document.getElementById('category-link');
                        let categoryQuerySelector = categoryLinkDiv.querySelector('a');
                        let categoryLink = categoryQuerySelector.getAttribute('href');
                        
                        if(document.getElementById('aspecto-link') && index == 1) {
                            //agrego 's' antes del cierre de la etiqueta para hacer el plural
                            breadCrumpsHtml += ' <a href="' + categoryLink +'" '+ bold +' data-index="'+ index +'">' + convertSlugToTitle(segment) + 's</a> > ';
                        }
                        else if(!document.getElementById('aspecto-link') && index == 1) {
                            breadCrumpsHtml += ' <a href="' + categoryLink +'" '+ bold +' data-index="'+ index +'">' + convertSlugToTitle(segment) + '</a> > ';
                        } else{
                            breadCrumpsHtml += ' <a href="' + currentPath +'" '+ bold +' data-index="'+ index +'">' + convertSlugToTitle(segment) + '</a> > ';
                        }
                        
                    }

                }
            }
        });

        // Insertar las migas de pan en el div 
    
        document.getElementById('bread-crumps-container').innerHTML = breadCrumpsHtml;
    }

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
      
    if (document.getElementById('bf-banner')) {
        var banner = document.getElementById('bf-banner');
        if (!banner) return;

        // Si ya se cerró, ocúltalo y no toques nada
        if (document.cookie.indexOf('bf_banner_closed=1') !== -1) {
            banner.style.display = 'none';
            return;
        }

        var bannerHeight = banner.offsetHeight || 30;

        // ==== HEADER (margen gris) ====
        var header = document.querySelector('#header, .header-container, header, .page-header, .top-menu');
        var originalHeaderMarginTop = null;
        var originalBodyPaddingTop = null;

        if (header) {
            originalHeaderMarginTop = header.style.marginTop || '';
            header.style.marginTop = bannerHeight + 'px';
        } else {
            originalBodyPaddingTop = document.body.style.paddingTop || '';
            document.body.style.paddingTop = bannerHeight + 'px';
        }

        // ==== NAV Y MENU DESKTOP (se aplica en todos los tamaños) ====
        var menuCeramic = document.getElementById('menu-ceramic');
        var stickyNav   = document.querySelector('nav.header-nav.header-sticky');

        var originalMenuTop = null;
        var originalStickyNavTop = null;

        if (menuCeramic) {
            var csMenu = window.getComputedStyle(menuCeramic);
            originalMenuTop = csMenu.top; // p.ej. "104px"
            var currentMenuTop = parseFloat(csMenu.top) || 0;
            menuCeramic.style.top = (currentMenuTop + bannerHeight) + 'px';
        }

        if (stickyNav) {
            var csNav = window.getComputedStyle(stickyNav);
            originalStickyNavTop = csNav.top; // p.ej. "0px"
            var currentNavTop = parseFloat(csNav.top) || 0;
            stickyNav.style.top = (currentNavTop + bannerHeight) + 'px';
        }

        // ==== MENÚ MÓVIL ====
        var mobileMenu = document.getElementById('menu-mobile-list');
        var burgerInnerDiv = document.querySelector('#openMenuButton + div');

        var originalMobileTop = null;
        var originalBurgerPadding = null;

        if (window.innerWidth < 1224) {
            // #menu-mobile-list → +32px al top
            if (mobileMenu) {
                var csMobile = window.getComputedStyle(mobileMenu);
                originalMobileTop = csMobile.top; // ej. "0px"
                var currentMobileTop = parseFloat(csMobile.top) || 0;
                mobileMenu.style.top = (currentMobileTop + 32) + 'px';
            }

            // div debajo de #openMenuButton → +30 al padding-top (5 → 35)
            if (burgerInnerDiv) {
                var csBurger = window.getComputedStyle(burgerInnerDiv);
                originalBurgerPadding = csBurger.paddingTop; // ej. "5px"
                var currentPadding = parseFloat(csBurger.paddingTop) || 5;
                burgerInnerDiv.style.paddingTop = (currentPadding + 30) + 'px';
            }
        }

        // ==== BOTÓN CERRAR ====
        var closeBtn = banner.querySelector('.bf-banner__close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                banner.style.display = 'none';

                // Restaurar header / body
                if (header && originalHeaderMarginTop !== null) {
                    header.style.marginTop = originalHeaderMarginTop;
                }
                if (!header && originalBodyPaddingTop !== null) {
                    document.body.style.paddingTop = originalBodyPaddingTop;
                }

                // Restaurar menuCeramic y stickyNav
                if (menuCeramic && originalMenuTop !== null) {
                    menuCeramic.style.top = originalMenuTop;
                }
                if (stickyNav && originalStickyNavTop !== null) {
                    stickyNav.style.top = originalStickyNavTop;
                }

                // Restaurar menú móvil
                if (mobileMenu && originalMobileTop !== null) {
                    mobileMenu.style.top = originalMobileTop;
                }
                if (burgerInnerDiv && originalBurgerPadding !== null) {
                    burgerInnerDiv.style.paddingTop = originalBurgerPadding;
                }

                // Guardar cookie para no mostrarlo más
                var d = new Date();
                d.setDate(d.getDate() + 7);
                document.cookie = 'bf_banner_closed=1; path=/; expires=' + d.toUTCString();
            });
        }
    }



});


// Escuchar el evento 'updateFacets' de Prestashop para actualizar la meta tag de robots
prestashop.on('updateFacets', function() {
    let metaTag = document.querySelector('meta[name="robots"]');
    metaTag.setAttribute('content', 'noindex, nofollow');
});
    



