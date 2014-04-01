<?php
/*
Template Name: Workshops
*/
?>

<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>

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
              'post_status' => array('future','published', 'draft'),
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
                <div class="workshop" id="wshop-<?php echo $post->ID; ?>"
                    <h2><a data-toggle="collapse" href="#wshop-content-<?php echo $post->ID; ?>"
                         <?php the_title(); ?>
                        </a>
                    </h2>
                    <h3><span class="glyphicon glyphicon-user"></span><?php the_author_meta('first_name'); ?> <?php the_author_meta('last_name'); ?></h3>
                    <div class="wshop-content collapse" id="wshop-content-<?php echo $post->ID; ?>"><?php the_content(); ?></div>
                    <p class="format"><strong>Format: </strong><?php echo $format; ?></p>
                  </div>    
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

