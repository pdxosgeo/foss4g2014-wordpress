<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>

<section class="page">
  <div class="container">
    <div class="row">
      <!-- content -->
      <div class="col-sm-8 single">
          <h1 class="title"><?php the_title(); ?></h1>
          <div class="single-meta">
            <?php
              // echo author information
              echo the_author_meta('first_name').' ';
              echo the_author_meta('last_name');
              if (get_the_author_meta('organization')){echo ', ';the_author_meta('organization');};
              $p2=get_post_custom_values('presenter2_full_name');
              $p3=get_post_custom_values('presenter3_full_name');
              $p2org=get_post_custom_values('presenter2_organization');
              $p3org=get_post_custom_values('presenter3_organization');
              if ($p2[0]) {echo '<br>'.$p2[0]; if ($p2org[0] ) {echo ', '.$p2org[0];}}
              if ($p3[0]) {echo '<br>'.$p3[0]; if ($p3org[0] ) {echo ', '.$p3org[0];}}

              // echo time slot and info
              $day = get_post_custom_values('schedule_day');
              $session = get_post_custom_values('schedule_session');
              $slot = get_post_custom_values('schedule_slot');
              $track = get_post_custom_values('schedule_track');
              switch($day[0]) {
                case 1:
                  $dayNice = 'Wednesday';
                  break;
                case 2:
                  $dayNice = 'Thursday';
                  break;
                case 3:
                  $dayNice = 'Friday';
                  break;
                default:
                  $dayNice = '';
              }
              echo '<br>';
              echo '<strong>' . $dayNice . '</strong> ';
              if ($track[0] == 0) {
                echo time_for_presentation($day[0],$session[0],$slot[0], 60);
              } else {
                echo time_for_presentation($day[0],$session[0],$slot[0]);
              }
              echo '<br>';
              echo '<em>Session ' . $session[0] .', Track ' . $track[0] . ', Slot ' . $slot[0] . '</em>';
            ?>
          </div>
          <div class="content">
              <?php the_content(); ?>
          </div>
          <?php endwhile; // end of the loop. ?>
      </div>

      <div class="col-sm-3 sidebar">
      <?php
      get_sidebar();
      get_template_part( 'includes/partials/sidebar-sponsors', 'sidebar-sponsors' );
      ?>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>