<section id="keynote">
  <div class="container">
    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-8">
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
            if (has_post_thumbnail( $post->ID ) ): ?>
              <?php the_post_thumbnail('medium'); ?>
              <h5>Keynote Speaker</h5>
              <h1><?php the_title(); ?></h1>
              <?php the_content(); ?>              
            <?php endif; ?>
          <?php endwhile; ?>
          <!-- end of the loop -->
          <?php wp_reset_postdata(); ?>
      <?php else:  ?>
        <p><?php _e( 'No speakers yet!' ); ?></p>
      <?php endif; ?>
        </div>
      </div>
    <div class="col-md-2"></div>
  </div>
</section>