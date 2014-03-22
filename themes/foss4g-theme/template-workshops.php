<?php
/*
Template Name: Workshops
*/
?>

<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>

<section class="page">
    <section id="portland-home-image">
      <?php get_template_part( 'includes/partials/schedule-menu', 'schedule-menu' ); ?>
    </section>
    <div class="container">
        
        <div class="row">
            <!-- left sidebar -->
            <div class="col-sm-1"></div>

            <!-- content -->
            <div class="col-sm-10">
                <h1 class="title"><?php the_title(); ?></h1>
                <?php the_content(); ?>
  
          <?php endwhile; // end of the loop. ?>
                <?php
          $args = array(
            'post_type'   => 'workshop',
            'post_status' => array('future','published'),
            'order'       => 'ASC',
            'orderby'     => 'date',
            'number_of_posts'  => 999
          );
          $query = new WP_Query( $args );
            if ( $query->have_posts() ) : ?>
              <!-- the loop -->
              <?php
              while ( $query->have_posts() ) : $query->the_post();
                // meta values
                $format = get_post_meta($post->ID, 'format', true);
                $length = get_post_meta($post->ID, 'length', true);
                $max_participants = get_post_meta($post->ID, 'maximum_participants', true);
                $target_audience = get_post_meta($post->ID, 'target_audience', true); ?>

              <span class="conference-day row"><?php the_date('l'); ?></span>
              <div class="workshop row">
                <div class="col-md-3">
                  <span class="date"><span class="glyphicon glyphicon-time"></span><?php the_time('l, g:ia'); ?></span>
                  <ul class="workshop-meta">
                    <li><span class="glyphicon glyphicon-tag"></span><?php echo $length ?></li>
                    <li><span class="glyphicon glyphicon-ok"></span><?php echo $format ?></li>
                    <!-- <li><span class="glyphicon glyphicon-eye-open"></span><?php echo $max_participants ?></li>
                    <li><span class="glyphicon glyphicon-tags"></span><?php echo $target_audience ?></li>
                  --> </ul>
                </div>
                <div class="col-md-9 workshop-content">
                  <h2><?php the_title(); ?></h2>
                  <h3><span class="glyphicon glyphicon-user"></span><?php the_author_meta('first_name'); ?> <?php the_author_meta('last_name'); ?></h3>
                  <?php the_content(); ?>
                </div>
              </div>



              <?php endwhile; ?>
              <!-- end of the loop -->
              <?php wp_reset_postdata(); ?>
          <?php else:  ?>
            <p><?php _e( 'Workshops will be published April 1st!' ); ?></p>
          <?php endif; ?>
              </div>
              <div class="col-sm-1"></div>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>

