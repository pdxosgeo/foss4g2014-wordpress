<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>

<section class="page">
    <div class="container">
        <div class="row">
            <!-- content -->
            <div class="col-sm-8 single">
                <h1 class="title"><?php the_title(); ?></h1>
                <div class="single-meta"><span class="date"><?php the_time('l, F j, Y') ?> | </span><span class="author">by <a href="<?php the_author_link(); ?>"><?php the_author(); ?></a></span></div>
                <div class="content">
                    <?php the_content(); ?>
                </div>
                <?php endwhile; // end of the loop. ?>
            </div>

            <div class="col-sm-3 sidebar">
            <?php
            get_sidebar();
            get_template_part( 'includes/partials/sidebar-sponsors', 'sidebar-sponsors' );
            ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>