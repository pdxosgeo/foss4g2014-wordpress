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
            <?php }

                echo 'Sponsors';
                // 5 random sponsors
                $args=array(
                  'post_type' => 'sponsor',
                  'post_status' => 'publish',
                  'posts_per_page' => 5,
                  'orderby' => 'rand'
                );
                ?>
                <ul class="subsponsors">
                <?php
                $my_query = null;
                $my_query = new WP_Query($args);
                if( $my_query->have_posts() ) {
                  while ($my_query->have_posts()) : $my_query->the_post(); 
                    $url = get_post_meta( $post->ID, "_URL", true ); ?>
                        <li><a href="<?php echo $url; ?>" target="_blank"><?php the_post_thumbnail(); ?></a></li>
                  <?php endwhile; ?>
                </ul>
                <?php }
                wp_reset_query(); ?>
            </div>

            <!-- content -->
            <div class="col-sm-8">
                <h1 class="title"><?php the_title(); ?></h1>
                <div class="content">
                   	<?php the_content(); ?>
                </div>
    			<?php endwhile; // end of the loop. ?>
            </div>

            <!-- right sidebar -->
            <div class="col-sm-2 visible-lg sidebar">
            	<?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>