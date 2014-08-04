<?php
/*
Template Name: Workshops
*/
?>

<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<style>
.workshops h2 {
  font-family: 'Arvo', serif;
  color: #FCBA16;
}
.workshop h2 {
  font-family: 'Arvo', serif;
  color: #FCBA16;
  font-size: 1.3em;
  margin-bottom: 5px;
}

.workshop h3 {
  font-family: 'Arvo', serif;
  font-size: 1.1em;
  margin-top: 0px;
  margin-bottom: 5px;
}
.workshop .format {
  font-family: 'Arvo', serif;
}
</style>

<section class="page">
    <div class="container">
        
        <div class="row">
  
          <?php endwhile; // end of the loop. ?>
          <?php
          // loop through each workshop type in specified order
          $ws = array('Monday Morning','Monday Afternoon','Tuesday Morning','Tuesday Afternoon','Monday Full Day','Tuesday Full Day'); ?>
            
            <div class="col-sm-3">
              <ul class="wshop-nav">
                <li><strong>Monday</strong> <br><a href="#type-0">Morning</a> | <a href="#type-1">Afternoon</a> | <a href="#type-4">Full-day</a></li>
                <li><strong>Tuesday</strong> <br><a href="#type-2">Morning</a> | <a href="#type-3">Afternoon</a> | <a href="#type-5">Full-day</a></li>
              </ul>
            </div>


            <div class="col-sm-9 workshops">
              <h1 class="title"><?php the_title(); ?></h1>
              <?php the_content(); ?>
          <?php 
          for ($w=0; $w<count($ws); $w++) { ?>
            <h2 class="session-type" id="type-<?php echo $w; ?>"><span class="glyphicon glyphicon-time"></span><?php echo $ws[$w]; ?></h2>
            <?php
            $args = array(
              'post_type'   => 'workshop',
              'post_status' => array('future','published', 'draft', 'publish'),
              'order'       => 'ASC',
              'orderby'     => 'title',
              'posts_per_page'  => -1
            );
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) :
            while ( $query->have_posts() ) : $query->the_post();
              $session = get_post_meta($post->ID, 'scheduled_slot', true);
              if($session == $ws[$w]) : 
                
                $format = get_post_meta($post->ID, 'format', true); ?>
                <div class="workshop" id="wshop-<?php echo $post->ID; ?>">
                     <h2>
                        <a data-toggle="collapse" href="#wshop-content-<?php echo $post->ID; ?>">
                         <span style="font-size: .75em;" class="glyphicon glyphicon-chevron-right"></span>
                         <?php the_title(); ?>
                        </a>
                    </h2>
                    <h3 data-toggle="popover" data-content="<?php the_author_meta('description'); ?>">
                      <span class="glyphicon glyphicon-user"></span><?php the_author_meta('first_name'); ?> <?php the_author_meta('last_name'); ?>
                      <?php if (get_the_author_meta('organization')){echo ', ';the_author_meta('organization');};?>
                    </h3>
                      <?php $p2=get_post_custom_values('presenter2_full_name');
                            $p3=get_post_custom_values('presenter3_full_name');
                            $p4=get_post_custom_values('presenter4_full_name');
                            $p2org=get_post_custom_values('presenter2_organization');
                            $p3org=get_post_custom_values('presenter3_organization');
                            $p4org=get_post_custom_values('presenter4_organization');
                            if ($p2[0]) {echo '<h3><span class="glyphicon glyphicon-user"></span>'.$p2[0]; if ($p2org[0]) {echo ', '.$p2org[0];} echo '</h3>';}
                            if ($p3[0]) {echo '<h3><span class="glyphicon glyphicon-user"></span>'.$p3[0]; if ($p3org[0]) {echo ', '.$p3org[0];}echo '</h3>';}
                            if ($p4[0]) {echo '<h3><span class="glyphicon glyphicon-user"></span>'.$p4[0]; if ($p4org[0]) {echo ', '.$p4org[0];}echo '</h3>';}
                      ?>
                    <div class="wshop-content collapse" id="wshop-content-<?php echo $post->ID; ?>"><?php the_content(); ?></div>
                    <p class="format"><strong>Format: </strong><?php echo $format; ?></p>
                  </div>
                  <script>
                  jQuery('#wshop-content-<?php echo $post->ID; ?>').on('show.bs.collapse', function (e) {
                    jQuery("#wshop-content-<?php echo $post->ID; ?>")
                      .parent()
                      .children('h2')
                      .children('a')
                      .children('span')
                      .removeClass("glyphicon-chevron-right").addClass("glyphicon-chevron-down")

                  });
                  jQuery('#wshop-content-<?php echo $post->ID; ?>').on('hide.bs.collapse', function (e) {
                    jQuery("#wshop-content-<?php echo $post->ID; ?>")
                      .parent()
                      .children('h2')
                      .children('a')
                      .children('span')
                      .removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-right")
                  });
                  </script>

              <?php endif;
            endwhile; 
            wp_reset_postdata();
            else:  ?>
              <p><?php _e( 'Workshops will be published April 1st!' ); ?></p>
            <?php endif;

          } ?>
        </div>
      </div>
    </div>
</section>

<?php get_footer(); ?>
<script>
jQuery.ready(
  jQuery('.workshop h3').popover({
    container: 'body', 
    trigger: 'ontouchstart' in document.documentElement ? 'click' : 'hover' ,
    placement: 'ontouchstart' in document.documentElement ? 'bottom' : 'left'    })
);
</script>
