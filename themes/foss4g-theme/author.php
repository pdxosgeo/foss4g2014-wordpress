<?php get_header(); ?>
<?php if ( have_posts() ) : ?>
<section class="page">
    <div class="container">
        <div class="row">
            <!-- content -->
            <div class="col-sm-9 author-single">
				<h1 class="title">
						<?php
							if ( is_author() ) :
								printf( __( 'Posts by %s', 'wordpress-starter' ), get_the_author() );
							else :
								_e( 'Archives', 'wordpress_starter' );
							endif;
						?>
				</h1>
				<?php the_author_meta( 'description' ) ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<article>
						<h1 class="title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
						<span class="date"><?php the_time('l, F j, Y') ?></span>
						<div class="content">
							<?php the_excerpt(); ?>
						</div>
					</article>
				<?php endwhile; ?>
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
<?php endif; ?>

<?php get_footer(); ?>