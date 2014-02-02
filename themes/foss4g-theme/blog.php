<?php
/*
Template Name: News/Blog
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
        <div class="col-md-8 content" id="news">
			<?php
			$args = array(
				'posts_per_page' => 10,
				'category_name' => 'update'
			);
			$query = new WP_Query( $args );
			if ( $query->have_posts() ) : ?>
			  	<!-- the loop -->
			  	<?php while ( $query->have_posts() ) : $query->the_post(); ?>
		    	<div class="post">
	        		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
	        		<h3>By <?php the_author(); ?> <span class="date"><?php the_date(); ?></span></h2>
		        	<div class="post-content">
		        		<?php the_content(); ?>
		        	</div>
		    	</div>
			  	<?php endwhile; ?>
			  	<!-- end of the loop -->
			  	<?php wp_reset_postdata(); ?>
				<?php else:  ?>
					<p><?php _e( 'Sorry, there are no recent updates.' ); ?></p>
			<?php endif; ?>	
		</div>
        <div class="col-md-4 sidebar">
        	<?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>