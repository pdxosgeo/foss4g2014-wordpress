<?php get_header(); ?>
<section id="landing-intro">
	<div class="container">
    <div class="row">
      <div class="col-sm-2"></div>
  		<div class="col-sm-4">
  			<img class="img-responsive" src="<?php bloginfo('template_url'); ?>/img/logo_landing_main.png">
      </div>
      <div class="col-sm-6">
        <h2><?php echo get_theme_mod( 'foss4g2014_conference_location' ); ?></h2>
        <h3><?php echo get_theme_mod( 'foss4g2014_conference_date' ); ?></h3>
        <p><?php echo get_theme_mod( 'foss4g2014_description' ); ?></p>
      </div>
    </div>
  </div>
</section>

<section id="conference-info">
  <div id="conference-times" class="row container">
    <div class="col-sm-4">
      <p <?php echo ( get_theme_mod( 'foss4g2014_section1_display' ) ) ? "style='display:none;'" : "" ?>><?php echo get_theme_mod( 'foss4g2014_section1_title' ); ?> <br> 
      <span class="date"><?php echo get_theme_mod( 'foss4g2014_section1_desc' ); ?></span></p>
      <a href="<?php echo get_theme_mod( 'foss4g2014_button_one_link' ); ?>" <?php echo ( get_theme_mod( 'foss4g2014_button_one_display' ) ) ? "style='display:none;'" : "" ?> type="button" class="btn btn-lg" id="button-one"><?php echo get_theme_mod( 'foss4g2014_button_one_text' ); ?></a>             
    </div>
    <div class="col-sm-4">
      <p <?php echo ( get_theme_mod( 'foss4g2014_section2_display' ) ) ? "style='display:none;'" : "" ?>><?php echo get_theme_mod( 'foss4g2014_section2_title' ); ?> <br> 
      <span class="date"><?php echo get_theme_mod( 'foss4g2014_section2_desc' ); ?></span></p>
      <a href="<?php echo get_theme_mod( 'foss4g2014_button_two_link' ); ?>" <?php echo ( get_theme_mod( 'foss4g2014_button_two_display' ) ) ? "style='display:none;'" : "" ?> type="button" class="btn btn-lg" id="button-two"><?php echo get_theme_mod( 'foss4g2014_button_two_text' ); ?></a>
    </div>
    <div class="col-sm-4">
      <p <?php echo ( get_theme_mod( 'foss4g2014_section3_display' ) ) ? "style='display:none;'" : "" ?>><?php echo get_theme_mod( 'foss4g2014_section3_title' ); ?> <br> 
      <span class="date"><?php echo get_theme_mod( 'foss4g2014_section3_desc' ); ?></span></p>
      <a href="<?php echo get_theme_mod( 'foss4g2014_button_three_link' ); ?>" <?php echo ( get_theme_mod( 'foss4g2014_button_three_display' ) ) ? "style='display:none;'" : "" ?> type="button" class="btn btn-lg" id="button-three"><?php echo get_theme_mod( 'foss4g2014_button_three_text' ); ?></a>  
    </div>
  </div>
</section>
                           
                
          
          
      	</div> <!-- /container -->
      </section> <!-- /landing-intro -->

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
        </div>
      </section>
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
                    <a href="#" id="keynote" class="speaker">
                      <div class="speaker-image" style="background-image: url('<?php echo $image[0]; ?>')"></div>
                      <div class="name"><?php the_title(); ?></div>
                     <!--  <div class="speaker-content">
                        <div class="speaker-group"><?php wp_get_post_terms(); ?></div>
                        <div class="speaker-name"><?php the_title(); ?></div>
                        <div class="speaker-info">T</div>
                      </div> -->
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

      <section id="portland-home-image"></section>

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
      <section id="map"></section>
<?php get_footer(); ?>
