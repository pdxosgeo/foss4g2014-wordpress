function init() {

	// set image on homepage of portland
	var homeImages = [
			'portland-home-2.jpg',
			'portland-home-3.jpg',
			'portland-home-4.jpg',
			'portland-home-5.jpg',
			'portland-home-6.jpg',
			'portland-home-7.jpg',
			'portland-home-8.png',
			'portland-home-9.jpg',
			'portland-home-10.jpg',
			'portland-home-11.jpg'
		],
		image = Math.floor(Math.random() * homeImages.length);
	$('#portland-home-image').css('background-image', 'url(wp-content/themes/foss4g-theme/img/portland-home/' + homeImages[image] + ')');

	// contact form functionality
	$('#contact-form-button').click(function(){
		$('#contact').toggleClass('up');
		$('#contact').toggleClass('down');
		$('#contact-form').slideToggle(200);
		$(this).children('.nav-arrow').toggleClass('hide');
	});

	// jquery marquee
	var sponsors = $('.marquee > a').length;
	var speed = sponsors * 1400;
	console.log(sponsors);
	$('.marquee').marquee({
		speed: 12000,
		gap: 0,
		delayBeforeStart: 15,
		direction: 'right',
		duplicated: true,
		pauseOnHover: true
	});

	//map on homepage
	var map = L.mapbox.map('map', 'examples.map-9ijuk24y')
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
	        'marker-color': 'steelblue'
	    }
	}).addTo(map);

}


window.onLoad = init();