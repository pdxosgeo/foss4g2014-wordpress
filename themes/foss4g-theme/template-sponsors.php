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
        <div class="col-md-8 content sponsor-content ">
            
            <?php
            //for a given post type, return all
            $post_type = 'sponsor';
            $tax = 'level';
            $tax_terms = array('platinum','gold','silver','bronze','supporter','media');
            if ($tax_terms) {
              foreach ($tax_terms as $tax_term) {
                $args=array(
                  'order' => 'ASC',
                  'post_type' => $post_type,
                  "$tax" => $tax_term,
                  'post_status' => 'publish',
                  'posts_per_page' => -1,
                  'caller_get_posts'=> 1              
                );

                $my_query = null;
                $count=0;
                $my_query = new WP_Query($args);
                if( $my_query->have_posts() ) {?>
                  <h1>
                  <?php echo ucfirst($tax_term); ?>
                  </h1>
                  <table class="table">
                  <?php while ($my_query->have_posts()) : $my_query->the_post(); 
                    if ($count % 2 == 0 ){echo('<tr>');}
                    echo('<td class="vert-align" style="border-top:0">');
                    $url = get_post_meta( $post->ID, "_URL", true ); ?>
                      <a href="<?php echo $url; ?>" target="_blank"><?php the_post_thumbnail(); ?></a>
                    </td>
                    <?php 
                  if ($count % 2 == 1 ){echo('</tr>');}
                  $count++;
                  endwhile; ?>
                </table>
                <?php }
                wp_reset_query(); ?>
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
