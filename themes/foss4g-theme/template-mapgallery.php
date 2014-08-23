<?php
/*
Template Name: Map Gallery
*/
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
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
  <div class="row">
    <div class="col-md-4">
      <a href="#" class="thumbnail">
        <img src="http://placehold.it/500x500"/>
      </a>
      <h3>Author</h3>
      <p>Description</p>
    </div>    
    <div class="col-md-4">
      <a href="#" class="thumbnail">
        <img src="http://placehold.it/500x500"/>
      </a>
      <h3>Author</h3>
      <p>Description</p>      
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <a href="#" class="thumbnail">
        <img src="http://placehold.it/500x500"/>
      </a>
      <h3>Author</h3>
      <p>Description</p>      
    </div>    
    <div class="col-md-4">
      <a href="#" class="thumbnail">
        <img src="http://placehold.it/500x500"/>
      </a>
      <h3>Author</h3>
      <p>Description</p>      
    </div>
  </div>
</div>
</section>

<script type="text/javascript">
subs = {};
jQuery.ajax({
  dataType: "json",
  url: "/map-gallery/map-gallery-feed/",
  success: function(data){subs = data;}
});
</script>

<?php get_footer(); ?>

