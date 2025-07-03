/**
 * 2007-2023 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2023 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */
$( document ).ready( function () {
   let $seccionesWrapper = $( '#recomendacion-secciones' );
   let $secciones = $seccionesWrapper.find( '.recomendacion-seccion' );
   let $apartadosWrapper = $( '#recomendacion-apartados-seccion' );
   let $titulos = $apartadosWrapper.find( '.recomendacion-seccion-titulo' );
   let $listaApartados = $apartadosWrapper.find( '.recomendacion-apartados-lista' );
   let $apartados = $listaApartados.find( '.recomendacion-apartado-titulo' ).find( 'span' );
   let $contenidos = $apartadosWrapper.find( '.recomendacion-apartados-contenido' );

   let $mobileApartadosWrapper = $( '#recomendacion-apartados-seccion-mobile' );
   let $mobileTitulos = $mobileApartadosWrapper.find( '.recomendacion-seccion-titulo' );
   let $mobileListaApartados = $mobileApartadosWrapper.find( '.recomendacion-apartados-lista' );
   let $mobileApartados = $mobileListaApartados.find( '.recomendacion-apartado-titulo' ).find( 'span' );
   let $mobileContenidos = $mobileListaApartados.find( '.recomendacion-apartados-contenido-global' );

   $secciones.each( function () {
      $( this ).on( 'click', function () {
         let seccionId = $( this ).data( 'show-id' );

         $seccionesWrapper.addClass( 'd-none' );
         $apartadosWrapper.removeClass( 'd-none' );

         $contenidos.each( function () {
            $( this ).addClass( 'd-none' );
         } );

         $( '#recomendacion-seccion-' + seccionId ).removeClass( 'd-none' );
         $titulos.find( '.recomendacion-seccion-titulo' ).each( function () {
            $( this ).removeClass( 'open' );
         } );
         $( '#recomendacion-botones-seccion-' + seccionId ).find( '.recomendacion-seccion-titulo' ).addClass( 'open' );
         $( '#recomendacion-botones-seccion-' + seccionId ).find( '.recomendacion-apartado-titulo' ).removeClass( 'd-none' );
      } );
   } );


   $titulos.each( function () {
      $( this ).on( 'click', function () {
         $contenidos.each( function () {
            $( this ).addClass( 'd-none' );
         } );

         $( this ).parent().parent().find( '.recomendacion-seccion-titulo' ).each( function () {
            $( this ).removeClass( 'open' );
         } );

         $( this ).addClass( 'open' );

         let primerApartadoId = $( this ).parent().find( '.recomendacion-apartado-titulo' ).find( 'span' ).data( 'show-id' );
         let $recomendacionApartado = $( '#recomendacion-apartado-' + primerApartadoId );
         $recomendacionApartado.closest( '.recomendacion-apartados-contenido' ).removeClass( 'd-none' );

         $( this ).parent().parent().find( '.recomendacion-apartado-titulo' ).each( function () {
            $( this ).addClass( 'd-none' );
         } );

         $( this ).parent().find( '.recomendacion-apartado-titulo' ).each( function () {
            $( this ).removeClass( 'd-none' );
         } );
      } );
   } );

   $apartados.each( function () {
      $( this ).on( 'click', function () {
         let apartadoId = $( this ).data( 'show-id' );

         $contenidos.each( function () {
            $( this ).addClass( 'd-none' );
         } );

         let $recomendacionApartado = $( '#recomendacion-apartado-' + apartadoId );

         $recomendacionApartado.closest( '.recomendacion-apartados-contenido' ).removeClass( 'd-none' );

         $( [ document.documentElement, document.body ] ).animate( {
            scrollTop: ( $recomendacionApartado.offset().top - 200 )
         }, 2000 );
      } );
   } );

   $mobileTitulos.each( function () {
      $( this ).on( 'click', function () {
         $mobileContenidos.each( function () {
            $( this ).addClass( 'd-none' );
         } );

         console.log($(this));
         if ( $( this ).hasClass( 'open' ) ) {
            $( this ).removeClass( 'open' );
            $( this ).parent().find( '.recomendacion-apartado-titulo' ).each( function () {
               $( this ).addClass( 'd-none' );
            } );
         } else {
            $( this ).parent().parent().find( '.recomendacion-seccion-titulo' ).each( function () {
               $( this ).removeClass( 'open' );
            } );

            $( this ).addClass( 'open' );

            $( this ).parent().parent().find( '.recomendacion-apartado-titulo' ).each( function () {
               $( this ).addClass( 'd-none' );
               $( this ).removeClass( 'open' );
            } );

            $( this ).parent().find( '.recomendacion-apartado-titulo' ).each( function () {
               $( this ).removeClass( 'd-none' );
            } );
         }
      } );
   } );

   $mobileApartados.each( function () {
      $( this ).on( 'click', function () {
         let apartadoId = $( this ).data( 'show-id' );

         let $mobileRecomendacionApartado = $mobileApartadosWrapper.find( '#recomendacion-apartado-' + apartadoId );
         let $mobileRecomendacionApartadoGlobal = $mobileRecomendacionApartado.closest( '.recomendacion-apartados-contenido-global' );
         let $prev = $mobileRecomendacionApartadoGlobal.prev();

         if ( $prev.hasClass( 'open' ) ) {
            $prev.removeClass( 'open' );
            $mobileRecomendacionApartadoGlobal.addClass( 'd-none' );
         } else {
            $mobileContenidos.each( function () {
               $( this ).addClass( 'd-none' );
            } );

            $mobileRecomendacionApartadoGlobal.removeClass( 'd-none' );

            $mobileListaApartados.find( '.recomendacion-apartado-titulo' ).each( function () {
               $( this ).removeClass( 'open' );
            } );

            $prev.addClass( 'open' );

            $( [ document.documentElement, document.body ] ).animate( {
               scrollTop: ( $mobileRecomendacionApartado.offset().top - 130 )
            }, 1000 );
         }
      } );
   } );
} );