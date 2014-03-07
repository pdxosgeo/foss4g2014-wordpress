<section id="sponsors" class="">
  <div class="container">
    <div class="col-md-3" id="featured-sponsors">
      <h2>Sponsors</h2>
      <p><a href="/sponsorships">Learn more</a> about how to help out.</p>
    </div>
    
    <div class="col-md-9" style="padding-left:0;">
      <div id="sponsor-slider" class="hidden-xs">
        <div class='marquee'>
          
          <?php
          $args=array(
            'post_type' => 'sponsor',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            'orderby' => 'rand'
          );

          $my_query = null;
          $my_query = new WP_Query($args);
          if( $my_query->have_posts() ) {
            while ($my_query->have_posts()) : $my_query->the_post(); 
              $url = get_post_meta( $post->ID, "_URL", true ); ?>
                  <a href="<?php echo $url; ?>" target="_blank"><?php the_post_thumbnail(); ?></a>
            <?php endwhile; ?>
          <?php }
          wp_reset_query(); ?>

          

        </div>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
</section>