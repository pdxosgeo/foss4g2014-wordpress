<?php
/*
Template Name: Sponsors
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
        <div class="col-md-8 content">
            
            <?php
            //for a given post type, return all
            $post_type = 'sponsor';
            $tax = 'level';
            $tax_terms = get_terms($tax);
            if ($tax_terms) {
              foreach ($tax_terms as $tax_term) {
                $args=array(
                  'order' => 'ASC',
                  'post_type' => $post_type,
                  "$tax" => $tax_term->slug,
                  'post_status' => 'publish',
                  'posts_per_page' => -1,
                  'caller_get_posts'=> 1              
                );

                $my_query = null;
                $my_query = new WP_Query($args);
                if( $my_query->have_posts() ) {
                  echo $tax_term->name; ?>
                  <ul>
                  <?php while ($my_query->have_posts()) : $my_query->the_post(); 
                    $url = get_post_meta( $post->ID, "_URL", true );
                    if ($url) { ?>
                        <li><a href="<?php echo $url; ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
                    <?php } else { ?>
                        <li><?php the_title(); ?></li>
                    <?php }
                  endwhile; ?>
                  </ul> 
                <?php }
                wp_reset_query(); ?>
                <hr>
              <?php }
            }
            ?>


        </div>
        <div class="col-md-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>

