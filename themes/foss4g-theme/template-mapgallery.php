<?php
/*
Template Name: Map Gallery
*/

function my_scripts_method() {
  wp_enqueue_script(
    'custom-script',
    get_stylesheet_directory_uri() . '/js/custom_script.js',
    array( 'jquery' )
  );
}
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );
?>

<?php get_header(); ?>

<section class="page-header">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
        <h1><?php the_title(); ?></h1> 
        <?php endwhile; // end of the loop. ?>       
        <?php the_content(); ?>
    </div>
</section>

<section class="page-content">
<div class="container">
  <div id='thumb-grid' class="row">               
  </div>
</section>

<script type="text/javascript">
  //Bring back the $!!! But just for this page
  var $ = jQuery.noConflict();

  function loadThumbs(subs) {
    subs.forEach(loadThumb);
  }

  function loadThumb(sub) {
    console.log(sub);
    var grid = jQuery('#thumb-grid');
    grid.append("<div class='col-md-4 col-sm-6 col-lg-3'><a href='"+sub.large+"' class='thumbnail'><img src='"+sub.small+"'/></a><p>"+sub.slug+"</p></div>");
  }

  jQuery.ajax({
    dataType: "json",
    url: "/map-gallery/map-gallery-feed/",
    success: function(data){loadThumbs(data);}
  });
</script>

<?php get_footer(); ?>

