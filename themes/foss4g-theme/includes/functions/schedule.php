<?php

function slot_time_str($start_time,$slot,$length=25){
  $start_time=$start_time+(($slot-1)*60*30);
  $finish_time=$start_time + (60*$length); #25 minutes long
  return(date('H:i', $start_time) . ' - ' . date('H:i', $finish_time));
}

function time_for_presentation($day, $session, $slot,$length=25){
  # third day starts half hour later, for that
  # extra hangover recovery time.
  $first_session=strtotime('10:00');
  //if ($day == 3) {
  //  $first_session=strtotime('10:30');
  //}
  switch ($session){
    case 1:
      $ret = slot_time_str($first_session, $slot,$length);
      break;
    case 2:
      # second sessions starts 3 hours after first
      $ret = slot_time_str($first_session+(3*60*60), $slot,$length);
      break;
    case 3:
      # third sessions starts 5 hours after first
      $ret = slot_time_str($first_session+(5*60*60), $slot,$length);
      break;
    default:
      $ret = 'Unknown Time Slot';
  }
  return($ret);
}

function args_for_post($day, $session, $track, $slot){
  $args = array (
    'post_type' => 'session',
    'post_status' => 'any',
    'meta_query' => array(
      array(
        'key'  => 'decision',
        'value'=>  'accepted',
      ),
      array(
        'key'  => 'schedule_day',
        'value'=>  $day,
        'type' => 'NUMERIC',
      ),
      array(
        'key'  => 'schedule_session',
        'value'=>  $session,
        'type' => 'NUMERIC',
      ),
      array(
        'key'  => 'schedule_track',
        'value'=>  $track,
        'type' => 'NUMERIC',
      ),
      array(
        'key'  => 'schedule_slot',
        'value'=>  $slot,
        'type' => 'NUMERIC',
      ),
    ),
  );
  return($args);
}

function get_schedule() {
  for($day = 1; $day<=3; $day++) {
    for($session = 1; $session<=3; $session++) {
      echo '<div id="d'.$day.'s'.$session.'" class="sched-block ">';
      $j=1;
      for($track = 0; $track<=8; $track++) {
        if ($j % 3 == 1) {echo '<div class="row">';}
        echo '<div class="col-sm-4 session "><h2>';
            if ($track == 0) {
              echo 'Invited Talk';
            } else {
              echo 'Track '. $track ;
          }
        echo '</h2>';
        for($slot = 1; $slot<=3; $slot++) {
          $session_id = "d".$day."t".$track."s".$session."l".$slot;
          $the_query = new WP_Query( args_for_post($day, $session, $track, $slot) );
          while ( $the_query->have_posts() ) {
            $the_query->the_post();
            echo '<div id="'.$session_id.'" class="single-session">';
            // foreach (get_post_custom_values('topic')  as $key => $value ) {
            //   echo '<span class="session-topic">'. $value.'</span><br>';
            // }
            echo '<span class="session-time">';
            if ($track == 0) {
              echo time_for_presentation($day,$session,$slot, 60);
            } else {
              echo time_for_presentation($day,$session,$slot);
            }
            echo '</span><br>';
            echo '<span data-toggle="collapse" data-target="#'.$session_id.'-content" class="session-title">'.get_the_title().'</span><br>';
            echo '<span class="session-presenter">';
            echo the_author_meta('first_name').' ';
            echo the_author_meta('last_name');
            if (get_the_author_meta('organization')){echo ', ';the_author_meta('organization');};
            $p2=get_post_custom_values('presenter2_full_name');
            $p3=get_post_custom_values('presenter3_full_name');
            $p2org=get_post_custom_values('presenter2_organization');
            $p3org=get_post_custom_values('presenter3_organization');
            if ($p2[0]) {echo '<br>'.$p2[0]; if ($p2org[0] ) {echo ', '.$p2org[0];}}
            if ($p3[0]) {echo '<br>'.$p3[0]; if ($p3org[0] ) {echo ', '.$p3org[0];}}
            echo '</span>';
            $collapse =  $track == 0 ? '' : 'collapse';
            echo '<div id="'.$session_id.'-content" class="post-content ' .  $collapse . '">';
            $photo=get_post_custom_values('highlight_photo');
            if ($photo[0]) {echo '<div style="float: left; margin-right: 1em;margin-bottom: 1em;"><img src="' . $photo[0] . '"></div>';}
            echo '<div>'.the_content().'</div><br><a href="'.get_permalink().'"><span class="glyphicon glyphicon-link"></span></a></div>';
            echo '</div>'; //single-session
          } //while
        } //for each slot
        echo '</div>'; // end session
        if ($j % 3 == 0) {echo '</div>';} // end row div
        $j++;
      } // for each track
      echo '</div>'; //end sched-block
    }
  }
}
