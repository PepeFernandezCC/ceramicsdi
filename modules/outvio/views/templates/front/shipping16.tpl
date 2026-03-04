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
<div class="outvio-preloader">
	<svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="160px" height="20px" viewBox="0 0 128 16" xml:space="preserve" style="width: 100%;"><rect x="0" y="0" width="100%" height="100%" fill="none"></rect><path fill="#b3e0df" fill-opacity="0.42" d="M6.4,4.8A3.2,3.2,0,1,1,3.2,8,3.2,3.2,0,0,1,6.4,4.8Zm12.8,0A3.2,3.2,0,1,1,16,8,3.2,3.2,0,0,1,19.2,4.8ZM32,4.8A3.2,3.2,0,1,1,28.8,8,3.2,3.2,0,0,1,32,4.8Zm12.8,0A3.2,3.2,0,1,1,41.6,8,3.2,3.2,0,0,1,44.8,4.8Zm12.8,0A3.2,3.2,0,1,1,54.4,8,3.2,3.2,0,0,1,57.6,4.8Zm12.8,0A3.2,3.2,0,1,1,67.2,8,3.2,3.2,0,0,1,70.4,4.8Zm12.8,0A3.2,3.2,0,1,1,80,8,3.2,3.2,0,0,1,83.2,4.8ZM96,4.8A3.2,3.2,0,1,1,92.8,8,3.2,3.2,0,0,1,96,4.8Zm12.8,0A3.2,3.2,0,1,1,105.6,8,3.2,3.2,0,0,1,108.8,4.8Zm12.8,0A3.2,3.2,0,1,1,118.4,8,3.2,3.2,0,0,1,121.6,4.8Z"></path><g transform="translate(178 0)"><path fill="#4bb4b3" fill-opacity="1" d="M-42.7,3.84A4.16,4.16,0,0,1-38.54,8a4.16,4.16,0,0,1-4.16,4.16A4.16,4.16,0,0,1-46.86,8,4.16,4.16,0,0,1-42.7,3.84Zm12.8-.64A4.8,4.8,0,0,1-25.1,8a4.8,4.8,0,0,1-4.8,4.8A4.8,4.8,0,0,1-34.7,8,4.8,4.8,0,0,1-29.9,3.2Zm12.8-.64A5.44,5.44,0,0,1-11.66,8a5.44,5.44,0,0,1-5.44,5.44A5.44,5.44,0,0,1-22.54,8,5.44,5.44,0,0,1-17.1,2.56Z"></path><animateTransform attributeName="transform" type="translate" values="23 0;36 0;49 0;62 0;74.5 0;87.5 0;100 0;113 0;125.5 0;138.5 0;151.5 0;164.5 0;178 0" calcMode="discrete" dur="1170ms" repeatCount="indefinite"></animateTransform></g></svg>
</div>

  <!--START MAP -->

{if $outvio_carrier_id neq 0}
  <script>
	if ($('#map > div').length == 0) {
	  	var delivery_opts_init = setInterval(function() {
	  		if ($('.delivery_options').length > 0) {
	  			clearInterval(delivery_opts_init);
				//console.log('1.6     '+ $('.delivery_option_radio input[value="4,"]').length);
				if ($('#map > div').length == 0) {
					var outvio_carrier_id = $('#outvio_carrier_id').val();
					$zblock = $('.delivery_option_radio input[value="{$outvio_carrier_id|escape:'htmlall':'UTF-8'},"]').closest('.delivery_option');
					$('#map > div').html();
					$zblock.append('<div class="map-container "><div id="map"></div></div>');
					Outv.checkContinue($('.delivery_option_radio input:checked'));

		        	var script = document.createElement('script'); 
		        	script.src = "https://maps.googleapis.com/maps/api/js?key={$google_maps_api_key|escape:'htmlall':'UTF-8'}&callback=initMap"; 
		        	script.async = true;
		        	script.defer = true;
		        	document.head.appendChild(script);
		        	clearInterval(delivery_opts_init);
	        	}
	  		}
	  	}, 1500);
	}
  </script>	
  <script>
  // Initialize and add the map

	var locations = [
	{foreach from=$points item=point}
		{* Keep only lat & lng > 0 *}
		{literal}{{/literal}lat: {$point->latitude|floatval}, lng: {$point->longitude|floatval}, point_name: '{$point->name|escape:'htmlall':'UTF-8'}', point_id: '{$point->id|escape:'htmlall':'UTF-8'}', json_info: '{$point->json_info|escape:'htmlall':'UTF-8'}', address: '{$point->address|escape:'htmlall':'UTF-8'}', wh: '{$point->workingHours|escape:'htmlall':'UTF-8'}', phone: '{$point->phone|escape:'htmlall':'UTF-8'}'},

		{*{literal}{{/literal}lat: {$point->latitude|floatval}, lng: {$point->longitude|floatval}, point_name: '{$point->name|escape:'htmlall':'UTF-8'}'},*}
	{/foreach}

	];
	function initMap()  {literal}{{/literal}
		/*
		if ($('#map > div').length == 1) {
			return true;
		}
		*/
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

		var map_zoom = 14;
		if (window.innerWidth < 500) {
			map_zoom = 13;
		}
		var map = new google.maps.Map(document.getElementById('map'),  {literal}{{/literal}zoom: map_zoom, mapId: 'main_map' });
        map.setCenter(center);
		var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		var infoWin = new google.maps.InfoWindow();
		
		var markers = locations.map(function(location, i) {literal}{{/literal}
			var marker = new google.maps.Marker({literal}{{/literal}
		  		icon: "{$base_dir}/modules/outvio/views/img/marker.png",
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
			    var workingHours = location.wh ? "<p>" + location.wh + "</p>" : "";
                infoWin.setContent('<br><h3>'+location.point_name + '</h3><p>'+formatted_address+'</p>'+ workingHours + '<input type="hidden" class="select_pickup" value="" data-info="'+ location.json_info+'"  \>');
			    //console.log(formatted_address);
			    Outv.ajaxSavePoint(location.json_info);
			    infoWin.open(map, marker);
			    $('button[name="processCarrier"]').prop('disabled', false);
			    $('#order-opc #cgv').prop('disabled', false);
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
		      infowindow.setContent(locations[i][0]);
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
  <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
  {/if}
<!--END MAP -->
