<?php get_header(); ?>
<?php if ( have_posts() ) : ?>
<section class="page">
    <div class="container">
        <div class="row">
            <!-- left sidebar -->
            <div class="col-sm-3 left-sidebar">
            <?php
            get_sidebar();
            get_template_part( 'includes/partials/sidebar-sponsors', 'sidebar-sponsors' );
            ?>
            </div>

            <!-- content -->
            <div class="col-sm-8">
				<h1 class="title">
				<?php
					if ( is_month() ) :
						printf( __( 'Posts from %s', 'wordpress_starter' ), get_the_date( _x( 'F Y', 'archive format', 'wordpress_starter' ) ) );
					else :
						_e( 'Archives', 'wordpress_starter' );
					endif;
				?>
				</h1>
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="archive content">
						<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
						<span class="date"><?php the_time('l, F j, Y') ?></span>
						<?php the_excerpt(); ?>
					</article>
				<?php endwhile; ?>
			</div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>