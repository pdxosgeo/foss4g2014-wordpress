<section id="featured-speakers">
  <div class="container">
    
    <h1>Speakers</h1>
    <h4>The best in open geo.</h4>

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
              'terms' => 'keynote',
              'operator'  => 'NOT IN'
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
                <div class="speaker" style="background-image: url('<?php echo $image[0]; ?>')">
                  <div class="speaker-content">
                    <div class="name"><?php the_title(); ?></div>
                    <div class="speaker-description"><?php the_excerpt(); ?></div>
                  </div>
                </div>
                
              <?php endif; ?>
            <?php endwhile; ?>
            <!-- end of the loop -->
            <?php wp_reset_postdata(); ?>
        <?php else:  ?>
          <p><?php _e( 'No speakers yet!' ); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>