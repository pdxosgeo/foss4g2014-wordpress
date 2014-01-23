<?php
/*
Template Name: Sidebar Left
*/
?>

<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<section class="page-header">
	<div class="container">
		<h1><?php the_title(); ?></h1>
	</div>
</section>
<section class="page-content">
<div class="container clearfix">
    <div class="row">
        <div class="col-md-8 content pull-right">
           	<?php the_content(); ?>
			<?php endwhile; // end of the loop. ?>
        </div>
        <div class="col-md-4 sidebar pull-left">
        	<?php the_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>