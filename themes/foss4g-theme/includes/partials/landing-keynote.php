<section id="keynote">
  <div class="container">
    <div class="speakers-container">
      <?php
      $args = array(
        'post_type' => 'speaker',
        'post_status' => 'publish',
        'orderby' => 'ASC',
        'tax_query' => array(
          array(
            'taxonomy' => 'group',
            'field' => 'slug',
            'terms' => 'keynote'
          )
        )
      );
      $query = new WP_Query( $args );
        if ( $query->have_posts() ) : ?>
          <!-- the loop -->
          <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <?php
            if (has_post_thumbnail( $post->ID ) ):
              $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
              <a href="#" id="keynote" class="speaker">
                <div class="speaker-image" style="background-image: url('<?php echo $image[0]; ?>')"></div>
                <div class="tree-ring"></div>
                <div class="name"><?php the_title(); ?></div>
                
               <!--  <div class="speaker-content">
                  <div class="speaker-group"><?php wp_get_post_terms(); ?></div>
                  <div class="speaker-name"><?php the_title(); ?></div>
                  <div class="speaker-info">T</div>
                </div> -->
                <img id="keynote-bird" src="<?php bloginfo('template_url'); ?>/img/logo_bird.png">
                <img id="keynote-callout" src="<?php bloginfo('template_url'); ?>/img/keynote-callout.png">
              </a>
              
            <?php endif; ?>
          <?php endwhile; ?>
          <!-- end of the loop -->
          <?php wp_reset_postdata(); ?>
      <?php else:  ?>
        <p><?php _e( 'No speakers yet!' ); ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>