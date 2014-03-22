<?php
/*
Template Name: Workshops
*/
?>

<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>

<section class="page">
    <div class="container">
        <div class="row">
            <!-- left sidebar -->
            <div class="col-sm-2 left-sidebar">
            <?php 
                // page title
                if($post->post_parent)
                echo get_the_title($post->post_parent);
                else
                the_title();

                // siblings
                if($post->post_parent)
                $children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0");
                else
                $children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0");
                if ($children) { ?>
                    <ul class="submenu">
                    <?php echo $children; ?>
                    </ul>
            <?php
                }
                get_template_part( 'includes/partials/sidebar-sponsors', 'sidebar-sponsors' );
            ?>
            </div>

            <!-- content -->
            <div class="col-sm-7">
                <h1 class="title"><?php the_title(); ?></h1>
                <div class="content">
                    <?php the_content(); ?>
  
          <?php endwhile; // end of the loop. ?>
                <?php
          $args = array(
            'post_type'   => 'workshop',
            'post_status' => array('future','published'),
            'order'       => 'ASC',
            'orderby'     => 'date'
          );
          $query = new WP_Query( $args );
            if ( $query->have_posts() ) : ?>
              <!-- the loop -->
              <?php while ( $query->have_posts() ) : $query->the_post();
                // meta values
                $format = get_post_meta($post->ID, 'format', true);
                $length = get_post_meta($post->ID, 'length', true);
                $max_participants = get_post_meta($post->ID, 'maximum_participants', true);
                $target_audience = get_post_meta($post->ID, 'target_audience', true);
              ?>
                  

              <div class="row workshop">
                <div class="col-md-4">
                  <?php the_date(); ?>
                  <ul class="workshop-meta">
                    <li><span class="glyphicon glyphicon-search"></span><?php echo $length ?></li>
                    <li><span class="glyphicon glyphicon-search"></span><?php echo $format ?></li>
                    <li><span class="glyphicon glyphicon-search"></span><?php echo $max_participants ?></li>
                    <li><span class="glyphicon glyphicon-search"></span><?php echo $target_audience ?></li>
                  </ul>
                </div>
                <div class="col-md-8">
                  <h2><?php the_title(); ?></h2>
                  <h3><?php the_author(); ?></h3>
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
            </div>

            <!-- right sidebar -->
            <div class="col-sm-3 sidebar">
            <?php
                get_sidebar();
                get_template_part( 'includes/partials/sidebar-submit', 'sidebar-submit' );
            ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>

