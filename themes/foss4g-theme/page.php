<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>

<section class="page">
    <div class="container">
        <div class="row">
            <!-- left sidebar -->
            <div class="col-sm-2 left-sidebar">
            <?php 
                // page title
                if($post->post_parent)
                echo get_the_title($post->post_parent);
                else
                the_title();

                // siblings
                if($post->post_parent)
                $children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0");
                else
                $children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0");
                if ($children) { ?>
                    <ul class="submenu">
                    <?php echo $children; ?>
                    </ul>
            <?php
                }
                get_template_part( 'includes/partials/sidebar-sponsors', 'sidebar-sponsors' );
            ?>
            </div>

            <!-- content -->
            <div class="col-sm-7">
                <div class="content">
                    <h1 class="title"><?php the_title(); ?></h1>
                   	<?php the_content(); ?>
                </div>
    			<?php endwhile; // end of the loop. ?>
            </div>

            <!-- right sidebar -->
            <div class="col-sm-3 sidebar">
            <?php
                get_sidebar();
                get_template_part( 'includes/partials/sidebar-submit', 'sidebar-submit' );
            ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>