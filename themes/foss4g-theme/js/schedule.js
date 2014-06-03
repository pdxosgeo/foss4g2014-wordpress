jQuery(document).ready(function ($) {
  var currentDay = 'd'+$('#day-sort li.current').attr('id').slice(4);
  var sec = 200;
  // schedule sorting and everything
  $('.sorting li').click(function(){
    var sortID = $(this).parent().attr('id');
    if(sortID==='day-sort') {
      switchCurrent($(this));
      $('#session-sort li').removeClass('current');
      $('#session-sort li:first').addClass('current');
      currentDay = 'd'+$(this).attr('id').slice(4);
      $('.sched-block').fadeOut(sec).delay(sec);
      $('#'+currentDay+'s1').fadeIn(sec);
    } else if (sortID==='session-sort') {
      switchCurrent($(this));
      $('.sched-block').fadeOut(sec).delay(sec);
      var sessionID = $(this).attr('id').slice(8);
      $('#'+currentDay+'s'+sessionID).fadeIn(sec);
    } else {
      // time stuff if we want
    }

    function switchCurrent(t) {
      t.siblings().removeClass('current');
      t.addClass('current');
    }
  });
  jQuery('#session-1').click();
});