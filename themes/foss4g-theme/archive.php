<?php get_header(); ?>
<?php if ( have_posts() ) : ?>
<section class="page">
    <div class="container">
        <div class="row">
            <!-- content -->
            <div class="col-sm-9">
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
					<article>
						<h1 class="title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
						<div class="single-meta"><span class="date"><?php the_time('l, F j, Y') ?> | </span><span class="author">by <?php the_author(); ?></span></div>
						<div class="content"><?php the_excerpt(); ?></div>
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