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
            <div id="sponsor-slider" class="hidden-xs carousel slide" data-ride="carousel">
              <!-- Wrapper for slides -->
              <div class="carousel-inner">
                <div class="item active">
                  <div class="sponsor-list text-center">
                    <a href="http://www.sensorsandsystems.com">
                      <img src="<?php echo get_bloginfo('url'); ?>/wp-content/uploads/2014/01/systems_and_sensors_logo.png">
                    </a>
                    <a href="http://www.directionsmag.com">
                      <img src="<?php echo get_bloginfo('url'); ?>/wp-content/uploads/2014/01/directionsmagazine_logo.png">
                    </a>
                    <a href="http://www.geoinformatics.com">
                      <img src="<?php echo get_bloginfo('url'); ?>/wp-content/uploads/2014/01/geoinformatics_logo.png">
                    </a>
                  </div>
                </div>
                <div class="item">
                  <div class="sponsor-list text-center">
                    <a href="http://www.geoconnexion.com">
                      <img src="<?php echo get_bloginfo('url'); ?>/wp-content/uploads/2014/01/geoconnexion_logo.png">
                    </a>
                    <a href="http://www.slashgeo.org">
                      <img src="<?php echo get_bloginfo('url'); ?>/wp-content/uploads/2014/01/slashgeo_logo.png">
                    </a>
  		  <a href="http://gisuser.com">
                      <img src="<?php echo get_bloginfo('url'); ?>/wp-content/uploads/2014/01/GISusrNews.png">
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
              
            
        </div>
        </div>
      </section>
      <section id="featured-speakers" style="display:none;">
        <div class="container">
          <div class="row">
            <div class="col-xs-6 speaker">
              <div class="col-sm-4"><div class="speaker-photo-container"><img class="img-responsive" src="http://placehold.it/200x200"></div></div>
              <div class="col-sm-8">
                <h2>Speaker Name</h2>
                <h3>Company</h3>
                <p>Noldor Rivendell olog-hai nazgul dolor auctor. Variags dragon miruvor Elrond Forodwaith sapien fermentum Frodo Baggins dragon the world is indeed full of peril and in it there are many dark places. All there is much that is fair. And though in all lands, love is now mingled with grief, it still grows, perhaps.</p>
              </div>
            </div>
            <div class="col-xs-6 speaker">
              <div class="col-sm-4"><div class="speaker-photo-container"><img class="img-responsive" src="http://placehold.it/200x200"></div></div>
              <div class="col-sm-8">
                <h2>Speaker Name</h2>
                <h3>Company</h3>
                <p>You shall be the fellowship of the ring. Great! Where are we going? interdum nec Minhiriath Bree you have my sword... and my axe... and my bow Morgomir orci tortor. Afternoon tea Easterlings uruk-hai Grey Havens ac aliquam Gandalf Cirdan Noldor congue viverra ent Annatar Bree all's well that ends better et congue.</p>
              </div>
            </div>
            <div class="col-xs-6 speaker">
              <div class="col-sm-4"><div class="speaker-photo-container"><img class="img-responsive" src="http://placehold.it/200x200"></div></div>
              <div class="col-sm-8">
                <h2>Speaker Name</h2>
                <h3>Company</h3>
                <p>Eagles undefined rhoncus metus nisi where there's life there's hope, and need of vittles warg Silmaril metus a semper. Cirdan Samwise Gamgee Elrond adipiscing tempor felis Morgomir Isildur Lindon Grey Havens imperdiet ultricies sem Merry Rhudaur Isildur afternoon tea supper vitae augue mi.</p>
              </div>
            </div>
            <div class="col-xs-6 speaker">
              <div class="col-sm-4"><div class="speaker-photo-container"><img class="img-responsive" src="http://placehold.it/200x200"></div></div>
              <div class="col-sm-8">
                <h2>Speaker Name</h2>
                <h3>Company</h3>
                <p>Dinner Tolkien they come in pints? I'm getting one Middle-earth a a. Galadriel black gate supper orci enim Rivendell balrog Mount Doom nec luctus. Isildur nazgul et metus Gondor Easterlings vel rhoncus. Easterlings warg Easterlings eagles tellus aliquam nazgul Numenoreans even the smallest person can change the course of the future Tom Bombadil.</p>
              </div>
            </div>
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
