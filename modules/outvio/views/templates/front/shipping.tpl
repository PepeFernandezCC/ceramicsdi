{*
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Outvio OU
 *  @copyright 2017-2010 Outvio OU
 *  @license   Outvio OU
 *}

<input type="hidden" id="outvio_ajax_url" value="{$outvio_ajax_url|escape:'htmlall':'UTF-8'}" />
<input type="hidden" id="outvio_carrier_id" value="{$outvio_carrier_id|escape:'htmlall':'UTF-8'}" />

  <!--START MAP -->
  	{if $oversion eq '17'}
	<div class="map-container ">
	    <div id="map">

	    </div>
    </div>
    {/if}

  <script>
	{if $oversion eq '161'}
		console.log('1.6 '+ $('.delivery_option_radio input[value="4,"]').length);
		var outvio_carrier_id = $('#outvio_carrier_id').val();
		$zblock = $('.delivery_option_radio input[value="{$outvio_carrier_id|escape:'htmlall':'UTF-8'},"]').closest('.delivery_option');
		$zblock.append('<div class="map-container"><div id="map"></div></div>');
	{/if}	

  // Initialize and add the map

	var locations = [
	{foreach from=$points item=point}
		{* Keep only lat & lng > 0 *}
		{literal}{{/literal}lat: {$point->latitude|floatval}, lng: {$point->longitude|floatval}, point_name: '{$point->name|escape:'htmlall':'UTF-8'}', point_id: '{$point->id|escape:'htmlall':'UTF-8'}', json_info: '{$point->json_info|escape:'htmlall':'UTF-8'}', address: '{$point->address|escape:'htmlall':'UTF-8'}', wh: '{$point->workingHours|escape:'htmlall':'UTF-8'}', phone: '{$point->phone|escape:'htmlall':'UTF-8'}'},

		{*{literal}{{/literal}lat: {$point->latitude|floatval}, lng: {$point->longitude|floatval}, point_name: '{$point->name|escape:'htmlall':'UTF-8'}'},*}
	{/foreach}

	];
	function initMap()  {literal}{{/literal}

		var lat = [];
		var lng = [];

		for (var i = 0 ; i < locations.length; i++) {literal}{{/literal}
			//list_hotspot.push(locations[i]);
			if (locations[i].lat > 1 || locations[i].lng > 1) {
				lat.push(locations[i].lat);
				lng.push(locations[i].lng);
			}

			//lat.push(locations[i].lat);
			//lng.push(locations[i].lng);
		}
		var lat_min = Math.min.apply(null, lat);
		var lat_max = Math.max.apply(null, lat);
		var lng_min = Math.min.apply(null, lng);
		var lng_max = Math.max.apply(null, lng);
		var center_lat = (lat_max + lat_min) / 2;
		var center_lng = (lng_max + lng_min) / 2;
		//console.log(center_lat + '     '+center_lng);
		var center =  {literal}{{/literal}
			lat: center_lat,
			lng: center_lng
		};

		var map_zoom = 13;
		if (window.innerWidth < 500) {
			map_zoom = 12;
		}
		var map = new google.maps.Map(document.getElementById('map'),  {literal}{{/literal}zoom: map_zoom, mapId: 'main_map' });
        map.setCenter(center);
		var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		var infoWin = new google.maps.InfoWindow();
		
		var markers = locations.map(function(location, i) {literal}{{/literal}
			var marker = new google.maps.Marker({literal}{{/literal}
		  		icon: "{$url_shop}/modules/outvio/views/img/marker.png",
		    	position: location,
				draggable: false,
				map: map,
				zIndex: 0
		  	});

			  google.maps.event.addListener(marker, 'click', function(evt) {
			    
			    var clatlng = {
				    lat: location.lat,
				    lng: location.lng
			    };
			    map.setCenter(clatlng);
			    var formatted_address = location.address + ', ' + location.city + ', ' + location.zip;
			    infoWin.setContent('<br><h3>'+location.point_name + '</h3>');
			    var workingHours = location.wh ? "<p>" + location.wh + "</p>" : "";
                infoWin.setContent('<br><h3>'+location.point_name + '</h3><p>'+formatted_address+'</p>'+ workingHours + '<input type="hidden" class="select_pickup" value="" data-info="'+ location.json_info+'"  \>');
			    //console.log(formatted_address);
			    Outv.ajaxSavePoint(location.json_info);
			    infoWin.open(map, marker);
			    $('button[name="confirmDeliveryOption"]').prop('disabled', false);
			  })
		  return marker;
		});
		// Add a marker clusterer to manage the markers.
		var markerCluster = new markerClusterer.MarkerClusterer({map, markers});

		//find center
		/*
		var lat = [];
		var lng = [];
		for (var i = locations.length - 1; i >= 0; i--) {literal}{{/literal}
			lat.push(locations[i].lat);
			lng.push(locations[i].lng);
		}
		
		var lat_min = Math.min(lat);
		var lat_max = Math.max(lat);
		var lng_min = Math.min(lng);
		var lng_max = Math.max(lng);


		/*
		map.setCenter(new google.maps.LatLng(
			((lat_max + lat_min) / 2.0),
			((lng_max + lng_min) / 2.0)
		));
		*/
		/*
		map.fitBounds(new google.maps.LatLngBounds(
			//bottom left
			new google.maps.LatLng(lat_min, lng_min),
			//top right
			new google.maps.LatLng(lat_max, lng_max)
		));
		*/
		/*
		var bounds = new google.maps.LatLngBounds();
		var infowindow = new google.maps.InfoWindow();

		for (i = 0; i < locations.length; i++) {literal}{{/literal}  
		  var marker = new google.maps.Marker({literal}{{/literal}
		    position: new google.maps.LatLng(locations[i][1], locations[i][2]),
		    map: map
		  });

		  //extend the bounds to include each marker's position
		  bounds.extend(marker.position);
		  google.maps.event.addListener(marker, 'click', (function(marker, i) {literal}{{/literal}
		    return function() {literal}{{/literal}
		      //console.log('work');
		      infowindow.setContent('<br>'+locations[i][0]);
		      infowindow.open(map, marker);
		    }
		  })(marker, i));
		}
		map.fitBounds(bounds);

		//(optional) restore the zoom level after the map is done scaling
		var listener = google.maps.event.addListener(map, "idle", function () {
		    map.setZoom(12);
		    google.maps.event.removeListener(listener);
		});
		*/
		//end find center	
	}

  </script>

      <!--Load the API from the specified URL
      * The async attribute allows the browser to render the page while the API loads
      * The key parameter will contain your own API key (which is not needed for this tutorial)
      * The callback parameter executes the initMap() function
      -->

    {if $outvio_use_map eq '1'}
    	<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={$google_maps_api_key|escape:'htmlall':'UTF-8'}&callback=initMap">
        </script>
    {/if}

<!--END MAP -->
