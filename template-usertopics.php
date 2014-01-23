<?php
/*
Template Name: User Propsals
*/
?>

<?php get_header(); ?>
<section class="page-header">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
        <h1><?php the_title(); ?></h1>
        <?php endwhile; // end of the loop. ?>
    </div>
</section>
<section class="page-content">
<div class="container">
    <div class="row">
        <div class="col-md-8 content">
        
       <?php
		global $wpdb;
		$users = $wpdb->get_results ("SELECT `user_id` as `ID` FROM `" . $wpdb->usermeta);
		if (is_array ($users) && count ($users) > 0)
		    {
		        foreach ($users as $user)
		            {
		                $user = new WP_User ($user->ID);
		                print_r($user->user_email);
		            }
		    }
		?>

        </div>
        <div class="col-md-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>

