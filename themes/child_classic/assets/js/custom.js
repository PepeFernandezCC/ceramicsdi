$( document ).ready( function () {

   let myGlobal = [];
   
   let initializeCustom = function () {

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

      if (document.getElementById('numberInput')) {

         document.getElementById('incrementButton').addEventListener('click', function() {

            numberInput.value = parseInt(numberInput.value) + 1;
  
            calculatem2bybox(m2Caja, numberInput, document.getElementById('surface-input'));

        });
    
        document.getElementById('decrementButton').addEventListener('click', function() {
  
           if(document.getElementById('numberInput').value > 0) {
  
              numberInput.value = parseInt(numberInput.value) - 1;
  
              calculatem2bybox(m2Caja, numberInput, document.getElementById('surface-input'));

           }

        });
  
        //document.getElementById('surface-input').addEventListener('change', function() {
  
        $('#surface-input').keyup( function() {
  
           let m2required = document.getElementById('surface-input').value;
  
           calculatem2OnChangeEvent(m2required, numberInput);
           
        })
  
        //numberInput.addEventListener('change', function() {
        $('#numberInput').keyup ( function() {
        
           calculatem2bybox(m2Caja, document.getElementById('numberInput'), document.getElementById('surface-input'));

        })
  
  
        let calculatem2bybox = function(m2Caja, numberInput, numberm2needed) {
  
           let totalSurface = (numberInput.value * m2Caja).toFixed( 2 );
  
           numberm2needed.value = totalSurface;
  
           $surfaceInputReal.val( totalSurface );
  
           setQuantitiesValue(numberInput.value);
  
           $eurosInput.val( ( numberInput.value * price ).toFixed( 2 ) );
          
        }
  
        let calculatem2OnChangeEvent = function(m2required, numberInput) {
  
           let quantity = Math.ceil(m2required / m2Caja);
  
           numberInput.value = quantity;
  
           let totalSurface = (numberInput.value * m2Caja).toFixed( 2 );
  
           //document.getElementById('surface-input').value = totalSurface;
  
           $surfaceInputReal.val( totalSurface );
  
           setQuantitiesValue(quantity)
  
           $eurosInput.val( ( numberInput.value * price ).toFixed( 2 ) );
  
  
        }
  
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

            inputQuantityBox.value = parseInt(inputQuantityBox.value) + 1;
            $eurosInput.val( ( inputQuantityBox.value * price ).toFixed( 2 ) );
  

         });
     
         document.getElementById('decrementQuantity').addEventListener('click', function() {
   
            if(document.getElementById('quantity-input').value > 0) {
   
               inputQuantityBox.value = parseInt(inputQuantityBox.value) - 1;
               $eurosInput.val( ( inputQuantityBox.value * price ).toFixed( 2 ) );
  
            }
         });

         $('#quantity-input').keyup( function() {
   
            $eurosInput.val( ( inputQuantityBox.value * price ).toFixed( 2 ) );
         })
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

      const inputPiecesBox = document.getElementById('inputPiecesBox');

      if (document.getElementById('inputPiecesBox')) {

         document.getElementById('incrementPieces').addEventListener('click', function() {

            inputPiecesBox.value = parseInt(inputPiecesBox.value) + 1;
   
            calculatepiecesbybox(piezasCaja, inputPiecesBox, document.getElementById('pieces-input'));

         });
     
         document.getElementById('decrementPieces').addEventListener('click', function() {
   
            if(document.getElementById('inputPiecesBox').value > 0) {
   
               inputPiecesBox.value = parseInt(inputPiecesBox.value) - 1;
   
               calculatepiecesbybox(piezasCaja, inputPiecesBox, document.getElementById('pieces-input'));

               updateButtonStateByPiece();
   
            }
         });
   
         $('#pieces-input').keyup( function() {
   
            calculatePiecesOnChangeEvent(document.getElementById('pieces-input').value, inputPiecesBox);
    
         })
   
         $('#inputPiecesBox').keyup ( function() {
            calculatepiecesbybox(piezasCaja, document.getElementById('inputPiecesBox'), document.getElementById('pieces-input'));

            updateButtonStateByPiece();
         })
   
         //FUNCIONES
   
         let calculatepiecesbybox = function(piezasCaja, inputPieces, piecesNeeded) {
   
            let totalPieces = (inputPieces.value * piezasCaja).toFixed( 2 );
   
            piecesNeeded.value = totalPieces;
   
            $piecesInputReal.val( totalPieces );
   
            setPiecesQuantitiesValue(inputPieces.value);
   
            $eurosInput.val( ( inputPieces.value * price ).toFixed( 2 ) );
           
         }
   
         let calculatePiecesOnChangeEvent = function(piecesRequired, inputPieces) {
   
            let quantity = Math.ceil(piecesRequired / piezasCaja);
   
            inputPieces.value = quantity;
   
            let totalPieces = (inputPieces.value * piezasCaja).toFixed( 2 );
   
            $piecesInputReal.val( totalPieces );
   
            setPiecesQuantitiesValue(quantity);
   
            $eurosInput.val( ( inputPieces.value * price ).toFixed( 2 ) );
   
   
         }
   
         let setPiecesQuantitiesValue = function(quantity) {
   
            let piecesValue = Math.ceil( quantity * piezasCaja );
   
            $piecesInputReal.val( piecesValue );
   
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

         $.ajax({
            url: '/ajax/checkSamplesInCart.php', 
            method: 'POST', 
            data: {
               id_cart: cartId,
            },
            success: function(response) {
               
               // Si la respuesta es válida, puedes usar el resultado (por ejemplo, el costo de envío)
               if (response.can_add_sample) {

                  var quantityOriginalValue = $( '#quantity-input' ).val();
           
                  $( '#variants-wrapper' ).find( '.input-radio:input[value="6"]' ).removeAttr( 'checked' );
      
                  $( '#variants-wrapper' ).find( '.input-radio:input[value="5"]' ).attr( 'checked', 'checked' );
      
                  $( '#quantity-input' ).val( 1 );
      
                  $( '#add-wrapper' ).find( '.add' ).find( '.add-to-cart' ).click();
      
                  $( '#variants-wrapper' ).find( '.input-radio' ).removeAttr( 'checked' );
      
                  $( '#quantity-input' ).val( quantityOriginalValue );
      
                  $( this ).attr( 'disabled', 'disabled' );
      
                  $('#sample-in-cart').show();
      
               }else{
      
                  $( this ).attr( 'disabled', 'disabled' );
      
                  $('#max-samples-reached').show();
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
         $('#field-siret').closest('.form-group').css('display', 'none');
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

      //CHECKOUT VAT

      if (document.getElementById('delivery-address')) {

         let $treatment = $( 'input[name="treatment"]:checked' );
         let $fieldAlias = $( '#field-alias' ).closest( '.form-group' );
         let $fieldFirstName = $( '#field-firstname' ).closest( '.form-group' );
         let $fieldLastName = $( '#field-lastname' ).closest( '.form-group' );
         let $fieldCompany = $( '.companyClass' ).closest( '.form-group' );
         let $fieldVatNumber = $( '#field-vat_number' ).closest( '.form-group' );
         let $fieldDniCif = $( '#field-dni' ).closest( '.form-group' );
         let $fieldAddress2 = $( '#field-address2' ).closest( '.form-group' );
         let $dniLabel = $('.dniShowClass');
         let $cifLabel = $('.cifShowClass');
         let intracomunitaryInput = $('#intracomunitary-identification');
         let intracomunitaryCheck = $('#intracomunitary-checkbox');
   
         let aliasTranslation = $('#alias-translation').data('translation');
         $fieldAlias.find('label').html(aliasTranslation);
         let companyTranslation = $('#company-translation').data('translation');
         $fieldCompany.find('label').html(companyTranslation);
         let address2Translation = $('#address2-translation').data('translation');
         $fieldAddress2.find('label').html(address2Translation);
         let firstNameTranslationCompany = $('#firstname-translation-company').data('translation');
         let firstNameTranslationParticular = $('#firstname-translation-particular').data('translation');
         
         $fieldVatNumber.find('input').prop('required', false);
         $fieldVatNumber.css( 'display', 'none' );
         $fieldVatNumber.find('input').val('');
         

         /* PRIMERA CARGA */
         if ( $treatment.val() === 'empresa' ) {
   
            intracomunitaryInput.css('display', 'inherit');
            $fieldDniCif.css( 'display', 'inherit' );
            $fieldCompany.css( 'display', 'inherit' );
            $fieldCompany.find('input').prop('required', true);
            $fieldFirstName.find('label').html(firstNameTranslationCompany);
            $fieldLastName.css('display', 'none');
            $fieldLastName.find('input').prop('required', false);
            $fieldLastName.find('input').val('');

         } else if ( $treatment.val() === 'particular' ) {

            $fieldCompany.css( 'display', 'none' );
            $fieldCompany.find('input').prop('required', false);

            $fieldFirstName.find('label').html(firstNameTranslationParticular);
            $fieldLastName.css('display', 'inherit');
            $fieldLastName.find('input').prop('required', true);
            intracomunitaryInput.css('display', 'none');

            if ($('#field-id_country').val() != 6) {
               $fieldDniCif.css( 'display', 'none' );
            }else{
               $fieldDniCif.css( 'display', 'inherit' );
            }


         }


         $( '#field-empresa' ).on( 'change', function () {

            if ( $( this ).is( ':checked' ) ) {
      
               $fieldDniCif.css( 'display', 'inherit' );        
               intracomunitaryInput.css('display', 'inherit');
               if ($('#field-id_country').val() == 6) {
                  intracomunitaryInput.css('display', 'none');
               }

               $dniLabel.css( 'display', 'none' );
               $cifLabel.css( 'display', 'inherit' );
               $fieldCompany.css( 'display', 'inherit' );
               $fieldCompany.find('input').prop('required', true);
               $fieldFirstName.find('label').html(firstNameTranslationCompany);
               $fieldLastName.css('display', 'none');
               $fieldLastName.find('input').prop('required', false);
               $fieldLastName.find('input').val('');
               
            } else {
               $dniLabel.css( 'display', 'inherit' );
               $cifLabel.css( 'display', 'none' );
               $fieldCompany.css( 'display', 'none' );
               $fieldCompany.find('input').prop('required', false);
               $fieldFirstName.find('label').html(firstNameTranslationParticular);
               $fieldLastName.css('display', 'inherit');
               $fieldLastName.find('input').prop('required', true);
               $fieldLastName.find('input').val('');
               intracomunitaryInput.css('display', 'none');

               if ($('#field-id_country').val() != 6) {
                  $fieldDniCif.css( 'display', 'none' );
               }else{
                  $fieldDniCif.css( 'display', 'inherit' );
               }
            }
         } );
   
         $( '#field-particular' ).on( 'change', function () {
            if ( $( this ).is( ':checked' ) ) {
               $dniLabel.css( 'display', 'inherit' );
               $cifLabel.css( 'display', 'none' );
               $fieldCompany.css( 'display', 'none' );
               $fieldCompany.find('input').prop('required', false);
               $fieldFirstName.find('label').html(firstNameTranslationParticular);
               $fieldLastName.css('display', 'inherit');
               $fieldLastName.find('input').prop('required', true);
               $fieldLastName.find('input').val('');
               intracomunitaryInput.css('display', 'none');

               if ($('#field-id_country').val() != 6) {
                  $fieldDniCif.css( 'display', 'none' );
               }else{
                  $fieldDniCif.css( 'display', 'inherit' );
               }

            } else {
               $fieldDniCif.css( 'display', 'inherit' );
               intracomunitaryInput.css('display', 'inherit');

               if ($('#field-id_country').val() == 6) {
                  intracomunitaryInput.css('display', 'none');
               } 

               $dniLabel.css( 'display', 'none' );
               $cifLabel.css( 'display', 'inherit' );
               $fieldCompany.css( 'display', 'inherit' );
               $fieldCompany.find('input').prop('required', true);
               $fieldFirstName.find('label').html(firstNameTranslationCompany);
               $fieldLastName.css('display', 'none');
               $fieldLastName.find('input').prop('required', false);
               $fieldLastName.find('input').val('');
            }
         } );

         intracomunitaryCheck.on( 'change', function () {
            let $fieldDniCif = $( '#field-dni' ).closest( '.form-group' );

            if ( $( this ).is( ':checked' ) ) {
               $fieldVatNumber.find('input').prop('required', true);
               $fieldVatNumber.css( 'display', 'inherit' );
               $fieldDniCif.css( 'display', 'none' );
               $fieldDniCif.find('input').prop('required', false);
            }else{
               $fieldDniCif.find('input').prop('required', true);
               $fieldDniCif.css( 'display', 'inherit' );
               $fieldVatNumber.css( 'display', 'none' );
               $fieldVatNumber.find('input').prop('required', false);

            }

         });

         $fieldAddress2.css('display', 'none');

         function resetButtonState() {
            setTimeout(() => {
                document.getElementById("cancel-address-form").style.display = "block";
                document.getElementById("confirmAddressButton").classList.remove("disabled");
                document.getElementById("confirmAddressButton").disabled = false;
                document.getElementById("loader-overlay").style.display = "none"; // Oculta el loader
            }, 200);
        }

        function getValidations() {
            let validation = true;

            if ( intracomunitaryCheck.is(':checked')) { 
               if($('#field-vat_number').val() == ''){
                  console.log('error en vat');
                  document.getElementById("vat-required-error").style.display = "block";
                  validation = false;
               }else{
                  document.getElementById("vat-required-error").style.display = "none";
               }
            }
            /*
            if($('#field-firstname').val() == ''){
               console.log('error en nombre');
               document.getElementById("firstname-required-error").style.display = "block";
               validation = false;
            }else{
               document.getElementById("firstname-required-error").style.display = "none";
            }
            */
            if($('#field-city').val() == ''){
               console.log('error en city');
               document.getElementById("city-required-error").style.display = "block";
               validation = false;
            }else{
               document.getElementById("city-required-error").style.display = "none";
            }

            if($('#field-address1').val() == ''){
               console.log('error en address');
               document.getElementById("address-required-error").style.display = "block";
               validation = false;
            }else{
               document.getElementById("address-required-error").style.display = "none";
            }

            if($('#field-postcode').val() == ''){
               console.log('error en postcode');
               document.getElementById("postcode-required-error").style.display = "block";
               validation = false;
            }else{
               document.getElementById("postcode-required-error").style.display = "none";
            }

            if($('#field-phone').val() == ''){
               console.log('error en phone');
               document.getElementById("phone-required-error").style.display = "block";
               validation = false;
            }else{
               document.getElementById("phone-required-error").style.display = "none";
            }

            console.log('las validaciones son: ' + validation);

            return validation;
        }

         // Escucha el evento submit
         if(document.getElementById("address-form")) {
            document.getElementById("confirmAddressButton").addEventListener("click", function(event) {
               console.log('botón apretado...');
               var loader = document.getElementById("loader-overlay");
               if (getValidations() === true) {
                  console.log('Validación Ok...');
                  if ($('#field-id_country').val() != 6 && $( '#field-empresa' ).is(':checked')){
                     console.log('Empresa internacional detectada...');
                     loader.style.display = "flex";
                     document.getElementById("confirmAddressButton").classList.add("disabled");
                     document.getElementById("cancel-address-form").style.display ="none";
                     event.preventDefault();
   
                     if ( intracomunitaryCheck.is(':checked')) { 
                        console.log('Validando VAT');
                        if($fieldVatNumber.find('input').val() != '') {// vat no vacío
                           document.getElementById("vat-required-error").style.display = "none";// borra error
                           $('#field-dni').val($('#field-vat_number').val());// copia vat en dni
                           //loader.style.display = "none";
                           document.getElementById("address-form").submit();
                        }else {
                           document.getElementById("vat-required-error").style.display = "block";//muestra error vat vacío
                           resetButtonState();
                        }  
                     }else{
                        if ($('#field-dni').val() != '') { // CIF/DNI NO VACÍO
                           event.preventDefault();
                           console.log('Validando CIF');
                           $.ajax({ // comprueba si el vat es válido
                              url: '/ajax/checkVatNumber.php', 
                              method: 'POST', 
                              data: {
                                 vat_number: $('#field-dni').val(),
                              },
                              success: function(response) {                                 
                                 if (response.result) {
                                    $fieldVatNumber.find('input').val($('#field-dni').val()); 
                                 } else {
                                    $fieldVatNumber.find('input').val('');
                                 }
                                 //loader.style.display = "none";
                                 document.getElementById("address-form").submit(); //envía el formulario
                              },
                              error: function(err) {
                                 console.error('Error en la solicitud AJAX:', err);
                                 resetButtonState();
                              }
                        });
                        }else{ 
                           document.getElementById("dni-error").style.display = "block";// error cif/dni vacío
                           resetButtonState();
                        }   
                     }
                  }
               } else{
                  console.log('Fallo validaciones...');
                  resetButtonState();
               }

            });
         }
        
      }
   
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