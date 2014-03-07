<section id="keynote">
  <div class="container">
    <div class="row">
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
                <div class="col-sm-3 keynote-image-container">
                  <div class="keynote-image" style="background-image: url('<?php echo $image[0]; ?>')"></div>
                </div>
                <div class="col-sm-6 keynote-content">
                  <h1><?php the_title(); ?><span>Keynote</span></h1>
                  <?php the_content(); ?>
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