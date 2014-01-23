function init() {

	// contact form functionality
	$('#contact-form-button').click(function(){
		$('#contact-form').slideToggle(200);
		$(this).children('.nav-arrow').toggleClass('hide');
	});

}


window.onLoad = init();