<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>

<section class="page">
    <div class="container">
        <div class="row">
            <!-- left sidebar -->
            <div class="col-sm-3 left-sidebar">
            <?php
            get_sidebar();
            get_template_part( 'includes/partials/sidebar-sponsors', 'sidebar-sponsors' );
            ?>
            </div>

            <!-- content -->
            <div class="col-sm-8">
                <h1 class="title"><?php the_title(); ?></h1>
                <div class="content">
                    <?php the_content(); ?>
                </div>
                <?php endwhile; // end of the loop. ?>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>