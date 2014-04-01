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

	//map on homepage
	var map = L.mapbox.map('map', 'foss4g2014.hjbf0lfe')
    .setView([45.528419, -122.663247], 9);
    L.mapbox.featureLayer({
	    type: 'Feature',
	    geometry: {
	        type: 'Point',
	        coordinates: [-122.663247, 45.528419]
	    },
	    properties: {
	        title: 'Oregon Convention Center',
	        description: '<strong>Address:</strong>777 NE Martin Luther King Jr Blvd, Portland, OR 97232<br><a href="https://2014.foss4g.org/attending/accommodations/">Accommodations</a>',
	        // one can customize markers by adding simplestyle properties
	        // http://mapbox.com/developers/simplestyle/
	        'marker-size': 'large',
	        'marker-color': '#4682B4'
	    }
	}).addTo(map);
    map.scrollWheelZoom.disable();
    
});