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
        <div class="col-md-8 content">
            <?php the_content(); ?>
            <?php endwhile; // end of the loop. ?>

        </div>
        <div class="col-md-4">
        </div>
    </div>
</div>

<?php get_footer(); ?>

