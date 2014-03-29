jQuery(document).ready(function ($) {

	// contact form functionality
	$('#contact-form-button').click(function(){
		$('#contact').toggleClass('up');
		$('#contact').toggleClass('down');
		$('#contact-form').slideToggle(200);
		$(this).children('.nav-arrow').toggleClass('hide');
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