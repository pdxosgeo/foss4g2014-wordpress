<?php get_header(); ?>

<section class="page">
    <div class="container">
        <div class="row">
            

            <!-- 404 content -->
            <div class="col-sm-3">

            </div>
            <div class="col-sm-6">
                <h1 class="title">Uh Oh!</h1>
                <div class="content">
                    <p>Shoot! We messed up. That doesn't mean the end of the world, though. <a href="<?php echo home_url(); ?>/contact">Let us know</a> if this is a major problem, otherwise make sure you are registered and have submitted your content for FOSS4G2014 in Portland. See you there!</p>
                    <img style="margin-top:15px" width="100%" height="auto" src="<?php bloginfo('template_url'); ?>/img/404cat.gif">
                </div>
                <div class="left-sidebar">
                <?php get_sidebar();
                

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
            </div>

            <div class="col-sm-3"></div>

            

        </div>
    </div>
</section>

<?php get_footer(); ?>