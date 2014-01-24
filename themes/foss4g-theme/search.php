<?php get_header(); ?>

        <div class="container">
                <div class="content" role="main">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                        <div class="entry">
                <div class="entry-title">
                <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                <p>posted by <?php the_author_posts_link() ?> on <?php the_time('F jS, Y') ?> 
                </div><!-- end entry title -->

                <div class="entry-content" >
                        <?php the_content(); ?>
                </div><!-- end entry-content -->

                <div class="entry-meta">
                        <p>Get the 
                                <a href="<?php the_permalink() ?>">permalink</a>.
                                <br><?php the_tags(); ?>
                        
                                </p>
                </div><!--end entry-meta -->

        </div><!-- end entry -->
                <?php endwhile; else: ?>
                <div class="entry"><h2><?php _e('Sorry, no posts matched your Search criteria'); ?></h2></div>
                

                <?php endif; ?>

                </div><!-- #content -->
                <?php get_sidebar(); ?>
        </div><!-- #container -->


<?php get_footer(); ?>