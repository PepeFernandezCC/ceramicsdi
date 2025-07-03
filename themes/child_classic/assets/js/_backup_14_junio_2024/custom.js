$( document ).ready( function () {

   let myGlobal = [];

   let initializeCustom = function () {

      let $body = $( 'body' );



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



      const $surfaceInputReal = $( '#surface-real' );

      const $piecesInputReal = $( '#pieces-real' );



      let m2Caja = $surfaceInput.attr( 'data-m2-caja' );

      if ( m2Caja !== undefined ) {

         m2Caja = parseFloat( m2Caja.replace( ',', '.' ) );

      }

      let piezasCaja = $piecesInput.attr( 'data-piezas-caja' );

      if ( piezasCaja !== undefined ) {

         piezasCaja = parseFloat( piezasCaja.replace( ',', '.' ) );

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

            let piecesValue = Math.ceil( quantityValue * piezasCaja );

            let surfaceValueReal = ( quantityValue * m2Caja ).toFixed( 2 );



            $quantityInput.val( quantityValue );

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



      $( '#add-wrapper' ).find( '.add-sample' ).find( '.add-to-cart-sample' ).on( 'click', function () {

         $( '#variants-wrapper' ).find( '.input-radio:input[value="6"]' ).removeAttr( 'checked' );

         $( '#variants-wrapper' ).find( '.input-radio:input[value="5"]' ).attr( 'checked', 'checked' );

         $( '#quantity-input' ).val( 1 );

         $( '#add-wrapper' ).find( '.add' ).find( '.add-to-cart' ).click();

         $( '#variants-wrapper' ).find( '.input-radio' ).removeAttr( 'checked' );

         $( this ).attr( 'disabled', 'disabled' );

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



      /*$( '#recipeCarousel' ).carousel( {

       interval: 5000

       } )*/



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



      if ( $( window ).scrollTop() !== 0 ) {

         $( '#header' ).find( '.header-nav' ).addClass( 'header-sticky' );

         $( '#header' ).find( '.ets_mm_megamenu' ).addClass( 'scroll_heading' );

      }



      $( window ).scroll( function () {

         let scroll = $( window ).scrollTop();



         if ( scroll !== 0 ) {

            $( '#header' ).find( '.header-nav' ).addClass( 'header-sticky' );

            $( '#header' ).find( '.ets_mm_megamenu' ).addClass( 'scroll_heading' );

         } else {

            if (!$('#menu-ceramic').hasClass('up-menu')) {
               $( '#header' ).find( '.header-nav' ).removeClass( 'header-sticky' );
            }
            $( '#header' ).find( '.ets_mm_megamenu' ).removeClass( 'scroll_heading' );

         }

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
         $( '#header' ).find( '.header-nav' ).toggleClass( 'header-sticky' );
         $('#trustbadge-container-98e3dadd90eb493088abdc5597a70810').toggleClass('d-none-forced');
         $( '.whatsapp' ).toggleClass( 'd-none-forced' );

      } );


      
      //FUNCIONALIDAD MANTIENE ABIERTO
      //cambia el ' + ' por ' - ' al abrir una opción del menu
      $( '.mm_has_sub:not(.custom-menu-ceramica)' ).on( 'click', function () {

         $( this ).toggleClass( 'opened' );
         console.log('alpha derribado');

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
      
   /*   
   //FUNCIONALIDAD UNO ABRE LOS OTROS CIERRAN
   //cambia el '+' por '-' al abrir una opción del menu
   $('.mm_has_sub:not(.custom-menu-ceramica)').on('click', function() {
      // Cierra todas las opciones excepto la que se está abriendo
      $('.mm_has_sub').not(this).removeClass('opened');
      $('.mm_blocks_ul .ets_mm_block_content').not($(this).find('.mm_blocks_ul .ets_mm_block_content')).css('display', 'none');
      $(this).toggleClass('opened');
      
   });

   //muestra o esconde el contenido de la opción del menú
   $('.ets_mm_block').find('span').on('click', function() {
         var $title = $(this).find('.h4');
         var $blockContent = $(this).closest('.mm_blocks_ul').find('.ets_mm_block_content');

         if ($blockContent.css('display') == 'none') {
            $title.addClass('opened');
            $blockContent.css('display', 'block');
         } else {
            $title.removeClass('opened');
            $blockContent.css('display', 'none');
         }
   });
   */

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



   prestashop.on( 'updateProductList', function () {

      initializeCustom();



      $.each( myGlobal, function ( index, value ) {

         $( '.accordion[data-label="' + value + '"]' ).click();

      } );

   } );

   
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

} );