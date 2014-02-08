function init() {

	// set image on homepage of portland
	var homeImages = ['portland-home-1.jpg', 'portland-home-2.jpg'],
		image = Math.floor(Math.random() * homeImages.length);
	$('#portland-home-image').css('background-image', 'url(wp-content/themes/foss4g-theme/img/portland-home/' + homeImages[image] + ')');
	console.log(image);

	// contact form functionality
	$('#contact-form-button').click(function(){
		$('#contact').toggleClass('up');
		$('#contact').toggleClass('down');
		$('#contact-form').slideToggle(200);
		$(this).children('.nav-arrow').toggleClass('hide');
	});



}


window.onLoad = init();