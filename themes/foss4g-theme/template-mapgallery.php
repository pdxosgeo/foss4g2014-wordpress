<?php
/*
Template Name: Map Gallery
*/
?>

<?php get_header(); ?>
<section class="page-header">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
        <h1 class="title"><?php the_title(); ?></h1> 
        <?php endwhile; // end of the loop. ?>       
        <?php the_content(); ?>
    </div>
</section>

<!-- Grid we will insert images into -->

<section class="page-content">
<div class="container">
  <div id='thumb-grid' class="row">               
  </div>
</section>

<!-- Bootstrap Image Gallery lightbox loads onclick -->

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
                    <button type="button" class="btn btn-info pull-left popover-dismiss modal-desc" data-toggle="popover" data-placement="bottom">                                                
                        More
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
                        <i class="voteglyph glyphicon glyphicon-thumbs-up"></i>
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
//Enable auto storage of JSON objects
$.cookie.json = true;

//Global variables. Used by mapgallery.js and bootstrap-image-gallery.js
globals = {
    votes: null,    //Votes by this client from cookie
    maps: null,     //Map objects from server
    ip_votes: null, //Votes at this IP address from server
    curip: null     //IP address of this client from server
}

jQuery(document).ready(function ($) {  
    fetchVotes();
});

</script>

<?php get_footer(); ?>

