$( document ).ready( function () {

   let myGlobal = [];
   
   let initializeCustom = function () {
      if(document.getElementById('desktop-product-images')) {
         $(document).on('click', '.images-container .layer', function () {
            const idx = parseInt($(this).data('iteration'), 10) - 1;

            $('#product-modal').one('shown.bs.modal', function () {
               const $m = $(this);

               // Espera a que el carrusel/galería termine de inicializar
               setTimeout(function () {
                  // Ajusta este selector al de las miniaturas dentro del modal
                  const $thumbs = $m.find('.js-modal-thumb, .js-thumb'); 
                  $thumbs.eq(idx).trigger('click');
               }, 0);
            });
         });
      }

      if (document.getElementById('board-section')) {
         document.querySelectorAll('[data-cards-carousel]').forEach(root => {
            const track = root.querySelector('.cards-track');
            const slides = Array.from(root.querySelectorAll('.cards-slide'));
            const prev = root.querySelector('[data-cards-prev]');
            const next = root.querySelector('[data-cards-next]');

            let i = 0;

            function update() {
               const w = root.querySelector('.cards-viewport').getBoundingClientRect().width;
               track.style.transform = `translateX(-${i * w}px)`;
               prev.disabled = (i === 0);
               next.disabled = (i === slides.length - 1);
            }

            prev.addEventListener('click', () => { i = Math.max(0, i - 1); update(); });
            next.addEventListener('click', () => { i = Math.min(slides.length - 1, i + 1); update(); });
            window.addEventListener('resize', update);

            update();
         });
      }      
      /* ocultar images large */
      $(function () {
         var $productModal = $('#product-modal');

         // Si no existe el modal, no hacemos nada
         if (!$productModal.length) {
            return;
         }

         // Cuando el modal se ha mostrado
         $productModal.on('shown.bs.modal', function () {
            var $cover = $(this).find('.js-modal-product-cover');

            if (!$cover.length) {
               return;
            }

            var largeSrc = $cover.data('large-src');

            // Si no hay large o ya se ha cargado antes, salimos
            if (!largeSrc || $cover.data('large-loaded')) {
               return;
            }

            // Cambiamos el src a la versión grande
            $cover.attr('src', largeSrc);

            // Marcamos que ya la hemos cargado para no repetir
            $cover.data('large-loaded', true);
         });
      });


      /* QUITAR ENLACES MENU */
      document
         .querySelectorAll('#menu-desktop-list a[href="#"]')
         .forEach(a => a.removeAttribute('href'));

      document
         .querySelectorAll('#menu-mobile-list a[href="#"]')
         .forEach(a => a.removeAttribute('href'));

      /* MOSTRAR PROMO CODE */
      if(document.getElementById('show-promo-code')) {
         $('#promo-code').css('display', 'block');
      }else{
         $('#promo-code').css('display', 'none');
      }

      /* CARRUSEL JUNTAS */
      if (document.getElementById('board-section')){

         const track = document.querySelector(".carousel-track");
         const images = track.querySelectorAll("img");
         const prevBtn = document.querySelector(".carousel-btn.prev");
         const nextBtn = document.querySelector(".carousel-btn.next");
         let index = 0;

         function updateCarousel() {
            const width = images[0].clientWidth;
            track.style.transform = `translateX(-${index * width}px)`;
         }

         nextBtn.addEventListener("click", () => {
            index = (index + 1) % images.length;
            updateCarousel();
         });

         prevBtn.addEventListener("click", () => {
            index = (index - 1 + images.length) % images.length;
            updateCarousel();
         });

         window.addEventListener("resize", updateCarousel);

         // ---- Soporte táctil para móviles ----
         let startX = 0;
         let moveX = 0;

         track.addEventListener("touchstart", (e) => {
            startX = e.touches[0].clientX;
         });

         track.addEventListener("touchmove", (e) => {
            moveX = e.touches[0].clientX - startX;
         });

         track.addEventListener("touchend", () => {
            if (moveX > 50) { // swipe derecha
               index = (index - 1 + images.length) % images.length;
               updateCarousel();
            } else if (moveX < -50) { // swipe izquierda
               index = (index + 1) % images.length;
               updateCarousel();
            }
            moveX = 0;
         });

      }
  
      /* CAROUSEL PRODUCTOS COMPLEMENTARIOS */

      if (document.getElementById('complement-products-box')) {
         /* CARRUSEL IMÁGENES EN CADA BOARD-CARD (COMPLEMENTS) */

         const carousels = document.querySelectorAll("[data-img-carousel]");

         carousels.forEach((carousel) => {
            const track = carousel.querySelector("[data-img-track]");
            const images = track ? track.querySelectorAll("img") : [];
            const prevBtn = carousel.querySelector("[data-img-prev]");
            const nextBtn = carousel.querySelector("[data-img-next]");

            // Si no hay suficientes imágenes, no hace falta carrusel
            if (!track || images.length <= 1) {
               if (prevBtn) prevBtn.style.display = "none";
               if (nextBtn) nextBtn.style.display = "none";
               return;
            }

            let index = 0;
            let startX = 0;
            let moveX = 0;

            function getSlideWidth() {
               // Mejor que images[0].clientWidth cuando hay imágenes lazy o tamaños variables
               const first = images[0];
               const w = first.getBoundingClientRect().width;
               return w || first.clientWidth || carousel.clientWidth;
            }

            function updateCarousel() {
               const width = getSlideWidth();
               track.style.transform = `translateX(-${index * width}px)`;
            }

            function goNext() {
               index = (index + 1) % images.length;
               updateCarousel();
            }

            function goPrev() {
               index = (index - 1 + images.length) % images.length;
               updateCarousel();
            }

            nextBtn && nextBtn.addEventListener("click", goNext);
            prevBtn && prevBtn.addEventListener("click", goPrev);

            // Recalcular si cambia el tamaño
            window.addEventListener("resize", updateCarousel);

            // Si las imágenes cargan después (lazy), re-ajustar
            images.forEach((img) => img.addEventListener("load", updateCarousel));

            // Soporte táctil móvil (por carrusel)
            track.addEventListener("touchstart", (e) => {
               startX = e.touches[0].clientX;
               moveX = 0;
            });

            track.addEventListener("touchmove", (e) => {
               moveX = e.touches[0].clientX - startX;
            });

            track.addEventListener("touchend", () => {
               if (moveX > 50) goPrev();        // swipe derecha
               else if (moveX < -50) goNext();  // swipe izquierda
               moveX = 0;
            });

            // Estado inicial
            updateCarousel();
         });

      }

      /* BOTONES DE COMPRA FLOTANTES */

      if(document.getElementById('sticky-sentinel')) {

         const wrapper = document.getElementById("add-wrapper");
         const sentinel = document.getElementById("sticky-sentinel");

         let lastScrollY = window.scrollY;
         let stuck = false; // si está anclado en su sitio
         let floating = true; // si está flotando

         const observer = new IntersectionObserver(
            ([entry]) => {
               const currentScrollY = window.scrollY;

               if (entry.isIntersecting) {
               stuck = true;
               floating = false;
               wrapper.classList.add("sticky-stop");
               } else {
               if (currentScrollY < lastScrollY) {
                  // Scroll hacia arriba → volver a flotante
                  floating = true;
                  stuck = false;
                  wrapper.classList.remove("sticky-stop");
               }
               }

               lastScrollY = currentScrollY;
            },
            {
               root: null,
               threshold: 0
            }
         );

         observer.observe(sentinel);
      }



      let $body = $( 'body' );

      $('.hideThisCheck').css('display', 'none');

      $( '.search_change_visualization' ).find( 'button' ).on( 'click', function () {

         let $currentDisplay = $( this );

         const currentDisplay = parseInt( $currentDisplay.data( 'display' ) );



         $( '#js-product-list' ).find( '.js-product' ).each( function () {

            if ( currentDisplay === 4 ) {

               $( this ).removeClass( 'col-xl-3' );

               $( this ).removeClass( 'col-md-4' );

               $( this ).removeClass( 'col-xs-12' );

               $( this ).addClass( 'col-xl-6' );

               $( this ).addClass( 'col-md-6' );

               $( this ).addClass( 'col-xs-6' );

               $currentDisplay.attr( 'data-display', 2 );

               $currentDisplay.data( 'display', '2' );

               $currentDisplay.html( '<span class="column-visualization four-columns"></span>\n' +

                  '<span class="column-visualization not-first four-columns"></span>' +

                  '<span class="column-visualization not-first four-columns"></span>' +

                  '<span class="column-visualization not-first four-columns"></span>' );

            } else {

               $( this ).removeClass( 'col-xl-6' );

               $( this ).removeClass( 'col-md-6' );

               $( this ).removeClass( 'col-xs-6' );

               $( this ).addClass( 'col-xl-3' );

               $( this ).addClass( 'col-md-4' );

               $( this ).addClass( 'col-xs-12' );

               $currentDisplay.attr( 'data-display', 4 );

               $currentDisplay.data( 'display', '4' );

               $currentDisplay.html( '<span class="column-visualization"></span>\n' +

                  '<span class="column-visualization not-first"></span>' );

            }

         } );

      } );



      $( '#search_filters' ).find( '.accordion' ).each( function () {

         accordionAction( $( this ) );

      } );



      $( '#faq' ).find( '.accordion' ).each( function () {

         accordionAction( $( this ) );

      } );



      function accordionAction( selector ) {

         selector.on( 'click', function () {

            const label = $( this ).data( 'label' );



            if ( $( this ).hasClass( 'active' ) ) {

               $( this ).removeClass( 'active' );

               $( this ).next().css( 'display', 'none' );



               myGlobal.splice( $.inArray( label, myGlobal ) );

            } else {

               $( this ).addClass( 'active' );

               $( this ).next().css( 'display', 'block' );



               if ( $.inArray( label, myGlobal ) === -1 ) {

                  myGlobal.push( label );

               }

            }

         } );

      }



      let $searchWidget = $( '.search-overlay' );

      $searchWidget.detach().insertAfter( $body );



      $( '#search_widget_button' ).on( 'click', function () {

         $searchWidget.show();

         $( '#search_widget' ).find( '.ui-autocomplete-input' ).focus();

      } );



      $searchWidget.on( 'click', function ( e ) {

         if ( e.target !== this ) {

            return;

         }

         $( this ).hide();

      } );



      let $productAccordionButtons = $( '.product-accordion' ).find( 'button' );

      $productAccordionButtons.each( function () {

         const $currentButton = $( this );



         $currentButton.on( 'click', function () {

            $productAccordionButtons.each( function () {

               if ( $( this )[ 0 ] === $currentButton[ 0 ] ) {

                  let next = $( this ).next();



                  if ( next.is( ':visible' ) ) {

                     next.hide();

                     $( this ).removeClass( 'accordion-active' );

                  } else {

                     next.show();

                     $( this ).addClass( 'accordion-active' );

                  }

               } else {

                  $( this ).next().hide();

                  $( this ).removeClass( 'accordion-active' );

               }

            } );

         } );

      } );



      const $surfaceInput = $( '#surface-input' );

      const $piecesInput = $( '#pieces-input' );

      const $quantityInput = $( '#quantity-input' );

      const $eurosInput = $( '#euros-input' );

      const $m2TotalMeters = $( '#m2TotalMeters');

      const $m2TotalPrice = $( '#m2TotalPrice');

      const $m2subtotal = $( '#m2PriceNoDiscount');

      const $m2TotalDiscount = $( '#m2TotalDiscount');

      const $pieceTotalMeters = $( '#pieceTotalMeters');

      const $surfaceInputReal = $( '#surface-real' );

      const $piecesInputReal = $( '#pieces-real' );



      let m2Caja = $surfaceInput.attr( 'data-m2-caja' );

      if ( m2Caja !== undefined ) {

         m2Caja = parseFloat( m2Caja.replace( ',', '.' ) );

      }

      let piezasCaja = $piecesInput.attr( 'data-piezas-caja' );
      let linealMeters = $piecesInput.attr( 'data-lineal-meters' );

      if ( piezasCaja !== undefined ) {

         if(piezasCaja < 1) {

            piezasCaja = '1';

            piezasCaja = parseFloat( piezasCaja.replace( ',', '.' ) );

         }

      }

      if ( linealMeters !== undefined ) {

         if(linealMeters < 0) {

            linealMeters = parseFloat( linealMeters );

         }

      }

      let price = $eurosInput.attr( 'data-price' );

      if ( price !== undefined ) {

         price = parseFloat( price.replace( ',', '.' ) );

      }


      let lastSurface = '';

      let lastPieces = '';

      $surfaceInput.on( 'blur', function () {

         let surfaceValue = $( this ).val();

         if ( surfaceValue !== '' && surfaceValue !== lastSurface ) {

            surfaceValue = parseFloat( surfaceValue.replace( ',', '.' ) );

            lastSurface = surfaceValue;



            let quantityValue = Math.ceil( surfaceValue / m2Caja );

            $quantityInput.val( quantityValue );

            let piecesValue = Math.ceil( quantityValue * piezasCaja );

            let surfaceValueReal = ( quantityValue * m2Caja ).toFixed( 2 );

            $eurosInput.val( ( quantityValue * price ).toFixed( 2 ) );

            $piecesInput.val( '' );

            $surfaceInputReal.val( surfaceValueReal );

            $piecesInputReal.val( piecesValue );

         }

      } );


      $piecesInput.on( 'blur', function () {

         let piecesValue = $( this ).val();

         if ( piecesValue !== '' && piecesValue !== lastPieces ) {

            piecesValue = parseFloat( piecesValue.replace( ',', '.' ) );

            lastPieces = piecesValue;



            let quantityValue = Math.ceil( piecesValue / piezasCaja );

            let surfaceValue = ( quantityValue * m2Caja ).toFixed( 2 );

            let piecesValueReal = Math.ceil( quantityValue * piezasCaja );



            $quantityInput.val( quantityValue );

            $eurosInput.val( ( quantityValue * price ).toFixed( 2 ) );

            $surfaceInput.val( '' );

            $surfaceInputReal.val( surfaceValue );

            $piecesInputReal.val( piecesValueReal );

         }

      } );





      // Disable Mouse scrolling

      $( 'input[type=number]' ).on( 'mousewheel', function ( e ) {

         $( this ).blur();

      } );

      // Disable keyboard scrolling

      $( 'input[type=number]' ).on( 'keydown', function ( e ) {

         var key = e.charCode || e.keyCode;

         // Disable Up and Down Arrows on Keyboard

         if ( key == 38 || key == 40 ) {

            e.preventDefault();

         } else {

            return;

         }

      } );

      $( '.carousel .carousel-item' ).each( function () {

         var minPerSlide = 2;

         var next = $( this ).next();

         if ( !next.length ) {

            next = $( this ).siblings( ':first' );

         }

         next.children( ':first-child' ).clone().appendTo( $( this ) );



         for ( var i = 0; i < minPerSlide; i++ ) {

            next = next.next();

            if ( !next.length ) {

               next = $( this ).siblings( ':first' );

            }



            next.children( ':first-child' ).clone().appendTo( $( this ) );

         }

      } );

      $( ".owl-carousel:not(.owl-carousel-image-products-mobile)" ).owlCarousel( {

         stagePadding: 40,

         loop: true,

         nav: true,

         margin: 0,

         responsiveClass: true,

         responsive: {

            0: {

               items: 1

            },

            550: {

               items: 2

            },

            768: {

               items: 3

            },

            1000: {

               items: 4

            },

            1500: {

               items: 5

            }

         }

      } );

      $( ".owl-carousel-image-products-mobile" ).owlCarousel( {

         items: 1,

         loop: true,

         nav: true,

         margin: 0,

         responsiveClass: true,

         center: true

      } );

      let sliderHeight = $( ".owl-carousel-image-products-mobile" ).find( '.owl-item' ).outerHeight();

      $( ".owl-carousel-image-products-mobile" ).find( '.video-item' ).css( 'height', sliderHeight );

      let sliderHeightImg = $( ".owl-carousel-image-products-mobile" ).find( '.owl-item' ).first().find( 'img' ).outerHeight();

      $( ".owl-carousel-image-products-mobile" ).find( '.video-item' ).find( '.product-video' ).css( 'height', sliderHeightImg + ' !important' );

      function setHeightVideo() {

         let $productContainer = $( '#product' ).find( '.product-container' );

         if ( $( window ).outerWidth() <= 1400 ) {

            let heightFirstImage = $productContainer.find( '.images-container' ).find( '.product-cover' ).first().height();

            //console.log( heightFirstImage );

            if ( heightFirstImage < 0 ) {

               heightFirstImage = $productContainer.find( '.owl-carousel-image-products-mobile' ).find( '.product-cover' ).first().height();

               //console.log( heightFirstImage );

               $productContainer.find( '.owl-carousel-image-products-mobile' ).find( '.product-video' ).height( heightFirstImage );

            } else {

               $productContainer.find( '.images-container' ).find( '.product-video' ).height( heightFirstImage );

            }

         } else {

            $productContainer.find( '.images-container' ).find( '.product-video' ).height( '100%' );

            $productContainer.find( '.owl-carousel-image-products-mobile' ).find( '.product-video' ).height( '100%' );

         }

      }

      setHeightVideo();

      $( window ).on( 'resize', function () {

         setHeightVideo()

      } );

      $( '.ets_mm_url' ).each( function () {

         $( this ).on( 'click', function () {

            pushBold( $( this ) );

         } );

      } );

      $( '.ets_mm_megamenu_content' ).find( '.arrow' ).each( function () {

         $( this ).on( 'click', function () {

            pushBold( $( this ).parent() );

         } );

      } );

      $( '.ets_mm_megamenu' ).find( '.custom-menu-ceramica' ).on( 'click', function () {

         let url = $( this ).find( '> a:first-child' ).attr( 'href' );



         if ( url !== undefined ) {

            window.location.href = url;

         }

      } );

      function pushBold( menuItem ) {

         let $menuTitle = menuItem.find( '.mm_menu_content_title' );



         if ( $menuTitle.hasClass( 'open' ) ) {

            $menuTitle.removeClass( 'open' );

         } else {

            $( '.mm_menu_content_title' ).each( function () {

               $( this ).removeClass( 'open' );

            } );



            $menuTitle.addClass( 'open' );

         }

      }

      $( '.read-more-button' ).each( function () {

         $( this ).on( 'click', function () {

            let $parent = $( this ).parent();



            $parent.find( '.profesional-description' ).toggleClass( 'hide' );

            $parent.find( 'img' ).toggleClass( 'op' );

         } );

      } );

      let checkboxes = $( 'input.activity_type' );

      checkboxes.change( function () {

         if ( $( '.activity_type:checked' ).length > 0 ) {

            checkboxes.removeAttr( 'required' );

         } else {

            checkboxes.attr( 'required', 'required' );

         }

      } );

      let $customFilterWrapper = $( '#custom-filter-wrapper' );

      $customFilterWrapper.find( 'button' ).on( 'click', function () {

         $( '.custom-filter-mobile' ).slideToggle( 'hidden-xs-down' );

      } );

      //Hacer que la opción salga desplegada
      
      var otherMaterialsLi = document.querySelectorAll('li.other-materials');

      otherMaterialsLi.forEach(function(li) {

         var blockContents = li.querySelectorAll('div.ets_mm_block_content');
         var spans = li.querySelectorAll('span.h4');

         blockContents.forEach(function(div) {
            div.classList.add('d-block');
         });

         spans.forEach(function(span) {
            span.classList.remove('h4');
            span.classList.add('h2', 'om-title');
         });

      });

      $( '.interesting-links' ).closest( '.ets_mm_block_content' ).css( 'display', 'block' );
    
      //Hacer que la opción de socialmedia salga desplegada y sin el título
      var liElement = $('.ets-mm-sm-links');
      liElement.find('.ets_mm_url').addClass('d-none-forced');
      liElement.find('.arrow').addClass('d-none-forced');
      liElement.find('.mm_columns_ul').removeClass('mm_columns_ul');


      let $pushScrollResponsive = $( '#push-scroll-responsive' );
      let $pushScrollResponsiveHeader = $( '#push-scroll-responsive-header' );

      scrollOnTouch( $pushScrollResponsive );
      scrollOnTouch( $pushScrollResponsiveHeader );

      function scrollOnTouch( touchSelector ) {

         touchSelector.on( 'touchmove', function ( event ) {

            event.preventDefault();


            $pushScrollResponsive.parent().css( 'transition', 'none' );


            let screenPosition = event.originalEvent.touches[ 0 ].clientY;

            let screenTotalHeight = document.documentElement.clientHeight;

            let calcHeight = 0;

            if ( screenPosition < 99 ) {

               calcHeight = screenTotalHeight - 99;

            } else {

               calcHeight = screenTotalHeight - screenPosition;

               if ( calcHeight < 90 ) {

                  calcHeight = 90;

               }

            }

            $pushScrollResponsive.parent().css( 'height', calcHeight + 'px' );

         } );



         touchSelector.on( 'click', function ( event ) {

            event.preventDefault();

            $pushScrollResponsive.parent().css( 'transition', 'ease-in-out 0.5s' );

            if ( $pushScrollResponsive.parent().position().top < 100 ) {

               $pushScrollResponsive.parent().css( 'height', '90px' );

            } else {

               $pushScrollResponsive.parent().css( 'height', document.documentElement.clientHeight - 99 + 'px' );

            }

         } );

      }

      $( window ).on( 'scroll', function () {

         if ( $( window ).scrollTop() + $( window ).height() == $( document ).height() ) {

            $pushScrollResponsive.parent().css( 'transition', 'ease-in-out 0.5s' );

            $pushScrollResponsive.parent().css( 'height', document.documentElement.clientHeight - 99 + 'px' );

         } else {

            if ( $( window ).scrollTop() == 0 ) {

               $pushScrollResponsive.parent().css( 'transition', 'ease-in-out 0.5s' );

               $pushScrollResponsive.parent().css( 'height', '90px' );

            }

         }

      } );

      let $modalImages = $( '.js-product-images-modal' );

      $( '.product-container' ).find( '.images-container' ).find( '.product-cover' ).each( function () {

         $( this ).on( 'click', function () {

            let iteration = $( this ).find( '.layer' ).data( 'iteration' );

            $modalImages.find( '.js-thumb-container' ).each( function ( index ) {

               if ( ( index + 1 ) === iteration ) {

                  $( this ).find( 'img' ).click();

               }

            } );

         } );

      } );

      $( '#module-planatec_recomendaciones-display' ).find( 'iframe' ).each( function () {

         $( this ).parent().css( 'position', 'relative' );

         $( this ).parent().css( 'padding-bottom', '56.25%' );

         $( this ).parent().css( 'height', '0' );

         $( this ).css( 'position', 'absolute' );

         $( this ).css( 'top', '0' );

         $( this ).css( 'left', '0' );

         $( this ).css( 'width', '100%' );

         $( this ).css( 'height', '100%' );

         $( this ).css( 'max-width', '800px' );

      } );

      $( 'footer' ).find( '.ps-social-follow' ).next().find( '.title.hidden-md-up' ).css( 'display', 'none' );

      $( document ).mouseup( function ( e ) {

         let containerMenusUl = $( '.mm_menus_ul' );

         let containerMenuToggle = $( '.ybc-menu-toggle' );

         if ( !containerMenusUl.is( e.target ) && containerMenusUl.has( e.target ).length === 0 
         && !containerMenuToggle.is( e.target ) 
         && containerMenuToggle.has( e.target ).length === 0 ) {

            $( '.ybc-menu-toggle.ybc-menu-btn.opened' ).click();

         }

      } );

      $( '.ybc-menu-toggle, .ybc-menu-vertical-button' ).on( 'click', function () {

         $( '.custom-menu-ult' ).toggleClass( 'custom-active' );
         $('#menu-ceramic').toggleClass('up-menu');
         $('#trustbadge-container-98e3dadd90eb493088abdc5597a70810').toggleClass('d-none-forced');
         $( '.whatsapp' ).toggleClass( 'd-none-forced' );

      } );

      //FUNCIONALIDAD MANTIENE ABIERTO
      $( '.mm_has_sub:not(.custom-menu-ceramica)' ).on( 'click', function () {

         $( this ).toggleClass( 'opened' );

      } );
      

      //muestra o esconde el contenido de la opción del menú
      $( '.ets_mm_block' ).find( 'span' ).on( 'click', function () {

         let $title = $( this ).find( '.h4' );

         let $blockContent = $( this ).closest( '.mm_blocks_ul' ).find( '.ets_mm_block_content' );



         if ( $blockContent.css( 'display' ) == 'none' ) {

            $title.addClass( 'opened' ); 
            $blockContent.css( 'display', 'block' );

         } else {

            $title.removeClass( 'opened' );
            $blockContent.css( 'display', 'none' );

         }

      } );
      

      // PRODUCTO POR SUPERFICIE
      const numberInput = document.getElementById('numberInput');
      const m2SubtotalBoxes = document.getElementById('m2SubtotalBoxes');

      if (document.getElementById('numberInput')) {

         document.getElementById('incrementButton').addEventListener('click', function() {

            numberInput.value = parseInt(numberInput.value) + 1;
            m2SubtotalBoxes.textContent = parseInt(numberInput.value);
  
            calculatem2bybox(m2Caja, numberInput, document.getElementById('surface-input'));

        });
    
        document.getElementById('decrementButton').addEventListener('click', function() {
  
           if(document.getElementById('numberInput').value > 0) {
  
              numberInput.value = parseInt(numberInput.value) - 1;
              m2SubtotalBoxes.textContent = parseInt(numberInput.value);
  
              calculatem2bybox(m2Caja, numberInput, document.getElementById('surface-input'));

           }

        });
  
        $('#surface-input').keyup( function() {
  
           let m2required = document.getElementById('surface-input').value;
  
           calculatem2OnChangeEvent(m2required, numberInput);
           
        })
  
        $('#numberInput').keyup ( function() {
        
           calculatem2bybox(m2Caja, document.getElementById('numberInput'), document.getElementById('surface-input'));

        })
  
  
         let calculatem2bybox = function(m2Caja, numberInput, numberm2needed) {

         let boxes = Number(numberInput.value);
         let totalSurface = (boxes * m2Caja).toFixed(2);

         numberm2needed.value = totalSurface;

         m2SubtotalBoxes.textContent = boxes;
         $surfaceInputReal.val(totalSurface);
         $m2TotalMeters.text(totalSurface);

         setQuantitiesValue(boxes);

         let discount = 1;

         const discountRows = document.querySelectorAll('.quantity-discount-row');

         discountRows.forEach(function(row) {
            const amount = Number(row.dataset.discountQuantity);
            const discountPercentage = Number(row.dataset.discount);

            if (boxes >= amount) {
               discount = 1 - (discountPercentage / 100);
            }
         });

         let subtotal = boxes * price;
         let total = subtotal * discount;

         if (total - subtotal < 0) {
            document.getElementById("subotals-product-dicount").style.display = "";
         } else {
            document.getElementById("subotals-product-dicount").style.display = "none";
         }

         $eurosInput.val(total.toFixed(2));
         $m2subtotal.text(subtotal.toFixed(2));
         $m2TotalDiscount.text((total - subtotal).toFixed(2));
         $m2TotalPrice.text(total.toFixed(2));
         };
  
         let calculatem2OnChangeEvent = function(m2required, numberInput) {

         let quantity = Math.ceil(m2required / m2Caja);

         numberInput.value = Number(quantity);
         m2SubtotalBoxes.textContent = Number(quantity);

         let totalSurface = (numberInput.value * m2Caja).toFixed(2);

         $surfaceInputReal.val(totalSurface);
         $m2TotalMeters.text(totalSurface);

         setQuantitiesValue(quantity);

         let discount = 1;

         const discountRows = document.querySelectorAll('.quantity-discount-row');

         discountRows.forEach(function(row) {
            const amount = Number(row.dataset.discountQuantity);
            const discountPercentage = Number(row.dataset.discount);

            if (quantity >= amount) {
               discount = 1 - (discountPercentage / 100);
            }
         });

         let subtotal = quantity * price;
         let total = subtotal * discount;

         if (total - subtotal < 0) {
            document.getElementById("subotals-product-dicount").style.display = "";
         } else {
            document.getElementById("subotals-product-dicount").style.display = "none";
         }

         $eurosInput.val(total.toFixed(2));
         $m2subtotal.text(subtotal.toFixed(2));
         $m2TotalDiscount.text((total - subtotal).toFixed(2));
         $m2TotalPrice.text(total.toFixed(2));

         };
  
         let setQuantitiesValue = function(quantity) {
   
            let piecesValue = Math.ceil( quantity * piezasCaja );
   
            $piecesInput.val( '' );
   
            $piecesInputReal.val( piecesValue );
   
            $quantityInput.val( quantity );
   
         }

       //botón +15%
      document.getElementById('recomendation-check').addEventListener('change', function() {

         let m2required = document.getElementById('surface-input');

         let currentValue = parseFloat(m2required.value);

         if (this.checked) {
            m2required.value = (currentValue * 1.15).toFixed(2);
         } else {
            m2required.value = (currentValue / 1.15).toFixed(2);
         }

         calculatem2OnChangeEvent(m2required.value, numberInput);

     });


      }

      // PRODUCTO POR UNIDAD
      
      if (document.getElementById('incrementQuantity')) {

      const inputQuantityBox = document.getElementById('quantity-input');

      document.getElementById('incrementQuantity').addEventListener('click', function() {

         inputQuantityBox.value = parseInt(inputQuantityBox.value || 0) + 1;

         let quantity = Number(inputQuantityBox.value);
         let discount = getQuantityDiscount(quantity);

         $eurosInput.val((quantity * price * discount).toFixed(2));

      });

      document.getElementById('decrementQuantity').addEventListener('click', function() {

         if (Number(inputQuantityBox.value) > 0) {

            inputQuantityBox.value = parseInt(inputQuantityBox.value || 0) - 1;

            let quantity = Number(inputQuantityBox.value);
            let discount = getQuantityDiscount(quantity);

            $eurosInput.val((quantity * price * discount).toFixed(2));

         }

      });

      $('#quantity-input').keyup(function() {

         let quantity = Number(inputQuantityBox.value || 0);
         let discount = getQuantityDiscount(quantity);

         $eurosInput.val((quantity * price * discount).toFixed(2));

      });

      }

      //JOINT CALCULATOR
   
      if (document.getElementById('jointCalculatorProcess')){
         
         const largeTile = document.getElementById('large_tile');
         const heightTile = document.getElementById('height_tile');
         const espessorTile = document.getElementById('espessor_tile');
         const largeJoint = document.getElementById('large_joint');
         const m2Area = document.getElementById('m2_area');
         const calculateButton = document.getElementById('jointCalculatorProcess');

         function validateInputs() {

            if (
            largeTile.value !== '' &&
            heightTile.value !== '' &&
            espessorTile.value !== '' &&
            largeJoint.value !== '' &&
            m2Area.value !== ''
            ) {
            calculateButton.disabled = false; // Habilitar el botón si todos tienen valores
            } else {
            calculateButton.disabled = true; // Deshabilitar el botón si alguno está vacío
            }
         }
      
         // Deshabilitar el botón al cargar la página
         calculateButton.disabled = true;
      
         // Añadimos el evento 'input' para cada campo para validar en tiempo real
         [largeTile, heightTile, espessorTile, largeJoint, m2Area].forEach(input => {
            input.addEventListener('input', validateInputs);
         });


         document.getElementById('jointCalculatorProcess').addEventListener('click', function() {
            let density = 850; 
            if (parseInt(document.getElementById('manufacturer').value) == 2) { //Si el producto es MAPEI
               density = 750;
            }
            let kgs_sack = parseInt(document.getElementById('kgs_sack').value);
            let large = (document.getElementById('large_tile').value)/1000;
            let height = (document.getElementById('height_tile').value)/1000;
            let espessor = (document.getElementById('espessor_tile').value)/1000;
            let largeJoint = (document.getElementById('large_joint').value)/1000;
            let area = parseInt(document.getElementById('m2_area').value);
            let perimeter = 2 * (large + height);
            let volume = perimeter * largeJoint * espessor;
            let tileArea = 1/(large * height);
            let total_volume = volume * tileArea * area;
            let quantity = (total_volume * density) * 1.10;
            let total_sacks = Math.ceil(quantity / kgs_sack);

            document.getElementById('quantity-input').value = parseInt(total_sacks);
            document.getElementById('total_kgs').value = Math.ceil(quantity);
            $eurosInput.val( ( parseInt(total_sacks) * price ).toFixed( 2 ) );

         });
      }

      // PRODUCTO POR PIEZA

      function getQuantityDiscount(quantity) {
         let discount = 1;

         const discountRows = document.querySelectorAll('.quantity-discount-row');

         discountRows.forEach(function(row) {
            const amount = Number(row.dataset.discountQuantity);
            const discountPercentage = Number(row.dataset.discount);

            if (Number(quantity) >= amount) {
               discount = 1 - (discountPercentage / 100);
            }
         });

         return discount;
      }

      const inputPiecesBox = document.getElementById('inputPiecesBox');
      const pieceSubtotalBoxes = document.getElementById('pieceSubtotalBoxes');

      if (document.getElementById('inputPiecesBox')) {

         document.getElementById('incrementPieces').addEventListener('click', function() {

            inputPiecesBox.value = parseInt(inputPiecesBox.value) + 1;
            pieceSubtotalBoxes.textContent = parseInt(inputPiecesBox.value);
   
            calculatepiecesbybox(piezasCaja, inputPiecesBox, document.getElementById('pieces-input'));

         });
     
         document.getElementById('decrementPieces').addEventListener('click', function() {
   
            if(document.getElementById('inputPiecesBox').value > 0) {
   
               inputPiecesBox.value = parseInt(inputPiecesBox.value) - 1;
               pieceSubtotalBoxes.textContent = parseInt(inputPiecesBox.value);
   
               calculatepiecesbybox(piezasCaja, inputPiecesBox, document.getElementById('pieces-input'));

            }
         });
   
         $('#pieces-input').keyup( function() {

            calculatePiecesOnChangeEvent(document.getElementById('pieces-input').value, inputPiecesBox);
   
         })
   
         $('#inputPiecesBox').keyup ( function() {
            calculatepiecesbybox(piezasCaja, document.getElementById('inputPiecesBox'), document.getElementById('pieces-input'));

         })
   
         //FUNCIONES
   
         let calculatepiecesbybox = function(piezasCaja, inputPieces, piecesNeeded) {
         let pieces = Number(inputPieces.value);

         let totalPieces = (pieces * piezasCaja).toFixed(2);

         if (linealMeters > 0) {
            piecesNeeded.value = Math.floor(pieces * linealMeters);
         } else {
            piecesNeeded.value = totalPieces;
         }

         pieceSubtotalBoxes.textContent = inputPiecesBox.value;
         $piecesInputReal.val(totalPieces);
         $pieceTotalMeters.text(totalPieces);

         setPiecesQuantitiesValue(pieces);

         let discount = getQuantityDiscount(pieces);

         $eurosInput.val((pieces * price * discount).toFixed(2));
         $m2TotalPrice.text((inputPiecesBox.value * price * discount).toFixed(2));
         };

         let calculatePiecesOnChangeEvent = function(piecesRequired, inputPieces) {
            let pieces = Number(inputPieces.value);

            let necessaryBox = piezasCaja;

            if (linealMeters > 0) {
               necessaryBox = linealMeters;
            }

            let quantity = Math.ceil(piecesRequired / necessaryBox);

            pieces = quantity;
            pieceSubtotalBoxes.textContent = quantity;

            let totalPieces = (pieces * piezasCaja).toFixed(2);

            if (linealMeters > 0) {
               totalPieces = quantity.toFixed(2);
            }
            
            inputPieces.value = quantity;

            $piecesInputReal.val(totalPieces);
            $pieceTotalMeters.text(totalPieces);

            setPiecesQuantitiesValue(quantity);

            let discount = getQuantityDiscount(quantity);

            $eurosInput.val((quantity * price * discount).toFixed(2));
            $m2TotalPrice.text((quantity * price * discount).toFixed(2));
         };

         let setPiecesQuantitiesValue = function(quantity) {
   
            let piecesValue = Math.ceil( quantity * piezasCaja );
   
            $piecesInputReal.val( piecesValue );
            $pieceTotalMeters.text(piecesValue);
   
            $quantityInput.val( quantity );
   
         }

         document.getElementById('recomendation-check-pieces').addEventListener('change', function() {
   
            let piecesRequired = document.getElementById('pieces-input');
      
            let currentValue = parseFloat(piecesRequired.value);
      
            if (this.checked) {
               piecesRequired.value = (currentValue * 1.15).toFixed(2);
            } else {
               piecesRequired.value = (currentValue / 1.15).toFixed(2);
            }
      
            calculatePiecesOnChangeEvent(piecesRequired.value, inputPiecesBox);
      
         });
   

      }

      $( '#add-wrapper' ).find( '.add-sample' ).find( '.add-to-cart-sample' ).on( 'click', function () {

         let cartId = document.getElementById('cartId').value;
         let productId = $('#product_page_product_id').val();
         let btn = $(this);

         $.ajax({
            url: '/ajax/checkSamplesInCart.php', 
            method: 'POST', 
            data: {
               id_cart: cartId,
               id_product: productId
            },
            success: function(response) {
               
               // Si la respuesta es válida, puedes usar el resultado (por ejemplo, el costo de envío)
               if (response.can_add_sample && response.id_sample !== false) {


                  var quantityOriginalValue = $( '#quantity-input' ).val();

                  $('#product_page_product_id').val(response.id_sample);
                  
                  $( '#quantity-input' ).val( 1 );

                  let addButton = $('#add-to-cart-submit');
                  addButton.prop('disabled', false);
                  addButton.trigger('click');
                  addButton.prop('disabled', true);
      
                  $( '#quantity-input' ).val( quantityOriginalValue );

                  $('#product_page_product_id').val(productId);

                  btn.attr( 'disabled', 'disabled' );

                  $('#sample-in-cart').show();
      
               }else{
      
                  btn.attr( 'disabled', 'disabled' );

                  if(response.elements > 7 ) {
                     $('#max-samples-reached').show();
                  }
                  
                  if(response.status) {
                     $('#sample-in-cart').show();
                  }
        
               }
            },
            error: function(err) {
               // Manejo de errores en caso de que algo falle en la solicitud
               console.error('Error en la solicitud AJAX:', err);
            }
         });


      } );

      $( '#add-wrapper' ).find( '.add' ).find( '.add-to-cart' ).on('click', function() {
         
         $( '#add-sample-to-cart-button' ).attr( 'disabled', 'disabled' );

         $('#sample-in-cart').show();

      });

      const recipeCarouselSwiper = new Swiper( '.recipeCarousel-swiper', {

         // Optional parameters
         loop: true,
         slidesPerView: 1,
         spaceBetween: 0,

         // Navigation arrows

         navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
         },

         // Breakpoint
         breakpoints: {

            550: {
               slidesPerView: 2
            },

            768: {
               slidesPerView: 3
            },

            1000: {
               slidesPerView: 4
            },

            1500: {
               slidesPerView: 5
            }

         }

      } );

      const customFeaturedSwiper = new Swiper('.custom-featured-swiper', {
         loop: true,  // Activar el loop
         slidesPerView: 1,
         spaceBetween: 0,
     
         navigation: {
             nextEl: '.swiper-button-next',
             prevEl: '.swiper-button-prev',
         },
     
         breakpoints: {
             550: {
                 slidesPerView: 2,
             },
             768: {
                 slidesPerView: 3,
             },
             1000: {
                 slidesPerView: 4,
             },
             1500: {
                 slidesPerView: 5,
             }
         },
     
     });
     
      let $customLightbox = $( '#custom-lightbox' );

      $( '.img-custom-lightbox' ).each( function () {

         let imgSrc = $( this ).data( 'lightbox' );



         $( this ).on( 'click', function () {

            $customLightbox.find( 'img' ).attr( 'src', imgSrc );

            $customLightbox.toggleClass( 'lightbox-visible' );

         } );

      } );

      $customLightbox.on( 'mouseup', function ( e ) {

         if ( !$( e.target ).is( 'img' ) ) {

            $customLightbox.toggleClass( 'lightbox-visible' );

         }

      } );

      // PROCESO DE CHECKOUT

      let $checkoutBody = $( '#checkout' );



      let $checkoutStepsWrapper = $checkoutBody.find( '.checkout-step' );

      let $checkoutTitleTab = $checkoutBody.find( '#planatec-tabs' );



      $checkoutTitleTab.find( '.planatec-step-title' ).each( function () {

         let indexStep = $( this ).data( 'index' );



         $( this ).on( 'click', function ( event ) {

            let permitido = false;

            for ( let i = 1; i <= 4; i++ ) {

               let $forTab = $( '#planatec-step-title-' + i );

               if ( $forTab.hasClass( 'tab-actual' ) ) {

                  if ( i > indexStep ) {

                     permitido = true;

                  }

               }

               if ( permitido ) {

                  $forTab.css( 'cursor', 'not-allowed' );

               }

            }



            if ( permitido ) {

               let tabname = $( this ).data( 'tabname' );

               let $checkoutStep = $( '#' + tabname );

               $checkoutStepsWrapper[ indexStep - 1 ].click();

               $checkoutStep.addClass( "actual" );


               for ( let i = 1; i <= 4; i++ ) {

                  let $forTab = $( '#planatec-step-title-' + i );

                  $forTab.removeClass( 'tab-actual' );

               }



               let $currentTab = $( '#planatec-step-title-' + indexStep );

               $currentTab.addClass( 'tab-actual' );

            }

         } );



         if ( $checkoutStepsWrapper[ indexStep - 1 ].classList.contains( '-current' ) ) {

            $( this ).addClass( 'tab-actual' );

         }



         let notAllowed = false;

         for ( let i = 1; i <= 4; i++ ) {

            let $forTab = $( '#planatec-step-title-' + i );

            if ( $forTab.hasClass( 'tab-actual' ) ) {

               if ( indexStep > i ) {

                  notAllowed = true;

               }

            }



            if ( notAllowed ) {

               $forTab.css( 'cursor', 'not-allowed' );

            } else {

               $forTab.css( 'cursor', 'pointer' );

            }

         }

      } );



      let $buttonNewAccount = $checkoutBody.find( '#planatec-new-account' );


      $buttonNewAccount.on( 'click', function () {
         let $fieldCompany = $( '.companyClass' ).closest( '.form-group' );
         $fieldCompany.find('input').prop('required', false);

         if ( $buttonBuyGuest.hasClass( 'open' ) ) {

            $buttonBuyGuest.parent().find( '.customer-fields' ).children().each( function () {

               $( this ).css( 'display', 'none' );

            } );

            $buttonBuyGuest.removeClass( 'open' );
         }

         $buttonNewAccount.parent().find( '.customer-fields' ).children().each( function (index) {

            if ( $( this ).css( 'display' ) === 'none' ) {

               $( this ).css( 'display', 'inherit' );

               $( this ).addClass('bac-position-'+index);

               $buttonNewAccount.addClass( 'open' );

               $buttonBuyGuest.removeClass( 'open' );

               $buttonNewAccount.parent().find( '.customer-fields' ).after( $buttonBuyGuest );

            } else {

               $( this ).css( 'display', 'none' );

               $buttonNewAccount.removeClass( 'open' );

               $buttonBuyGuest.removeClass( 'open' );

            }

         } );
         $('#field-siret').closest('.form-group').css('display', 'none');
         $('#field-birthday').closest('.form-group').css('display', 'none');
         

         document.querySelector('input[name="customer_privacy"]').checked = true;
         $('.bac-position-8').css('display', 'none');

         if ($buttonNewAccount.hasClass( 'open' )) {
            $('#nc-continue-button').css('display', 'inherit');
         }else{
            $('#nc-continue-button').css('display', 'none');
         }

      } );

      let $buttonBuyGuest = $checkoutBody.find( '#planatec-buy-guest' );

      $buttonBuyGuest.on( 'click', function () {
         let $fieldCompany = $( '.companyClass' ).closest( '.form-group' );
         $fieldCompany.find('input').prop('required', false);

         if ( $buttonNewAccount.hasClass( 'open' ) ) {

            $buttonBuyGuest.parent().find( '.customer-fields' ).children().each( function () {

               $( this ).css( 'display', 'none' );

            } );

            $buttonNewAccount.removeClass( 'open' );

         }

         $buttonBuyGuest.parent().find( '.customer-fields' ).children().each( function ( index ) {

            if ( !$( this ).hasClass( 'form-informations' )) {

               if ( $( this ).css( 'display' ) === 'none' ) {

                  $( this ).css( 'display', 'inherit' );

                  $( this ).addClass('bag-position-'+index);

                  $buttonBuyGuest.addClass( 'open' );

                  $buttonNewAccount.removeClass( 'open' );

                  $buttonNewAccount.after( $buttonBuyGuest );
                  

               } else {

                  $( this ).css( 'display', 'none' );

                  $buttonBuyGuest.removeClass( 'open' );

                  $buttonNewAccount.removeClass( 'open' );

               }

            }

         } );

         
         $('#field-company').closest('.form-group').css('display', 'none');
         
         $('#field-birthday').closest('.form-group').css('display', 'none');
         $('.bag-position-6').css('display', 'none');
         document.querySelector('input[name="customer_privacy"]').checked = true;
         $('.bag-position-8').css('display', 'none');

         if ($buttonBuyGuest.hasClass( 'open' )) {
            $('#nc-continue-button').css('display', 'inherit');
         }else{
            $('#nc-continue-button').css('display', 'none');
         }

      } );

      
      $( '.planatec-show-action' ).on( 'click', function () {

         let $fieldPassword = $( this ).closest( '.input-group' ).find( 'input' );

         let imgUrl = $( this ).find( 'img' ).data( 'url' );



         if ( $fieldPassword.attr( 'type' ) === 'password' ) {

            $fieldPassword.attr( 'type', 'text' );

            $( this ).find( 'img' ).attr( 'src', imgUrl + 'ojo.png' );

         } else {

            $fieldPassword.attr( 'type', 'password' );

            $( this ).find( 'img' ).attr( 'src', imgUrl + 'ojo-contrasena.png' );

         }

      } );



      $( '.planatec-bottom-checkout' ).find( '.planatec-bottom-right' ).find( 'button' ).on( 'click', function () {

         $( '#register-new-customer' ).click();

      } );

      //DELIVERY PRICE CALCULATOR

      if (document.getElementById('deliveryPriceCalculator')) {

         if (!document.getElementById('cart')) {

            $('#deliveryPriceCalculator').css('display', 'none');

         }else{

            $('#deliveryPriceCalculator').css('display', 'block');

            const countrySelector = document.getElementById("field-id_country");
            const deliverySearchButton = document.getElementById("calculateMyDeliveryButton");
            const provinceSelector = document.getElementById("field-id_state");
            const language = document.getElementById('language').value;
            let provinceMessage = 'Select a state';

            if(language == 1) {
               provinceMessage = 'Selecciona una provincia';
            }
            if(language == 2) {
               provinceMessage = 'Sélectionnez une province';
            }
            if(language == 4) {
               provinceMessage = 'Wählen Sie eine Provinz';
            }
            if(language == 5) {
               provinceMessage = 'Selecione uma província';
            }
            if(language == 6) {
               provinceMessage = 'Selecteer een provincie';
            }
        
            countrySelector.addEventListener("change", function () {
                let countryId = this.value;

        
                // Limpia las opciones anteriores
                provinceSelector.innerHTML = '<option value=""> ... </option>';
        
                // Realiza la llamada AJAX al nuevo endpoint
                fetch(`/ajax/getProvinces.php?id_country=${countryId}`)
                  .then((response) => response.json())
                  .then((data) => {
                     // Limpia el selector y agrega las nuevas provincias
                     provinceSelector.innerHTML = "<option value=''>" + provinceMessage + "</option>";
                     data.forEach((province) => {
                        const option = document.createElement("option");
                        option.value = province.id_state;
                        option.textContent = province.name;
                        provinceSelector.appendChild(option);
                     });
                  })
                  .catch((error) => {
                     console.error("Error cargando provincias:", error);
                     provinceSelector.innerHTML = '<option value=""> - . Error . - </option>';
                  });
            });
   
            deliverySearchButton.addEventListener("click", function () {
               // Obtener los valores de los campos de formulario
               let countryId = document.getElementById('field-id_country').value;
               let stateId = document.getElementById('field-id_state').value;
               let postal = document.getElementById('postalzip').value;
               let cartId = document.getElementById('cartId').value;
               let packageWeight = document.getElementById('packageWeight').value;
               let showTaxes = document.getElementById('showTaxes').value;
               
               if (!countryId.trim() || !stateId.trim() || !postal.trim()){
                  document.getElementById('messageContainer').style.display = 'block'
               }else{
                  //si hay error limpiarlo
                   document.getElementById('messageContainer').style.display = 'none'
                  // Realizar la solicitud AJAX utilizando jQuery
                  console.log('enviando ajax, calculo precios...');
                  $.ajax({
                     url: '/ajax/getDeliveryPrice.php', // Ruta al archivo PHP
                     method: 'POST', // Usamos POST para enviar los datos
                     data: {
                        id_country: countryId,
                        id_state: stateId,
                        postal: postal,
                        id_cart: cartId,
                        weight: packageWeight,
                        taxes: showTaxes
                     },
                     success: function(response) {
                        // Si la respuesta es válida, puedes usar el resultado (por ejemplo, el costo de envío)
                        console.log('respuesta obtenida');
                        if (response.shipping_cost) {
                           document.getElementById('euros-input').value = response.shipping_cost;
                        } else if (response.error) {
                           console.error('Error:', response.error);
                        }
                     },
                     error: function(err) {
                        // Manejo de errores en caso de que algo falle en la solicitud
                        console.error('Error en la solicitud AJAX:', err);
                     }
                  });
               }

           });

         }

      }

      //CUSTOM LOAD COUNTRIES
      if (document.getElementById("field-id_country")) {

            const countrySelector = document.getElementById("field-id_country");
            const deliverySearchButton = document.getElementById("calculateMyDeliveryButton");
            const provinceSelector = document.getElementById("field-id_state");
            const language = document.getElementById('language').value;
            let provinceMessage = 'Select a state';

            if(language == 1) {
               provinceMessage = 'Selecciona una provincia';
            }
            if(language == 2) {
               provinceMessage = 'Sélectionnez une province';
            }
            if(language == 4) {
               provinceMessage = 'Wählen Sie eine Provinz';
            }
            if(language == 5) {
               provinceMessage = 'Selecione uma província';
            }
            if(language == 6) {
               provinceMessage = 'Selecteer een provincie';
            }
        
            countrySelector.addEventListener("change", function () {
               let countryId = this.value;
               let useSameCheck = true;
                 
               if(document.getElementById('useDifferentAddress')) {
                  if (document.getElementById('useDifferentAddress'). checked){ //Diferente Dirección
                     useSameCheck = false;
                  }
               }
                              
               if ($('input[name="treatment"]:checked').val() === 'particular') {
                  applyFormSetup('PARTICULAR', useSameCheck);
               }else{
                  applyFormSetup('COMPANY', useSameCheck);
               }
               
                // Limpia las opciones anteriores
                provinceSelector.innerHTML = '<option value=""> ... </option>';
        
                // Realiza la llamada AJAX al nuevo endpoint
                fetch(`/ajax/getProvinces.php?id_country=${countryId}`)
                    .then((response) => response.json())
                    .then((data) => {
                        // Limpia el selector y agrega las nuevas provincias
                        provinceSelector.innerHTML = "<option value=''>" + provinceMessage + "</option>";
                        data.forEach((province) => {
                            const option = document.createElement("option");
                            option.value = province.id_state;
                            option.textContent = province.name;
                            provinceSelector.appendChild(option);
                        });
                    })
                    .catch((error) => {
                        console.error("Error cargando provincias:", error);
                        provinceSelector.innerHTML = '<option value=""> - . Error . - </option>';
                    });
            });
      }
          

      //CHECKOUT VAT
      function getisNewAddress() {
         // Obtener los parámetros de la URL actual
         let params = new URLSearchParams(window.location.search);

         // Comprobamos si existen los parámetros
         let hasNew = params.has("newAddress");

         if (hasNew) {
            return true;
         } 
         // Mostrar resultados
         return false;
      }

      function getisEditAddress() {
         // Obtener los parámetros de la URL actual
         let params = new URLSearchParams(window.location.search);

         // Comprobamos si existen los parámetros
         let hasNew = params.has("editAddress");

         if (hasNew) {
            return true;
         } 
         // Mostrar resultados
         return false;
      }


      /* logica formulario direcciones */

      function getAddressType(){
         // Obtener los parámetros de la URL actual
         let params = new URLSearchParams(window.location.search);

         // Comprobamos si existen los parámetros
         let hasNew = params.has("newAddress");
         let hasEdit = params.has("editAddress");

         let paramValue = null;

         if (hasNew) {
         paramValue = params.get("newAddress");
         } else if (hasEdit) {
         paramValue = params.get("editAddress");
         }

         return paramValue;
      }

      function addSameInput(){
         let addressForm = document.getElementById('address-form') ?? '';
      
         if (!addressForm) return;

         if (document.getElementById('useDifferentAddress')) {
            // Siempre que es "misma dirección" queremos tener use_same_address = 1
            if (!document.getElementById('use_same_address')) {
               let hidden = document.createElement('input');
               hidden.type  = 'hidden';
               hidden.name  = 'use_same_address';
               hidden.value = '1';
               hidden.id    = 'use_same_address';
               addressForm.appendChild(hidden);
            }

      }

      }

      function addConfirmAddress(){
         let addressForm = document.getElementById('address-form');
         if (!addressForm) return;

         // si ya existe, no creamos otro
         let confirmHidden = document.getElementById('confirm_addresses_hidden');
         if (!confirmHidden) {
            confirmHidden = document.createElement('input');
            confirmHidden.type  = 'hidden';
            confirmHidden.name  = 'confirm-addresses';
            confirmHidden.value = '1';
            confirmHidden.id    = 'confirm_addresses_hidden';
            addressForm.appendChild(confirmHidden);
         }
      }

      function removeConfirm() {
         if (document.getElementById('useDifferentAddress')) {
            let confirmHidden = document.getElementById('confirm_addresses_hidden');
            if (confirmHidden && confirmHidden.parentNode) confirmHidden.parentNode.removeChild(confirmHidden);
         }

      }
      function removeSame() {

         if (document.getElementById('useDifferentAddress')) {
            let hidden = document.getElementById('use_same_address');
            if (hidden && hidden.parentNode) hidden.parentNode.removeChild(hidden);
         }
      }


      function getSubtotalProductsPriceRoundUpInteger() {
         let text = document.querySelector('#cart-subtotal-products .value').textContent;
         // Limpiar formato europeo correctamente
         let number = parseFloat(
         text
            .replace(/\./g, '')     // quitar separador de miles
            .replace(',', '.')      // convertir decimal
            .replace(/[^\d.]/g, '') // quitar símbolo €
         );

         return  Math.ceil(number);
      }

      if (document.getElementById('delivery-address')) {
         const SubtotalProductsRoundUpInteger = getSubtotalProductsPriceRoundUpInteger();
         const $treatment = $( 'input[name="treatment"]:checked' );
         const $fieldAlias = $( '#field-alias' ).closest( '.form-group' );
         const customerTypeBox = $('#field-empresa').closest( '.form-group' );
         const $fieldFirstName = $( '#field-firstname' ).closest( '.form-group' );
         const $fieldLastName = $( '#field-lastname' ).closest( '.form-group' );
         const $fieldCompany = $( '.companyClass' ).closest( '.form-group' );
         const $fieldVatNumber = $( '#field-vat_number' ).closest( '.form-group' );
         const $fieldDniCif = $( '#field-dni' ).closest( '.form-group' );
         const $fieldAddress2 = $( '#field-address2' ).closest( '.form-group' );
         const $dniLabel = $('.dniShowClass');
         const $cifLabel = $('.cifShowClass');
         $fieldAlias.css( 'display', 'none' );
         const companyTranslation = $('#company-translation').data('translation');
         $fieldCompany.find('label').html(companyTranslation);
         const address2Translation = $('#address2-translation').data('translation');
         $fieldAddress2.find('label').html(address2Translation);
         const firstNameTranslationCompany = $('#firstname-translation-company').data('translation');
         $fieldVatNumber.find('input').prop('required', false);
         $fieldVatNumber.css( 'display', 'none' );
         $fieldVatNumber.find('input').val('');
         let getNewAddresParam = getisNewAddress();
         const newAddress = ((document.getElementById('newAddress') && document.getElementById('newAddress').dataset.new == '1') || getNewAddresParam) ? true : false;
         const addressType = getAddressType();
         var useSameCheck = false;
         const invoiceForm = document.getElementById('useDifferentAddress') ? false : true;
         let isInvoiceParam = getIsInvoiceParam();
         const originalIsInvoice = isInvoiceParam ? isInvoiceParam : $('#field-is_invoice').val();
         if ((document.getElementById('newAddress') && document.getElementById('newAddress').dataset.new == '1') && !getNewAddresParam){
            $('#cancel-address-form').css('display', 'none');
         }
         if (isInvoiceParam) {
           
            $('#field-is_invoice').val(isInvoiceParam);
         }else{
            setIsInvoiceDefault();
         }
 
         function getIsInvoiceParam() {         

            // Obtener los parámetros de la URL actual
            let params = new URLSearchParams(window.location.search);

            // Comprobamos si existen los parámetros
            let isInvoiceParam = params.has("address_toggle");

            if (!isInvoiceParam) {
               return false;
            }

            return params.get("address_toggle");
      
         }

         function setIsInvoiceDefault() {     

            if(!invoiceForm) {
               if (document.getElementById('useDifferentAddress').checked){ //Diferente Dirección
                  useSameCheck = false;
       
                  $('#field-is_invoice').val(originalIsInvoice);
               }else{ //misma dirección
                  useSameCheck = true;

                  $('#field-is_invoice').val('2');
               }
            }else{
               useSameCheck = false;
     
               $('#field-is_invoice').val('1');
            }

         }

         function setConfirmAndSameInputs() {
            let hasToggle   = !!document.getElementById('useDifferentAddress'); 
            let useSameCheck = true;
            let confirm = true;
            let useSameInput = true;
            let firstAddress = false;
            if ((document.getElementById('newAddress') && document.getElementById('newAddress').dataset.new == '1') && !getisNewAddress()){
               firstAddress = true;
            }
  
            removeSame();
            removeConfirm();
            console.log('removing inputs...');

            if (hasToggle && document.getElementById('useDifferentAddress').checked){ //Diferente Dirección
               useSameCheck = false;
            }
            if($('#field-empresa').is(':checked')) {
               useSameCheck = false;
            }
            
            //si es nueva o editar desde botón fuera input

            if (firstAddress){
               confirm = true;
               if(useSameCheck) {
                  useSameInput = true;
               }else{
                  useSameInput = false;
               }
            }

            if (getisNewAddress()) {
               useSameInput = false;
               confirm = false;
               if(useSameCheck && !invoiceForm){
                     confirm = true;
                     useSameInput = true;
               }
               
            }


            if(getisEditAddress()) {
               useSameInput = false;
               confirm = false;
            }
        
            if(useSameInput) {
               addSameInput();
               console.log('add usesame input...');
            }


            if (confirm) {
               addConfirmAddress();
               console.log('add confirm input...');
            }

            console.log('setup finalizada...');

         }

         function addressFormatOnlyName() {
                  $fieldCompany.css( 'display', 'none' );
                  $fieldCompany.find('input').prop('required', false);
                  $fieldFirstName.find('label').html(firstNameTranslationCompany);
                  $fieldFirstName.css('display', 'inherit');
                  $fieldFirstName.find('input').prop('required', true);
                  $fieldLastName.css('display', 'inherit');
                  $fieldLastName.find('input').prop('required', true);
                  $fieldDniCif.css( 'display', 'none' );        
                  $dniLabel.css( 'display', 'none' );
                  $cifLabel.css( 'display', 'none' );
         }

         function addressFormatCompanyCif() {
                  $fieldCompany.css( 'display', 'inherit' );
                  $fieldCompany.find('input').prop('required', true);
                  $fieldFirstName.find('label').html(firstNameTranslationCompany);
                  $fieldFirstName.css('display', 'none');
                  $fieldFirstName.find('input').prop('required', false);
                  $fieldLastName.css('display', 'none');
                  $fieldLastName.find('input').prop('required', false);
                  $fieldDniCif.css( 'display', 'inherit' );        
                  $dniLabel.css( 'display', 'none' );
                  $cifLabel.css( 'display', 'inherit' );
         }

         function addressFormatNameDni() {
                  $fieldCompany.css( 'display', 'none' );
                  $fieldCompany.find('input').prop('required', false);
                  $fieldFirstName.find('label').html(firstNameTranslationCompany);
                  $fieldFirstName.css('display', 'inherit');
                  $fieldFirstName.find('input').prop('required', true);
                  $fieldLastName.css('display', 'inherit');
                  $fieldLastName.find('input').prop('required', true);
                  $fieldDniCif.css( 'display', 'inherit' );        
                  $dniLabel.css( 'display', 'inherit' );
                  $cifLabel.css( 'display', 'none' );
         }

         function newAddresCompanysetup() {
             let switchUseSame = $('#switchUseSameFormDiv').closest( '.form-group' );
             switchUseSame.css('display', 'none');
            if($('#field-is_invoice').val() != '0'){
               addressFormatCompanyCif();
            }else{
               addressFormatOnlyName();
            }
             
            
         }

         function newAddresParticularsetup(useSameCheck) {
            
            let switchUseSame = $('#switchUseSameFormDiv').closest( '.form-group' );
            switchUseSame.css('display', 'flex');
            if (!useSameCheck && !invoiceForm) { //Diferente Dirección y no es facturacion
                  addressFormatOnlyName();
            }else{//misma dirección
               if ($('#field-id_country').val() != 6 && SubtotalProductsRoundUpInteger < 2200) {
                  addressFormatOnlyName();
               }else{
                  addressFormatNameDni();
               }
            }
            
         }

         function editAddressParticularDelivery(useSameCheck) {
            let switchUseSame = $('#switchUseSameFormDiv').closest( '.form-group' );
            switchUseSame.css('display', 'none');
            
            if (!useSameCheck) {
               addressFormatOnlyName(); 
            }else{
               if ($('#field-id_country').val() != 6 && SubtotalProductsRoundUpInteger < 2200) {
                  addressFormatOnlyName();
               }else{
                  addressFormatNameDni();
               }
            }
            

         }

         function editAddressCompanyDelivery() {
            let switchUseSame = $('#switchUseSameFormDiv').closest( '.form-group' );
            switchUseSame.css('display', 'none');
            
            addressFormatOnlyName();
            
         }

         function editAddressCompanyInvoice() {
            let switchUseSame = $('#switchUseSameFormDiv').closest( '.form-group' );
            switchUseSame.css('display', 'none');
            addressFormatCompanyCif();
         }

         function editAddressParticularInvoice() {
            let switchUseSame = $('#switchUseSameFormDiv').closest( '.form-group' );
            switchUseSame.css('display', 'none');
            if ($('#field-id_country').val() != 6 && SubtotalProductsRoundUpInteger < 2200) {
               addressFormatOnlyName();
            } else {
               addressFormatNameDni();
            }
         }

         function applyFormSetup(mode, useSameCheck) {
            // mode = 'COMPANY' o 'PARTICULAR'
            $('#field-alias').val(mode);

            const isCompany = (mode === 'COMPANY');

            if (newAddress) {
               if (isCompany) {
                  newAddresCompanysetup();
               } else {
                  newAddresParticularsetup(useSameCheck);
               }
            } else {
               if (addressType === 'delivery') {
                  if (isCompany) {
                  editAddressCompanyDelivery();
                  } else {
                  editAddressParticularDelivery(useSameCheck);
                  }
               } else {
                  if (isCompany) {
                  editAddressCompanyInvoice();
                  } else {
                  editAddressParticularInvoice();
                  }
               }
            }
         }

         function showGoToInvoiceButton() {
            $('#continue-label').css('display', 'none');
            $('#goto-invoice-label').css('display', 'inherit');
            document.getElementById("confirmAddressButton").classList.remove("continue");
            document.getElementById("confirmAddressButton").classList.replace("btn-primary", "btn-secondary");
            document.getElementById("confirmAddressButton").classList.replace("float-xs-right", "float-xs-left");
         }
      
         function hideGoToInvoiceButton() {
            $('#goto-invoice-label').css('display', 'none');
            $('#continue-label').css('display', 'inherit');
            document.getElementById("confirmAddressButton").classList.add("continue");
            document.getElementById("confirmAddressButton").classList.replace("btn-secondary", "btn-primary");
            document.getElementById("confirmAddressButton").classList.replace("float-xs-left", "float-xs-right");
         }
         /* PRIMERA CARGA */

         let initialMode = null;
  

         // 1) Si estamos en formulario de FACTURACIÓN, intentamos usar lo guardado
         
         if (invoiceForm) {

            if($('#field-alias').val() == '' || newAddress) {
               if(!getAddressType()) {
                  customerTypeBox.css('display', 'none');
               }
               const storedMode = localStorage.getItem('customer_type'); // 'COMPANY' o 'PARTICULAR'

               if (storedMode === 'COMPANY' || storedMode === 'PARTICULAR') {
                  initialMode = storedMode;

                  // Sincronizamos el radio para que coincida con lo guardado
                  if (storedMode === 'COMPANY') {
                     $('#field-empresa').prop('checked', true);
                  } else {
                     $('#field-particular').prop('checked', true);
                  }
               }

            }else{
               //EDITAR FORMULARIO
                  console.log('alias guardado: '+ $('#field-alias').val());
                  if($('#field-alias').val() == 'COMPANY'){
                     initialMode = 'COMPANY';
                     $('#field-empresa').prop('checked', true);
                  }
                  if($('#field-alias').val() == 'PARTICULAR'){
                     initialMode = 'PARTICULAR';
                     $('#field-particular').prop('checked', true);
                  }
            }
         }

         // 2) Si no hay valor en localStorage (o no es invoiceForm), usamos lo que venga marcado
         if (!initialMode) {
            if ($treatment.val() === 'particular') {
               initialMode = 'PARTICULAR';
            } else {
               initialMode = 'COMPANY';
            }
         }

         // 3) Si localStorage aún no tiene valor, lo inicializamos con el modo detectado
         if (!localStorage.getItem('customer_type')) {
            localStorage.setItem('customer_type', initialMode);
         }

         // 4) Aplicamos la configuración inicial del formulario
         //console.log('cart: '+ SubtotalProductsRoundUpInteger + ' type: '+invoiceForm+' | new: '+($('#field-alias').val() == '' || newAddress)+ ' |mode: '+ initialMode +' | useSame: '+useSameCheck);
         applyFormSetup(initialMode, useSameCheck);

         $('#field-empresa').on('change', function () {
            let mode = $(this).is(':checked') ? 'COMPANY' : 'PARTICULAR';
            let check = $(this).is(':checked') ? false : true;
   
            if ($(this).is(':checked')) {
               if ((document.getElementById('newAddress') && document.getElementById('newAddress').dataset.new == '1') && !getNewAddresParam) {//primera dirección
                  showGoToInvoiceButton();
               }
      
               $('#field-is_invoice').val(originalIsInvoice);
               localStorage.setItem('customer_type', mode);
            }
            
            applyFormSetup(mode, check);

         });



         $('#field-particular').on('change', function () {
            let useSameCheck = true;
            let mode = $(this).is(':checked') ? 'PARTICULAR' : 'COMPANY';
            let invoiceParam = getIsInvoiceParam();

            if(!invoiceForm) {
               if (document.getElementById('useDifferentAddress').checked){ //Diferente Dirección
                  useSameCheck = false;
               }
            }
            
            if ($(this).is(':checked')) {
               if (!useSameCheck) {
                 
                  $('#field-is_invoice').val(originalIsInvoice);
               } else {
                  if(!invoiceForm && !invoiceParam) {
                   
                     $('#field-is_invoice').val('2');
                  }
                  //addSameInputs(newAddress);
                  if ((document.getElementById('newAddress') && document.getElementById('newAddress').dataset.new == '1') && !getNewAddresParam) {//primera dirección
                     if(useSameCheck){
                        hideGoToInvoiceButton();
                     }else{
                        showGoToInvoiceButton();
                     }

                  }     
               }

               localStorage.setItem('customer_type', mode);
            }

            if (invoiceParam && invoiceParam != '2') {
               useSameCheck = false;
            }

            applyFormSetup(mode , useSameCheck);
         });

         $('#useDifferentAddress').on('change', function () {

            let mode = 'PARTICULAR'
            let useSameCheck = true;
            let invoiceParam = getIsInvoiceParam();

            localStorage.setItem('customer_type', mode);
            
            if (document.getElementById('useDifferentAddress').checked){ //Diferente Dirección
               useSameCheck = false;
               
               $('#field-is_invoice').val(originalIsInvoice);

               if ((document.getElementById('newAddress') && document.getElementById('newAddress').dataset.new == '1') && !getNewAddresParam) {
                  showGoToInvoiceButton();
               }

            }else{

               if ((document.getElementById('newAddress') && document.getElementById('newAddress').dataset.new == '1') && !getNewAddresParam) {
                  hideGoToInvoiceButton();
               }

               if (!invoiceParam){
                 
                  $('#field-is_invoice').val('2');
               }
               
            }
            

            console.log('aplicando cambio toggle : '+ mode + '/' +useSameCheck);
            applyFormSetup(mode, useSameCheck);
           
         });
         

         $fieldAddress2.css('display', 'none');

         const countryPrefixes = {
            1: '+49',   // DE
            2: '+43',   // AT
            3: '+32',   // BE
            6: '+34',   // ES
            7: '+358',  // FI
            8: '+33',   // FR
            9: '+30',   // GR
            10: '+39',  // IT
            12: '+352', // LU
            13: '+31',  // NL
            14: '+48',  // PL
            15: '+351', // PT
            16: '+420', // CZ
            17: '+44',  // GB
            18: '+46',  // SE
            19: '+41',  // CH
            20: '+45',  // DK
            23: '+47',  // NO
            26: '+353', // IE
            29: '+972', // IL
            36: '+40',  // RO
            37: '+421', // SK
            40: '+376', // AD
            52: '+375', // BY 
            74: '+385', // HR
            76: '+357', // CY
            86: '+372', // EE
            93: '+995', // GE
            97: '+350', // GI
            106: '+379',// VA
            124: '+371',// LV
            129: '+423',// LI
            130: '+370',// LT
            138: '+356',// MT
            142: '+36', // HU
            146: '+373',// MD
            147: '+377',// MC
            149: '+382',// ME
            188: '+381',// RS
            191: '+386',// SI
            231: '+387',// BA
            233: '+359' // BG
         };

         function detectOtherCountryPrefix(phoneDigits, selectedPrefixDigits) {
            const prefixes = Object.values(countryPrefixes)
               .map(prefix => prefix.replace(/\D/g, ''))
               .filter(Boolean)
               // Ordenar de más largo a más corto para evitar conflictos tipo 3, 34, 358
               .sort((a, b) => b.length - a.length);

            for (const prefixDigits of prefixes) {
               if (
                  prefixDigits !== selectedPrefixDigits &&
                  phoneDigits.startsWith(prefixDigits)
               ) {
                  return prefixDigits;
               }
            }

            return null;
         }

         if(document.getElementsByClassName('phoneClass')) {
            const countrySelect = document.querySelector('.id_countryClass');
            const phoneInput = document.querySelector('.js-phone-number');
            const prefixBox = document.querySelector('.js-phone-prefix');

            function cleanPhoneForDisplay() {
            if (!phoneInput || !countrySelect) return;

            let phone = phoneInput.value.replace(/\D/g, '');
            const countryId = countrySelect.value;
            const prefix = countryPrefixes[countryId] || '';
            const prefixDigits = prefix.replace(/\D/g, '');

            if (prefixDigits && phone.startsWith(prefixDigits)) {
               phone = phone.substring(prefixDigits.length);
            }

            phone = phone.replace(/^0+/, '');
            phoneInput.value = phone;
            }

            function updatePrefix() {
            if (!countrySelect || !prefixBox) return;

            const countryId = countrySelect.value;
            const prefix = countryPrefixes[countryId] || '';

            prefixBox.textContent = prefix || '+';
            prefixBox.setAttribute('data-prefix', prefix);
            }

            if (countrySelect) {
            countrySelect.addEventListener('change', function () {
               updatePrefix();
               cleanPhoneForDisplay();
            });

            updatePrefix();
            cleanPhoneForDisplay();
            }

            if (phoneInput) {
            phoneInput.addEventListener('input', function () {
               let value = this.value.replace(/\D/g, '');
               value = value.replace(/^0+/, '');
               this.value = value;
            });
            }
         }


         /* FIN LOGICA FORMULARIO DIRECCIONES */

         function resetButtonState() {
            setTimeout(() => {
               if(document.getElementById("cancel-address-form")) {
                  document.getElementById("cancel-address-form").style.display = "block";
               }
               if(document.getElementById("confirmAddressButton")) {
                  document.getElementById("confirmAddressButton").classList.remove("disabled");
               }
               if(document.getElementById("confirmAddressButton")) {
                  document.getElementById("confirmAddressButton").disabled = false;
               }
               if(document.getElementById("loader-overlay")) {
                  document.getElementById("loader-overlay").style.display = "none"; // Oculta el loader
               }
                
            }, 200);
        }

         function comprobarCPPorProvincia(cp, provincia) {
            console.log('cp: '+ cp);
            console.log('provincia: ' + provincia);
            const prefijosProvincias = {
               "354": "01", //alava
               "355": "02", //albacete
               "356": "03", //alicante
               "357": "04", //almeria
               "359": "05", //avila
               "360": "06", //badajoz
               "405": "07", //menorca
               "406": "07", //mayorca
               "407": "07", //ibiza
               "408": "07", //formentera
               "362": "08", //barcelona
               "363": "09", //burgos
               "364": "10", //caceres
               "365": "11", //cadiz
               "367": "12", //castellon
               "368": "13", //ciudad real
               "369": "14", //cordoba
               "353": "15", //la coruña
               "370": "16", //cuenca
               "371": "17", //gerona
               "372": "18", //granada
               "373": "19", //guadalajara
               "374": "20", //gipuzkua
               "375": "21", //huelva
               "376": "22", //huesca
               "377": "23", //jaen
               "380": "24", //leon
               "381": "25", //lerida
               "378": "26", //la rioja
               "382": "27", //lugo
               "383": "28", //madrid
               "384": "29", //malaga
               "385": "30", //murcia
               "386": "31", //navarra
               "387": "32", //orense
               "358": "33", //asturias
               "388": "34", //palencia
               "389": "36", //pontevedra
               "390": "37", //salamanca
               "366": "39", //cantabria
               "392": "40", //segovia
               "393": "41", //sevilla
               "394": "42", //soria
               "395": "43", //tarragona
               "396": "44", //teruel
               "397": "45", //toledo
               "398": "46", //valencia
               "399": "47", //vayadolid
               "400": "48", //vizcaya
               "401": "49", //zamora
               "402": "50", //zaragoza

            };

            // Normaliza entrada
            const cpStr = String(cp).padStart(5, '0');
            const prefijo = cpStr.substring(0, 2);

            console.log('prefijo: ' + prefijo);

            if (!prefijosProvincias[provincia]) {
               console.log('la provincia no es válida');
               return false;
            }

            return prefijosProvincias[provincia] === prefijo;
         }

         function validarDNI() {
            const dniInput = document.getElementById("field-dni").value.toUpperCase();
            const dniRegex = /^[0-9]{8}[A-Z]$/;  // DNI: 8 dígitos y 1 letra
            const nieRegex = /^[XYZ][0-9]{7}[A-Z]$/;  // NIE: X, Y o Z seguido de 7 dígitos y 1 letra
            const cifRegex = /^[ABCDEFGHJKLMNPQRSVW][0-9]{7}[0-9A-J]$/; // CIF

            if (dniRegex.test(dniInput)) {
               // Validación del DNI
               const numero = parseInt(dniInput.slice(0, 8));
               const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
               const letraEsperada = letras[numero % 23];
               return dniInput[8] === letraEsperada;
            } else if (nieRegex.test(dniInput)) {
               // Validación del NIE
               let numero = dniInput.slice(1, 8);
               switch (dniInput[0]) {
                     case 'X': numero = '0' + numero; break;
                     case 'Y': numero = '1' + numero; break;
                     case 'Z': numero = '2' + numero; break;
               }
               const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
               const letraEsperada = letras[parseInt(numero) % 23];
               return dniInput[8] === letraEsperada;
            } else if (cifRegex.test(dniInput)) {
               // Validación del CIF
               return isValidCif(dniInput);
            }

            return false; // Si no coincide con ninguna de las expresiones regulares
         }

         function validarNIE() {
            let dniInput = document.getElementById("field-dni").value.toUpperCase().trim();

            // Eliminar espacios internos y caracteres comunes como guiones o puntos
            dniInput = dniInput.replace(/[\s.-]/g, "");

            // Validar longitud mínima y máxima razonable
            if (dniInput.length < 5 || dniInput.length > 20) {
               return false;
            }

            // Solo permitir letras y números
            if (!/^[A-Z0-9]+$/.test(dniInput)) {
               return false;
            }

            // Debe contener al menos una letra o un número (evita cosas raras tipo "-----")
            if (!/[A-Z0-9]/.test(dniInput)) {
               return false;
            }

            return true;
         }

         // Función para validar el CIF
         function isValidCif(cif) {
            if (!cif || cif.length !== 9) {
               return false;
            }

            var letters = ['J', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
            var digits = cif.substr(1, cif.length - 2);
            var letter = cif.substr(0, 1);
            var control = cif.substr(cif.length - 1);
            var sum = 0;

            if (!letter.match(/[A-Z]/)) {
               return false;
            }

            for (let i = 0; i < digits.length; ++i) {
               let digit = parseInt(digits[i]);

               if (isNaN(digit)) {
                     return false;
               }

               if (i % 2 === 0) {
                     digit *= 2;
                     if (digit > 9) {
                        digit = Math.floor(digit / 10) + (digit % 10);
                     }
               }

               sum += digit;
            }

            sum %= 10;
            let digitControl = (sum !== 0) ? (10 - sum) : 0;

            if (letter.match(/[ABEH]/)) {
               return String(digitControl) === control;
            }
            if (letter.match(/[NPQRSW]/)) {
               return letters[digitControl] === control;
            }

            return String(digitControl) === control || letters[digitControl] === control;
         }

         /* VALIDACIONES */
         function getValidations() {
               let validation = true;
               let useSameCheck = true;

               if (document.getElementById('useDifferentAddress')) {
                  if (document.getElementById('useDifferentAddress'). checked){ //Diferente Dirección
                     useSameCheck = false;
                  }
               }

               let isInvoiceParam = getIsInvoiceParam();

               
               if (isInvoiceParam) {
                  useSameCheck = (isInvoiceParam == '2' || isInvoiceParam == '1') ? true : false;
               }

               if ($('#field-particular').is(':checked')) {
                  const isSpanish = $('#field-id_country').val() == 6;
                  const shouldValidateForeignId = !isSpanish && SubtotalProductsRoundUpInteger >= 2200;
                  const shouldValidate = useSameCheck && (isSpanish || shouldValidateForeignId);

                  if (shouldValidate) {
                     const dniValue = $('#field-dni').val().trim();
                     const errorSpan = document.getElementById("dni-error");

                     let isValid = true;
                     let errorMessage = "";

                     if (dniValue === "") {
                        isValid = false;
                        errorMessage = isSpanish
                        ? "Formato incorrecto. Introduzca un DNI o NIE válido."
                        : "ID error: wrong format";
                        console.log(`Error: validation id vacío | ${isSpanish ? 'español' : 'extranjero'}`);
                     } else if (isSpanish && !validarDNI()) {
                        isValid = false;
                        errorMessage = "Formato incorrecto. Introduzca un DNI o NIE válido.";
                        console.log("ERRORES CON DNI...");
                     } else if (!isSpanish && !validarNIE()) {
                        isValid = false;
                        errorMessage = "ID error: wrong format";
                        console.log("ERRORES CON ID extranjero...");
                     }

                     if (!isValid) {
                        validation = false;
                        if (errorSpan) {
                        errorSpan.style.display = "block";
                        errorSpan.innerText = errorMessage;
                        }
                     } else if (errorSpan) {
                        errorSpan.style.display = "none";
                     }
                  }
               }

               if( $( '#field-empresa' ).is(':checked')) {//VALIDAR EMPRESA
                  if (invoiceForm){
                     if($( '#field-company' ).val() == '')  {
                        console.log('error en nombre empresa')
                     }

                     if ($('#field-dni').val() == '') {
                        document.getElementById("dni-error").style.display = "block";// error cif/dni vacío
                        console.log('Error: validation cif empresa vacio | check off');
                        validation = false;
                     }else if(!validarDNI() && $('#field-id_country').val() == 6) {
                        validation = false;
                        console.log('ERRORES CON DNI...');
                        const errorSpan = document.getElementById("dni-error");
                        errorSpan.style.display = "block";
                        errorSpan.innerText = "Formato incorrecto. Introduzca un CIF válido.";
                     }else{
                        if(document.getElementById("dni-error")) {
                           document.getElementById("dni-error").style.display = "none";
                        }  
                     }   

                  }
                                  
               }

               if($('#field-city').val() == ''){ //validar ciudad
                  console.log('error en city');
                  document.getElementById("city-required-error").style.display = "block";
                  validation = false;
               }else{
                  document.getElementById("city-required-error").style.display = "none";
               }

               if($('#field-address1').val() == ''){ //validar dirección
                  console.log('error en address');
                  document.getElementById("address-required-error").style.display = "block";
                  validation = false;
               }else{
                  document.getElementById("address-required-error").style.display = "none";
               }

               if($('#field-postcode').val() == ''){ //Validar Codigo Postal
                  console.log('error en postcode');
                  document.getElementById("postcode-required-error").style.display = "block";
                  document.getElementById("postcode-matchmaking").style.display = "none";
                  validation = false;
               }else{                
                  if($('#field-id_country').val() == '6'){
                     console.log('comprobando codigo postal con provincia...');
                     if(!comprobarCPPorProvincia($('#field-postcode').val(),$('#field-id_state').val())){
                        console.log('codigo postal no casa con la provincia');
                        document.getElementById("postcode-required-error").style.display = "none";
                        document.getElementById("postcode-matchmaking").style.display = "block";
                        validation = false;
                     }else{
                        console.log('codigo postal correcto');
                        document.getElementById("postcode-required-error").style.display = "none";
                        document.getElementById("postcode-matchmaking").style.display = "none";
                     }
                  }else{
                     document.getElementById("postcode-required-error").style.display = "none";
                     document.getElementById("postcode-matchmaking").style.display = "none";
                  }
                  
               }

               if($('#field-phone').val() == ''){ // valida teléfono
               console.log('error en phone');
               document.getElementById("phone-required-error").style.display = "block";
               validation = false;

               }else{
                  document.getElementById("phone-required-error").style.display = "none";

                  var raw = $('#field-phone').val();

                  // 👉 dejar solo números y quitar ceros iniciales
                  var phone = raw.replace(/\D/g, '').replace(/^0+/, '');

                  // 👉 obtener prefijo actual desde el DOM
                  var prefix = $('.js-phone-prefix').attr('data-prefix') || '';
                  var prefixDigits = prefix.replace(/\D/g, '');

                  // 👉 evitar duplicar prefijo si ya venía (modo editar)
                  if (prefixDigits && phone.startsWith(prefixDigits)) {
                     phone = phone.substring(prefixDigits.length);
                  }

                  // Detectar si el usuario ha escrito otro prefijo internacional
                  var otherPrefix = detectOtherCountryPrefix(phone, prefixDigits);

                     //if (otherPrefix || phone.length >= 12) {
                     if (phone.length >= 12) {
                        console.log('El teléfono parece tener otro prefijo internacional:', otherPrefix);

                        document.getElementById("phone-required-error").innerText =
                           "Error: Use a selected country phone and less than 12 digits";

                        document.getElementById("phone-required-error").style.display = "block";
                        validation = false;
                     } else {
                        document.getElementById("phone-required-error").style.display = "none";

                        // Reconstruir valor final para guardar
                        $('#field-phone').val(prefix + phone);
                     }
               }

               console.log('las validaciones son: ' + validation);

               return validation;
         }

         /* VALIDAR Y COMPROBAR INTRACOMUNITARIO */
   
         if(document.getElementById("address-form")) {

               
               let validations = false;

               if (document.getElementById("confirmAddressButton").getAttribute("data-location") == "form") {

                  if($('#field-company').val() != ''){
                     $('#field-empresa').prop('checked', true).trigger('change');
                  }

                  document.getElementById("confirmAddressButton").addEventListener("click", function(event) {

                     var loader = document.getElementById("loader-overlay");
                     // Mostrar loader y bloquear botones
                     if (loader) {
                        loader.style.display = "flex";
                     }

                     // LOGICA NUEVA SIN AJAX
                     event.preventDefault();
                     validations = getValidations(); 

                     if (validations !== true) {
                        console.log('Fallo validaciones...');
                        resetButtonState();
                        return;
                     }
                     
                     // Extranjero PARTICULAR con DNI: limpiamos DNI y empresa antes de enviar
                     if ($('#field-id_country').val() != 6 && $('#field-dni').val() != '' && $('#field-particular').is(':checked')) {
                        
                        if (SubtotalProductsRoundUpInteger < 2200) {
                           $('#field-dni').val('');
                        }
                        
                        $('#field-company').val('');
                     }

                     // Extranjero PARTICULAR sin DNI: limpiar empresa por si acaso
                     if ($('#field-id_country').val() != 6 && $('#field-dni').val() == '' && $('#field-particular').is(':checked')) {
                        $('#field-company').val('');
                     }

                     // España + PARTICULAR: limpiar empresa
                     if ($('#field-id_country').val() == 6 && $('#field-particular').is(':checked')) {
                        $('#field-company').val('');
                     }

                     setConfirmAndSameInputs();
                     document.getElementById("address-form").submit();


                  });
               }

         
         }
         
        
      }


      (function () {
      var step = document.getElementById('checkout-delivery-step');
      if (!step) return;

      // Solo si el paso está activo/visible
      if (!step.classList.contains('-current') && !step.classList.contains('js-current-step')) return;

      var inputs = step.querySelectorAll('input[name^="delivery_option["]');
      if (inputs.length !== 1) return;

      var input = inputs[0];
      if (input.checked) return;

      input.checked = true;
      input.dispatchEvent(new Event('change', { bubbles: true }));
      })();
      

         /* END CHECKOUT */

         /* Enlaces ofuscados */
         document.querySelectorAll('.js-ofuscado-enlace').forEach(el => {
            el.addEventListener('click', function(event) {
               const url = event.currentTarget.dataset.filter;
               if (url) {
                  location.href = url;
               }
            });

         });

         //Banear correos spam
         const blackList = ['test@example.com'];
         const submitButton = document.getElementById('send-professional-button');
         if (submitButton) {
            const form = submitButton.closest('form');
            if (form) {
               form.addEventListener('submit', function (event) {
                  const emailInput = form.querySelector('input[name="from"]');
                  if (emailInput) {
                        const email = emailInput.value.trim().toLowerCase();
                        const domain = email.split('@')[1];
                     // Comparación por email exacto o dominio
                     if (blackList.includes(email) || domain === 'example.com') {
                           event.preventDefault(); // Bloquea el envío del formulario
                           window.location.href = '/'; // Redirige a la home
                           return false; // Extra seguro
                     }
                  }
               });
            }
         }
     
   }

   

   let customCarousel = function(divItemsName) {

      let carousel = document.getElementById(divItemsName);
      let inner = carousel.querySelector('.custom-carousel-inner');
      let prevBtn = carousel.querySelector('.custom-carousel-prev');
      let nextBtn = carousel.querySelector('.custom-carousel-next');

      let currentIndex = 0;
      let items = inner.children.length; //total de items
      let itemsOnScreen = parseInt(carousel.getAttribute('data-items-on-screen')) || 0;
      let itemWidth = inner.children[0].offsetWidth;

      nextBtn.addEventListener('click', function() {
         if ((currentIndex + itemsOnScreen) < items) {
               currentIndex++;
               inner.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
         }
      });

      prevBtn.addEventListener('click', function() {
         if (currentIndex > 0) {
               currentIndex--;
               inner.style.transform = `translateX(-${currentIndex * itemWidth}px)`;  
         }
      });

   }
      
   if (document.getElementById('related-products-carousel')){
      customCarousel('related-products-carousel');
   }

   if (document.getElementById('related-products-carousel-mobile')){
      customCarousel('related-products-carousel-mobile');
   }

   initializeCustom();

   prestashop.on( 'updatedAddressForm', function(){
      initializeCustom();
   });

   prestashop.on( 'updateDeliveryForm', function(){
      initializeCustom();
   });


   prestashop.on( 'updatedDeliveryForm', function(){
      initializeCustom();
   });

   prestashop.on( 'updateProductList', function () {

      initializeCustom();



      $.each( myGlobal, function ( index, value ) {

         $( '.accordion[data-label="' + value + '"]' ).click();

      } );

   } );

   if(document.getElementById('toggle-description')) {

      document.getElementById('toggle-description').addEventListener('click', function() {
         if(document.getElementById('toggle-description').checked) {
            document.getElementById('row-product-description').style.display = 'block';
            document.getElementById('toggle-hide-description-label').style.display = 'inline-block';
            document.getElementById('toggle-show-description-label').style.display = 'none';
         } else {
            document.getElementById('row-product-description').style.display = 'none';
            document.getElementById('toggle-hide-description-label').style.display = 'none';
            document.getElementById('toggle-show-description-label').style.display = 'inline-block';
         }
       });

   }


   /* BOTÓN LEER MÁS */
    const btnmore = document.querySelector('.read-more-btn');
    const btnless = document.querySelector('.read-less-btn');
    const container = document.querySelector('.category-description');

    if (btnmore && container) {
      btnmore.addEventListener('click', function () {
        container.classList.add('expanded');
        btnmore.style.display = 'none';
        btnless.style.display = 'inline-block';
      });

      btnless.addEventListener('click', function () {
        container.classList.remove('expanded');
        btnless.style.display = 'none';
        btnmore.style.display = 'inline-block';
      });
    }

   //Código para retrasar la carga del widget de eTrusted

   window.addEventListener("load", function() {
   setTimeout(() => {
      const trustedWidgetScript = document.createElement("script");
      trustedWidgetScript.src = "https://integrations.etrusted.com/applications/widget.js/v2";
      trustedWidgetScript.async = true;
      trustedWidgetScript.defer = true;
      document.head.appendChild(trustedWidgetScript);
   }, 3000); // Ajusta el tiempo de retraso según lo que prefieras
   });





} );