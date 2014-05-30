<?php
/*
Template Name: News/Blog
*/
?>

<?php get_header(); ?>

<section class="page-content">
<div class="container">
    <div class="row">
    	
        <div class="col-md-8" id="news">
        	<div class="row">
			<?php
			$count = 0;
			$args = array(
				'posts_per_page' => -1,
				'category_name' => 'update'
			);
			$query = new WP_Query( $args );
			if ( $query->have_posts() ) : ?>
			  	<!-- the loop -->
			  	<?php while ( $query->have_posts() ) : $query->the_post();
        		if(!$count) { ?>
        				<article class="post col-md-12">
        		<?php } else { ?>
        				<article class="post col-md-6">
        		<?php } ?>
			        		<div class="post-title">
				        		<h1 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				        		<div class="single-meta"><span class="date"><?php the_time('l, F j, Y') ?> | </span><span class="author">by <?php the_author_posts_link(); ?></span></div>
				        	</div>
				        	<div class="content">
				        		<?php wp_trim_words( the_excerpt(), $num_words = 55, $more = null ); ?>
				        	</div>
				    	</article>
				    <?php
				    if($count%2==0) { ?>
			  			</div><div class="row">
			  		<?php }
			  	$count++;
			  	endwhile; ?>
			  	<!-- end of the loop -->
			  	<?php wp_reset_postdata(); ?>
				<?php else:  ?>
					<p><?php _e( 'Sorry, there are no recent updates.' ); ?></p>
			<?php endif; ?>	
		</div>
		</div>
        <div class="col-md-3 sidebar">
        	<?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
