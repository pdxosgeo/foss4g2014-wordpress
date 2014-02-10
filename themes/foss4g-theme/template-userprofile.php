<?php
/*
Template Name: User Profile
*/
?>

<?php get_header(); ?>
<section class="page-header">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
        <h1>
            <?php if ( is_user_logged_in() ) {
                $user = wp_get_current_user();
                echo $user->user_firstname . "'s"; 
            } ?>
            <?php the_title(); ?>
        </h1>
    </div>
</section>
<section class="page-content">
<div class="container">
    <div class="row">
        <div class="col-md-6 content">
            <?php the_content(); ?>
            <?php echo do_shortcode('[wpuf_profile type="profile" id="272"]'); ?>
            <?php endwhile; // end of the loop. ?>
        </div>
        <div class="col-md-6 user-submissions">
            <?php echo do_shortcode('[wpuf_dashboard post_type="session"]'); ?>
            <?php echo do_shortcode('[wpuf_dashboard post_type="workshop"]'); ?>
        </div>
        <div class="col-md-4">
        </div>
    </div>
</div>

<?php get_footer(); ?>

