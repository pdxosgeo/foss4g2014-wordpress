jQuery(document).ready(function ($) {
	var currentDay = 'd'+$('#day-sort li.current').attr('id').slice(4);
	var sec = 200;
	// schedule sorting and everything
	$('.sorting li').click(function(){
		var sortID = $(this).parent().attr('id');
		if(sortID==='day-sort') {
			switchCurrent($(this));
			$('#track-sort li').removeClass('current');
			$('#track-sort li:first').addClass('current');
			currentDay = 'd'+$(this).attr('id').slice(4);
			$('.sched-block').fadeOut(sec).delay(sec);
			$('#'+currentDay+'t1').fadeIn(sec);
		} else if (sortID==='track-sort') {
			switchCurrent($(this));
			$('.sched-block').fadeOut(sec).delay(sec);
			var trackID = $(this).attr('id').slice(6);
			$('#'+currentDay+'t'+trackID).fadeIn(sec);
		} else {
			// time stuff if we want
		}


		function switchCurrent(t) {
			t.siblings().removeClass('current');
			t.addClass('current');
		}
	});
});