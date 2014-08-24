<?php
/*
Template Name: Map Gallery
*/
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

<!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
<div id="blueimp-gallery" class="blueimp-gallery">
    <!-- The container for the modal slides -->
    <div class="slides"></div>
    <!-- Controls for the borderless lightbox -->
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
    <!-- The modal dialog, which will be used to wrap the lightbox content -->
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body next"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left prev">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        Previous
                    </button>
                    <button type="button" class="btn next">
                        Vote
                        <i class="glyphicon glyphicon-thumbs-up"></i>
                    </button>
                    <button type="button" class="btn btn-primary next">
                        Next
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  //Bring back the $ shortcut but just for this page
  var $ = jQuery.noConflict();

  function loadThumbs(subs) {
    subs.forEach(loadThumb);
    $.getScript('//cdn.jsdelivr.net/isotope/1.5.25/jquery.isotope.min.js',function(){
      //
      $('#thumb-grid').imagesLoaded( function(){
        $('#thumb-grid').isotope({
          itemSelector : '.item'
        });
      });      
    });     
  }

  function loadThumb(sub) {
    console.log(sub);
    var grid = jQuery('#thumb-grid');
    grid.append("<div class='item col-md-4 col-sm-6 col-lg-3'><a href='"+sub.large+"' class='thumbnail' data-gallery><img src='"+sub.small+"'/></a><p>"+sub.slug+"</p></div>");  
  }

  jQuery.ajax({
    dataType: "json",
    url: "/map-gallery/map-gallery-feed/",
    success: function(data){loadThumbs(data);}
  });
</script>

<?php get_footer(); ?>

