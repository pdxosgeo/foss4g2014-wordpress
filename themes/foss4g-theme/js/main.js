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

});
