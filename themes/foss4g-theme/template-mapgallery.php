<?php
/*
Template Name: Map Gallery
*/
?>

<!----- Wordpress header code ---->

<?php get_header(); ?>
<section class="page-header">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
        <h1><?php the_title(); ?></h1> 
        <?php endwhile; // end of the loop. ?>       
        <?php the_content(); ?>
    </div>
</section>

<!----- Grid we will insert images into ---->

<section class="page-content">
<div class="container">
  <div id='thumb-grid' class="row">               
  </div>
</section>

<!----- Bootstrap Image Gallery lightbox loads onclick ---->

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
                    <button type="button" class="btn btn-info pull-left popover-dismiss modal-desc" data-toggle="popover" data-placement="bottom" data-content="test">                                                
                        Info
                        <span class="caret"></span>
                    </button>
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <h4 id="modal-title" class="modal-title"></h4>
                </div>
                <div class="modal-body next"></div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-default pull-left prev">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        Previous
                    </button>
                    <button type="button" class="modal-vote btn btn-default">
                        Vote
                        <i class="glyphicon glyphicon-thumbs-up"></i>
                    </button>                                      
                    <button type="button" class="btn btn-default next">
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
//Global variable used by bootstrap-image-gallery.js to lookup map attributes using title as the ID and set other attributes in the modal.  Make the loose assumption that title is unique.  Shameless hack
maps = null;

jQuery(document).ready(function ($) {  

  //Fetch maps
  jQuery.ajax({
    dataType: "json",
    url: "/map-gallery/map-gallery-feed/",
  }).done(function (result) {
    //Set global maps variable to result
    maps = result;

    //fetch URL parameters
    params = getUrlParams();
    if (params.order != 'yes') {
        shuffle(maps);
    }

    var grid = jQuery('#thumb-grid');
    // Add images to grid with attributes to drive modal gallery
    $.each(result, function (index, sub) {
      var griditem = jQuery.parseHTML("<div class='item col-md-4 col-sm-6 col-lg-3'><a id='img"+sub.id+"' href='"+sub.medium+"' class='thumbnail' data-gallery title=''><img src='"+sub.small+"'/></a><p class='small-title'>"+sub.title+"</p></div>");
      var esctitle = sub.title.replace(/["']/g, "&#39;");
      var aelement = $(griditem).find("#img"+sub.id.toString());
      aelement.attr('title', esctitle);
      grid.append(griditem);      
    });           
    $('#thumb-grid').imagesLoaded( function(){
      $('#thumb-grid').isotope({
        itemSelector : '.item'
      });
    });          
  });
});

</script>

<?php get_footer(); ?>

