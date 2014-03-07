<section id="recent">
  <div class="container">
    <div class="row">
      <div class="col-sm-8" id="landing-posts">
        <?php
        $args = array(
          'posts_per_page' => 3,
          'category_name' => 'update'
        );
        $query = new WP_Query( $args );
          if ( $query->have_posts() ) : ?>
            <!-- the loop -->
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
              <div class="post">
                <div class="col-sm-12 landing-post-meta">
                  <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                  <h2>FOSS4G UPDATES <span class="date"><?php the_date(); ?></span></h2>
                </div>
                <div class="col-sm-11 landing-post-content">
                  <?php the_excerpt(); ?>
                </div>
              </div>
            <?php endwhile; ?>
            <!-- end of the loop -->
            <?php wp_reset_postdata(); ?>
        <?php else:  ?>
          <p><?php _e( 'Sorry, there are no recent updates.' ); ?></p>
        <?php endif; ?>
      </div>
      <div class="col-sm-4" id="landing-side">
        <aside>
          <?php get_sidebar(); ?>
        </aside>
      </div>
    </div>
  </div>
</section>