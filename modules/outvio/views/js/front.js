/**
 * 2019 Outvio
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 *  @author    Eugene Zubkov <eugene.zubkov@zlabsolutions.com>
 *  @copyright 2020 Outvio
 *  @license   https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License v3.0
 *  International Property of Outvio
 */

var outv_lang_pac = '';

function readAjaxFields() {
  if ($('.outvio-lang-pac').length > 0) {
    var raw = $('.outvio-lang-pac').val();
    var json = decodeURIComponent(raw);
    outv_lang_pac = JSON.parse(json);
  }
}

function sortLocations(locs) {
  locs.sort((a, b) => (a.zip < b.zip) ? 1 : -1);
  locs.sort((a, b) => (a.city > b.city) ? 1 : -1);
  return locs;
}

function findMarker(map, lat, lng, id_carrier) {
  for (var i = 0; i < locs[id_carrier][0].length; i++) {
    if (locs[id_carrier][0][i].position.lat() == lat) {
      if (locs[id_carrier][0][i].position.lng() == lng) {
        if (outvio_use_map) map.setZoom(17);
        new google.maps.event.trigger(locs[id_carrier][0][i], 'click');
      }
    }
  }
}


function getIconUrl(courier) {
  var domain_url_shop = $('#domain_url_shop').val();
  var icon = domain_url_shop + "/modules/outvio/views/img/pin_parcellocker.svg";
  //console.log(courier);
  switch (courier) {
    case 'DPD':
    case 'DPD_PORTUGAL':
    case 'SMARTPOST':
    case 'SMARTPOST_NEW':
    case 'OMNIVA':
    case 'PUBLICCORREOS':
    case 'CORREOS':
    case 'DHL':
    case 'GLSNEW':
    case 'GLS':
    case 'MRW':
    case 'NACEX':
    case 'SENDING':
    case 'SEUR':
    case 'SEUR_ATLAS':
    case 'UPS':
      icon = domain_url_shop + "/modules/outvio/views/img/" + courier + "pin.svg";
      break;
    default:
      icon = domain_url_shop + "/modules/outvio/views/img/pin_parcellocker.svg";
      break;
  }
  return icon;
}

function getIconUrlBasedOnMarker(marker, zoomIcon) {
  //
  var domain_url_shop = $('#domain_url_shop').val();
  var baseName = zoomIcon ? "zoom_marker1" : "pin_parcellocker";
  if (marker.icon.indexOf(baseName) > -1) {
    return zoomIcon
      ? domain_url_shop + "/modules/outvio/views/img/zoom_marker1.png"
      : domain_url_shop + "/modules/outvio/views/img/pin_parcellocker.svg";
  } else {
    return zoomIcon
      ? marker.icon.replace('pin.svg', 'zoom.png')
      : marker.icon.replace('zoom.png', 'pin.svg');
  }
}
function getCarrierId(){
  return $('.delivery-option .custom-radio input:checked').length>0
      ? $('.delivery-option .custom-radio input:checked').val()
      : $('.delivery-option input:checked').val();
}

var map_zoom = 13;
if (window.innerWidth < 500) {
  map_zoom = 12;
}
var locations = [];
var locs = [];
var current_marker = false;
var current_center_marker = false;

var mapInstancesPool = {
  pool: [],
  used: 0,
  getInstance: function (options, id_carrier) {
    if (mapInstancesPool.used >= mapInstancesPool.pool.length) {
      mapInstancesPool.used++;
      mapInstancesPool.pool.push(mapInstancesPool.createNewInstance(options, id_carrier));
    } else {
      mapInstancesPool.used++;
    }
    return mapInstancesPool.pool[mapInstancesPool.used - 1];
  },
  mapCenterSelect: function (result, id_carrier) {
    var map = {};
    var map_id = 'map_' + id_carrier;
    var map_found = false;
    console.log(id_carrier);
    for (var i = 0; i < mapInstancesPool.pool.length; i++) {
      var obj = mapInstancesPool.pool[i];
      if (obj.div.id === map_id) {
        map = obj.map;
        map_found = true;
      }
    }
    if (map_found) {
      map.setCenter(result[0].geometry.location);
      current_center_marker.setMap(null);
      findMarker(map, result[0].geometry.location.lat, result[0].geometry.location.lng, id_carrier);
    }
  },
  
  mapCenter: function (result, id_carrier) {
    var map = {};
    var map_id = 'map_' + id_carrier;
    var map_found = false;
    for (var i = 0; i < mapInstancesPool.pool.length; i++) {
      var obj = mapInstancesPool.pool[i];
      if (obj.div.id === map_id) {
        map = obj.map;
        map_found = true;
      }
    }
    if (map_found) {
      map.setCenter(result[0].geometry.location);
      marker_center = new google.maps.Marker({
        map: map,
        position: result[0].geometry.location
      });
      current_center_marker.setMap(null);
      current_center_marker = marker_center;
    }
  },
  redrawMap: function (options, id_carrier) {
    var map = {};
    var map_id = 'map_' + id_carrier;
    var map_found = false;
    for (var i = 0; i < mapInstancesPool.pool.length; i++) {
      var obj = mapInstancesPool.pool[i];
      if (obj.div.id == map_id) {
        map = obj.map;
        map_found = true;
      }
    }
    if (map_found) {
      for (var i = 0; i < locs[id_carrier][0].length; i++) {
        locs[id_carrier][0][i].setMap(null);
      }
      
      var lat = [];
      var lng = [];
      
      for (var i = 0; i < locations.length; i++) {
        if (locations[i].lat > 1 || locations[i].lng > 1) {
          lat.push(locations[i].lat);
          lng.push(locations[i].lng);
        }
      }
      var lat_min = Math.min.apply(null, lat);
      var lat_max = Math.max.apply(null, lat);
      var lng_min = Math.min.apply(null, lng);
      var lng_max = Math.max.apply(null, lng);
      var center_lat = (lat_max + lat_min) / 2;
      var center_lng = (lng_max + lng_min) / 2;
      var center = {
        lat: center_lat,
        lng: center_lng
      };
      map.setCenter(center);
      
      var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      var infoWin = new google.maps.InfoWindow();

      locs[id_carrier][0] = [];
      var markers = locations.map(function (location, i) {
        var marker = new google.maps.Marker({
          icon: getIconUrl(location.courier),
          position: location,
          animation: google.maps.Animation.DROP,
          draggable: false,
          map: map,
          zIndex: 0
        });
        
        google.maps.event.addListener(marker, 'click', function (evt) {
          console.log('marker data V3', marker, evt);
          var clatlng = {
            lat: location.lat,
            lng: location.lng
          };
          map.setCenter(clatlng);
          marker.setIcon(getIconUrlBasedOnMarker(marker, true));
          $('#points-list_' + id_carrier).find('option[value="' + location.point_id + '"]').prop('selected', true).trigger("chosen:updated");
          var formatted_address = location.address + ', ' + location.city + ', ' + location.zip;
          var workingHours = location.wh ? "<p>" + location.wh + "</p>" : "";
          infoWin.setContent('<h3>' + location.point_name + '</h3>' + '<p>' + '<p>' + formatted_address + '</p>' + workingHours + '<input type="hidden" class="select_pickup" value="" data-info="' + location.json_info + '"  \>');
          Outv.ajaxSavePoint(location.json_info);
          infoWin.open(map, marker);
          setTimeout(function () {
            Outv.onResizeMapInfowin();
          }, 100);
          $('button[name="confirmDeliveryOption"]').prop('disabled', false);
          current_marker.setIcon(getIconUrlBasedOnMarker(marker, false));
          current_marker = marker;
        });
        locs[id_carrier][0].push(marker);
        
        return marker;
      });
      locs[id_carrier][1] = markers;
    }
  },
  
  reset: function () {
    mapInstancesPool.used = 0;
  },

  initPointsList: function () {
    var id_carrier = getCarrierId();
    id_carrier = id_carrier.replace(',', '');
    setTimeout(function () {
      //sorted_locations
      var sl = sortLocations(locations);
      for (var i = 0; i < sl.length; i++) {
        $('#points-list_' + id_carrier).append('<option value="' + sl[i].point_id + '" data-lat="' + sl[i].lat + '" data-lng="' + sl[i].lng + '">' + sl[i].city + ', ' + sl[i].point_name + ', ' + sl[i].zip + '</option>');
      }
      $('.points-list select:visible').chosen({width: '100%'});
    }, 100);
    //bind points list to map
    $(document).on('change', '#points-list_' + id_carrier, function () {
      //this.value
      
      var result = [];
      $option = $(this).find('option:selected');
      var geometry = {
        geometry: {
          location: {
            lat: $option.data('lat'),
            lng: $option.data('lng'),
          },
        },
      }
      result.push(geometry);
      if (this.value !== 0) {
        $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
        var res = mapInstancesPool.mapCenterSelect(result, id_carrier);
      } else {
        $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
      }
    });
    // Chosen touch support.
    $(document).on('touchstart', '.chosen-container', function (e) {
      e.stopPropagation();
      e.preventDefault();
      // Trigger the mousedown event.
      $(this).trigger('mousedown');
      $(this).trigger('click');
    });
  },
  
  createNewInstance: function (options, id_carrier) {
    var points_list = '<div class="points-list"><select id="points-list_' + id_carrier + '" name="points-list" selected data-placeholder="Please select pickup point"><option value="0">Please select pickup point</option></select></div>';
    $('input#delivery_option_' + id_carrier).closest('.delivery-option').next().append(points_list + '<div id="map_' + id_carrier + '" class="gmap"></div>');
    
    if (!outvio_use_map) $('.gmap').hide();
    var div = document.getElementById('map_' + id_carrier);
    var map = new google.maps.Map(div, options);
    mapInstancesPool.initPointsList();
    var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var infoWin = new google.maps.InfoWindow();
    /*find center*/
    var zip = $('#zip').val();
    var city = $('#city').val();
    var cc = $('#cc').val();
    try {
      Outv.ajaxGetLocations(`${city}, ${zip}, ${cc}`, map);
    } catch (err) {
      console.log(err);
    }
    /*end find center*/
    /*draw markers*/
    var domain_url_shop = $('#domain_url_shop').val();
    locs[id_carrier][0] = [];
    var markers = locations.map(function (location, i) {
      //console.log('location !!', location);
      var marker = new google.maps.Marker({
        icon: getIconUrl(location.courier),
        position: location,
        animation: google.maps.Animation.DROP,
        draggable: false,
        map: map,
        zIndex: 0
      });
      
      google.maps.event.addListener(marker, 'click', function (evt) {
        var clatlng = {
          lat: location.lat,
          lng: location.lng
          
        };
        //marker.scale = 2.1;
        console.log('marker data 2.1', marker, evt);
        marker.setIcon(getIconUrlBasedOnMarker(marker, true));
        map.setCenter(clatlng);
        //gm-style-iw-a
        
        var formatted_address = location.address + ', ' + location.city;
        var workingHours = location.wh ? "<p>" + location.wh + "</p>" : "";
        infoWin.setContent('<h3>' + location.point_name + '</h3>' + '<p>' + '<p>' + formatted_address + '</p>' + workingHours + '<input type="hidden" class="select_pickup" value="" data-info="' + location.json_info + '"  \>');
        Outv.ajaxSavePoint(location.json_info);
        
        infoWin.open(map, marker);
        $('#points-list_' + id_carrier).find('option[value="' + location.point_id + '"]').prop('selected', true).trigger("chosen:updated");
        setTimeout(function () {
          Outv.onResizeMapInfowin();
        }, 100);
        $('button[name="confirmDeliveryOption"]').prop('disabled', false);
        if (current_marker) {
          current_marker.setIcon(getIconUrlBasedOnMarker(current_marker, false));
        }
        
        current_marker = marker;
      })
      locs[id_carrier][0].push(marker);
      
      return marker;
    });
    
    locs[id_carrier][1] = markers;
    
    var markerCluster = new markerClusterer.MarkerClusterer({map, markers});
    
    /*end draw markers*/
    return {
      map: map,
      div: div
    }
  }
}

$(document).ready(function () {
  var paymentStep = document.getElementById("checkout-payment-step");
  var notPaymentStep = !paymentStep || paymentStep.classList.contains("-unreachable");
  if ($('#checkout .delivery-option').length > 0 && notPaymentStep) {
    Outv.init();
  }
});

var Outv = {
  init: function () {
    $(document).on('click', '.gm-style-iw button', function () {
      Outv.ajaxClearPoint();
      current_marker.setIcon(getIconUrlBasedOnMarker(current_marker, false));
      $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
    });

    var radioElem = $('.delivery-option .custom-radio input').length>0
        ? $('.delivery-option .custom-radio input')
        : $('.delivery-option input');
    radioElem.click(function () {
      Outv.getCarrierPoints($(this));
      $('.gm-style-iw button').click();
      
    });

    var radioElemChecked = $('.delivery-option .custom-radio input:checked').length>0
        ? $('.delivery-option .custom-radio input:checked')
        : $('.delivery-option input:checked');
    Outv.getCarrierPoints(radioElemChecked);
    Outv.ajaxClearPoint();
  },
  
  onResizeMapInfowin: function () {
    var map_width = $('.gmap:visible').width();
    map_width = map_width - 12;
    $('.gm-style-iw').css({'display': 'block', 'max-width': map_width});
  },

  getCarrierPoints: function ($el) {
    var id_carrier = $el.val();
    id_carrier = id_carrier.replace(',', '');
    if ($('#map_' + id_carrier).length == 0) {
      Outv.ajaxGetCarrierPoints(id_carrier);
    } else {
      $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
    }
  },
  
  checkContinue: function (el) {
    if ($(el).closest('.delivery-option').next().find('#map').length > 0) {
      Outv.ajaxClearPoint();
      $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
      //$('button[name="confirmDeliveryOption"]').prop('disabled', true);
    } else {
      $('button[name="confirmDeliveryOption"]').prop('disabled', false);
    }
  },
  
  initMap: function (points, id_carrier) {
    var points_length = points.length;
    if (points_length != 0) {
      Outv.drawMap(points, id_carrier);
    }
  },
  
  redrawMap: function (points, id_carrier) {
    var points_length = points.length;
    if (points_length != 0) {
      locations = [];
      for (var i = 0; i < points.length; i++) {
        locations.push({
          lat: parseFloat(points[i].latitude),
          lng: parseFloat(points[i].longitude),
          point_name: points[i].name,
          point_id: points[i].id,
          json_info: points[i].json_info,
          address: points[i].address,
          city: points[i].city,
          wh: points[i].workingHours,
          phone: points[i].phone,
          zip: points[i].postcode,
          courier: points[i].courier
        });
      }
      
      if (locations.length > 0) {
        $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
        var res = mapInstancesPool.redrawMap({
          zoom: map_zoom,
          scaleControl: false,
          streetViewControl: false,
          disableDefaultUI: true,
          panControl: false,
          mapTypeControl: false,
          overviewMapControl: false,
          zoomControl: false,
        }, id_carrier);
      } else {
        $('button[name="confirmDeliveryOption"]').prop('disabled', false);
      }
    } else {
      alert('No pickup points found');
    }
  },
  
  mapCenter: function (result, id_carrier) {
    var result_length = result.length;
    if (result_length != 0) {
      if (result_length > 0) {
        $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
        var res = mapInstancesPool.mapCenter(result, id_carrier);
        $('#points-list_' + id_carrier).find('option[value="0"]').prop('selected', true).trigger("chosen:updated");
      } else {
        $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
      }
    } else {
      alert('Nothing found');
    }
  },
  
  drawMap: function (points, id_carrier) {
    locations = [];
    locs[id_carrier] = [];
    for (var i = 0; i < points.length; i++) {
      locations.push({
        lat: parseFloat(points[i].latitude),
        lng: parseFloat(points[i].longitude),
        point_name: points[i].name,
        point_id: points[i].id,
        json_info: points[i].json_info,
        address: points[i].address,
        city: points[i].city,
        wh: points[i].workingHours,
        phone: points[i].phone,
        zip: points[i].postcode,
        courier: points[i].courier
      });
    }
    if (locations.length > 0) {
      $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
      var res = mapInstancesPool.getInstance({
        zoom: map_zoom,
        scaleControl: false,
        streetViewControl: false,
        disableDefaultUI: true,
        panControl: false,
        mapTypeControl: false,
        overviewMapControl: false,
        zoomControl: false,
        mapId: id_carrier,
      }, id_carrier);
    } else {
      $('button[name="confirmDeliveryOption"]').prop('disabled', false);
    }
  },
  
  ajaxUrl: function () {
    return $('#outvio_ajax_url').val();
  },
  
  ajaxGetCarrierSearchPoints: function (id_carrier, search) {
    $.ajax({
      type: 'POST',
      url: Outv.ajaxUrl() + '?action=getCarrierSearchPoints&outv_token=' + prestashop.static_token,
      data: {'id_carrier': id_carrier, 'search_text': search},
      
      beforeSend: function () {
        $("body").toggleClass("wait");
        $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
      },
      success: function (response) {
        var IS_JSON = true;
        try {
          var results = JSON.parse(response);
          if (results.length == 0) {
            $('button[name="confirmDeliveryOption"]').prop('disabled', false);
          }
          Outv.mapCenter(results, id_carrier);
        } catch (err) {
          IS_JSON = false;
          console.log(err);
        }
      },
      complete: function () {
        $("body").toggleClass("wait");
      },
    });
  },

  ajaxGetLocations: function (address, map) {
    $.ajax({
      type: 'POST',
      url: Outv.ajaxUrl() + '?action=getLocations&outv_token=' + prestashop.static_token,
      data: {address},

      success: function (response) {
        var marker_center = 0;
        try {
          var results = JSON.parse(response);
          map.setCenter(results.geometry.location);
          marker_center = new google.maps.Marker({
            map: map,
            position: results.geometry.location
          });
          current_center_marker = marker_center;
        } catch (err) {
          console.log(err);
        }
      },
    });
  },
  
  ajaxGetCarrierPoints: function (id_carrier) {
    if ($('#map_' + id_carrier).length > 0) {
      return true;
    }
    $.ajax({
      type: 'POST',
      url: Outv.ajaxUrl() + '?action=getCarrierPoints&outv_token=' + prestashop.static_token,
      data: {'id_carrier': id_carrier},
      
      beforeSend: function () {
        $("body").toggleClass("wait");
        $('button[name="confirmDeliveryOption"]').attr('disabled', 'disabled');
        $('input#delivery_option_' + id_carrier).closest('.delivery-option').next().append('<div class="outvio-preloader" id="map-preloader-' + id_carrier + '"><svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="160px" height="20px" viewBox="0 0 128 16" xml:space="preserve" style="width: 100%;"><rect x="0" y="0" width="100%" height="100%" fill="none"></rect><path fill="#b3e0df" fill-opacity="0.42" d="M6.4,4.8A3.2,3.2,0,1,1,3.2,8,3.2,3.2,0,0,1,6.4,4.8Zm12.8,0A3.2,3.2,0,1,1,16,8,3.2,3.2,0,0,1,19.2,4.8ZM32,4.8A3.2,3.2,0,1,1,28.8,8,3.2,3.2,0,0,1,32,4.8Zm12.8,0A3.2,3.2,0,1,1,41.6,8,3.2,3.2,0,0,1,44.8,4.8Zm12.8,0A3.2,3.2,0,1,1,54.4,8,3.2,3.2,0,0,1,57.6,4.8Zm12.8,0A3.2,3.2,0,1,1,67.2,8,3.2,3.2,0,0,1,70.4,4.8Zm12.8,0A3.2,3.2,0,1,1,80,8,3.2,3.2,0,0,1,83.2,4.8ZM96,4.8A3.2,3.2,0,1,1,92.8,8,3.2,3.2,0,0,1,96,4.8Zm12.8,0A3.2,3.2,0,1,1,105.6,8,3.2,3.2,0,0,1,108.8,4.8Zm12.8,0A3.2,3.2,0,1,1,118.4,8,3.2,3.2,0,0,1,121.6,4.8Z"></path><g transform="translate(178 0)"><path fill="#4bb4b3" fill-opacity="1" d="M-42.7,3.84A4.16,4.16,0,0,1-38.54,8a4.16,4.16,0,0,1-4.16,4.16A4.16,4.16,0,0,1-46.86,8,4.16,4.16,0,0,1-42.7,3.84Zm12.8-.64A4.8,4.8,0,0,1-25.1,8a4.8,4.8,0,0,1-4.8,4.8A4.8,4.8,0,0,1-34.7,8,4.8,4.8,0,0,1-29.9,3.2Zm12.8-.64A5.44,5.44,0,0,1-11.66,8a5.44,5.44,0,0,1-5.44,5.44A5.44,5.44,0,0,1-22.54,8,5.44,5.44,0,0,1-17.1,2.56Z"></path><animateTransform attributeName="transform" type="translate" values="23 0;36 0;49 0;62 0;74.5 0;87.5 0;100 0;113 0;125.5 0;138.5 0;151.5 0;164.5 0;178 0" calcMode="discrete" dur="1170ms" repeatCount="indefinite"></animateTransform></g></svg></div>');
      },
      success: function (response) {
        var IS_JSON = true;
        try {
          
          var results = JSON.parse(response);
          if (results.length == 0) {
            $('button[name="confirmDeliveryOption"]').prop('disabled', false);
          }
          Outv.initMap(results, id_carrier);
        } catch (err) {
          console.error('IS JSON false. Error', err);
          $('button[name="confirmDeliveryOption"]').prop('disabled', false);
        }
      },
      complete: function () {
        $("#map-preloader-" + id_carrier).remove();
        $("body").toggleClass("wait");
      },
    }).fail(function (){
      console.log('FAILED REQUEST');
      $('button[name="confirmDeliveryOption"]').prop('disabled', false);
    });;
  },
  ajaxSavePoint: function (point_info) {
    $.ajax({
      type: 'POST',
      url: Outv.ajaxUrl() + '?action=saveSelectedPoint&token=' + prestashop.static_token,
      data: {'point_info': point_info},
      
      beforeSend: function () {
        $("body").toggleClass("wait");
      },
      success: function (response) {
        //console.log(response);
      },
      complete: function () {
        $("body").toggleClass("wait");
      },
    });
  },
  ajaxClearPoint: function () {
    var paymentStep = document.getElementById("checkout-payment-step");
    var notPaymentStep = !paymentStep || paymentStep.classList.contains("-unreachable");
    if (notPaymentStep) {
      $.ajax({
        type: 'POST',
        url: Outv.ajaxUrl() + '?action=deleteSelectedPoint&token=' + prestashop.static_token,
        beforeSend: function () {
          $("body").toggleClass("wait");
        },
        success: function (response) {
          //console.log(response);
        },
        complete: function () {
          $("body").toggleClass("wait");
        },
      });
    }
  },
}

tools = {
  getParameterByName: function (name, url) {
    if (!url) {
      url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  },
}
