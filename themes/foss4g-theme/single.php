<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<section class="page-header post">
	<div class="container">
		<h1><?php the_title(); ?></h1>
	</div>
</section>
<section class="page-content">
<div class="container">
    <div class="row">
        <div class="col-md-8 content" id="single-post">
           	<div class="post-content">
                <?php the_content(); ?>
            </div>
			<?php endwhile; // end of the loop. ?>
        </div>
        <div class="col-md-4">
        	<?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>