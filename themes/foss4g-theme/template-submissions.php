<?php
/*
Template Name: User Submissions
*/
?>

<?php get_header(); ?>
<section class="page-header">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
        <h1><?php the_title(); ?></h1>
        
        
    </div>
</section>
<section class="page-content">
<div class="container">
    <div class="row">
        <div class="col-md-8 content">
            <?php the_content(); ?>
        <?php endwhile; // end of the loop. ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>