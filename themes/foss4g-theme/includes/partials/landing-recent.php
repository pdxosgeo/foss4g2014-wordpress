<section id="recent">
  <div class="container">
    <div class="row">
      <div class="col-sm-8 news">
        <?php
        $args = array(
          'posts_per_page' => 5,
          'category_name' => 'update'
        );
        $query = new WP_Query( $args );
          if ( $query->have_posts() ) : ?>
            <!-- the loop -->
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
              <article>
                <div class="landing-post-meta">
                  <h1 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                  <div class="single-meta"><span class="date"><?php the_time('l, F j, Y') ?> | </span><span class="author">by <?php the_author_posts_link(); ?></span></div>
                </div>
                <div class="content">
                  <?php the_excerpt(); ?>
                </div>
              </article>
            <?php endwhile; ?>
            <!-- end of the loop -->
            <?php wp_reset_postdata(); ?>
        <?php else:  ?>
          <p><?php _e( 'Sorry, there are no recent updates.' ); ?></p>
        <?php endif; ?>
      </div>
      <div class="col-sm-4 sidebar">
        <aside>
          <ul>
            <?php dynamic_sidebar( 'home-widget-area' ); ?>
          </ul>
          <?php get_template_part( 'includes/partials/sidebar-sponsors', 'sidebar-sponsors' ); ?>
        </aside>
      </div>
    </div>
  </div>
</section>