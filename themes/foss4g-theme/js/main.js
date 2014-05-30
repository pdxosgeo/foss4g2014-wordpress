jQuery(document).ready(function ($) {

	// contact form functionality
	$('#contact-form-button').click(function(){
		$('#contact').toggleClass('up');
		$('#contact').toggleClass('down');
		$('#contact-form').slideToggle(200);
		$(this).children('.nav-arrow').toggleClass('hide');
	});

	// page scroll speed on workshop nav click
	var height = $('nav').height() + 20;
	$('a[href*=#type]').click(function() {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
		&& location.hostname == this.hostname) {
			var $target = $(this.hash);
			$target = $target.length && $target
			|| $('[name=' + this.hash.slice(1) +']');
			if ($target.length) {
				var targetOffset = ($target.offset().top)-height;
				$('html,body')
				.animate({scrollTop: targetOffset}, 450);
				return false;
      		}
    	}
  	});

  	// updates header sizing
  	$(window).scroll(function() {
		var scroll = $(window).scrollTop();
		if ( scroll < 140 ) {
			$('.ws-nav').removeClass('fixed');
		}
		else {
			$('.ws-nav').addClass('fixed');
		}
	});

	// border hover for fun
	$('.border div').on('mouseover', function(){
		$('.border div').css('width', 80/9+'%');
		$(this).css('width', '20%');
	});
	$('.border').on('mouseout', function(){
		$('.border div').css('width', '10%');
	});

	// map geojson
	var geojson = [
		{
			type: 'Feature',
		    geometry: {
		        type: 'Point',
		        coordinates: [-122.663247, 45.528419]
		    },
		    properties: {
		        title: 'Oregon Convention Center',
		        description: '<strong>Address:</strong> 777 NE Martin Luther King Jr Blvd, Portland, OR 97232<br><a href="https://2014.foss4g.org/attending/accommodations/">Accommodations</a>',
		        type: 'main',
		        'marker-size': 'large',
		        'marker-color': '#4682B4',
		        'marker-symbol': 'star'
		    }
		},
                {
                        type: 'Feature',
                    geometry: {
                        type: 'Point',
                        coordinates: [-122.68584, 45.51836]
                    },  
                    properties: {
                        title: 'Eliot Center',
                        description: 'Code Sprint will be held here.<br><a href="https://2014.foss4g.org/schedule/code-sprint/">Details</a>',
                        type: 'main',
                        'marker-size': 'medium',
                        'marker-color': '#4682B4',
                        'marker-symbol': 'star'
                    }
                },
		{
			type: 'Feature',
		    geometry: {
		        type: 'Point',
		        coordinates: [-122.68462,45.51182],
		    },
		    properties: {
		        title: 'Portland State University',
		        description: 'Workshops will be held here.<br><a href="https://2014.foss4g.org/schedule/workshops/">Schedule</a>',
		        type: 'main',
		        'marker-size': 'medium',
		        'marker-color': '#4682B4',
		        'marker-symbol': 'star'
		    }
		},
		{
			type: 'Feature',
		    geometry: {
		        type: 'Point',
		        coordinates: [-122.655558, 45.530711]
		    },
		    properties: {
		        title: 'Double Tree by Hilton Portland',
		        description: '<strong>Address:</strong> 1000 NE Multnomah Street, Portland, Oregon, 97232, USA<br><a href="http://doubletree3.hilton.com/en/hotels/oregon/doubletree-by-hilton-hotel-portland-RLLC-DT/index.html">Book Now</a>',
		        type: 'hotel',
		        'marker-size': 'large',
		        'marker-color': '#E32028',
		        'marker-symbol': 'lodging'
		    }
		},
		{
			type: 'Feature',
		    geometry: {
		        type: 'Point',
		        coordinates: [-122.540932, 45.568492]
		    },
		    properties: {
		        title: 'Staybridge Suites Portland Airport Hotel',
		        description: '<strong>Address:</strong> 11936 NE Glenn Widing Drive Portland, Oregon 97220, USA<br><a href="http://www.ihg.com/staybridge/hotels/us/en/portland/pdxgw/hoteldetail">Book Now</a>',
		        type: 'hotel',
		        'marker-size': 'large',
		        'marker-color': '#E32028',
		        'marker-symbol': 'lodging'
		    }
		},
	];

	//map on homepage
	var map = L.mapbox.map('map', 'foss4g2014.hjbf0lfe');
    var poi = L.mapbox.featureLayer().addTo(map);
    poi.setGeoJSON(geojson);
    map.fitBounds(poi.getBounds());
    map.scrollWheelZoom.disable();

    var mapNav = document.getElementById('map-nav');
    poi.eachLayer(function(marker) {
	  var link = mapNav.appendChild(document.createElement('li'));
	  link.className = 'item ';
	  link.className = marker.feature.properties.type;
	  link.href = '#';

	  // Populate content from each markers object.
	  link.innerHTML = marker.feature.properties.title;
	  link.onclick = function() {
	    if (/active/.test(this.className)) {
	      this.className = this.className.replace(/active/, '').replace(/\s\s*$/, '');
	    } else {
	      var siblings = mapNav.getElementsByTagName('li');
	      for (var i = 0; i < siblings.length; i++) {
	        siblings[i].className = siblings[i].className
	          .replace(/active/, '').replace(/\s\s*$/, '');
	      };
	      this.className += ' active';
	      map.panTo(marker.getLatLng());
	      marker.openPopup();
	    }
	    return false;
	  };
	});

	var coordBox = document.getElementById('coordinates');
	map.on('move', function() {
		coordBox.innerHTML = map.getCenter();
	});


    
});
