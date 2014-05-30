<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>

<section class="page">
    <div class="container">
        <div class="row">
            <!-- left sidebar -->
            <div class="col-sm-2 nav-sidebar">
            <?php 
                // siblings
                if($post->post_parent)
                $children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0");
                else
                $children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0");
                if ($children) { ?>
                    <ul>
                    <?php echo $children; ?>
                    </ul>
            <?php
                }
            ?>
            </div>

            <!-- content -->
            <div class="col-sm-7">
                <?php the_title('<h1 class="title">','</h1>',true); ?>
                <div class="content">
                   	<?php the_content(); ?>
                </div>
    			<?php endwhile; // end of the loop. ?>
            </div>

            <!-- right sidebar -->
            <div class="col-sm-3 sidebar">
            <?php
                get_sidebar();
                get_template_part( 'includes/partials/sidebar-submit', 'sidebar-submit' );
                get_template_part( 'includes/partials/sidebar-sponsors', 'sidebar-sponsors' );
            ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>