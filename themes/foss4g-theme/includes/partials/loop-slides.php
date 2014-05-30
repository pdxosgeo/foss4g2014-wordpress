<?php
$args = array( 
	'post_type'			=> 'slide',
	'orderby'			=> 'rand',
	'posts_per_page'	=> 1
);
$query = new WP_Query( $args );
if ( $query->have_posts() ) :
	while ( $query->have_posts() ) : $query->the_post();
	$img = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'single-page-thumbnail'); ?>
	<section class="header-images" style="background-image:url(<?php echo $img[0]; ?>);filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $img[0] ?>', sizingMethod='scale');-ms-filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $img[0] ?>', sizingMethod='scale');"></section>
<?php endwhile;
wp_reset_postdata();
else: ?>
	<p><?php _e( 'No slides.' ); ?></p>
<?php endif; ?>
<div class="border">
	<div id="b01"></div>
	<div id="b02"></div>
	<div id="b03"></div>
	<div id="b04"></div>
	<div id="b05"></div>
	<div id="b06"></div>
	<div id="b07"></div>
	<div id="b08"></div>
	<div id="b09"></div>
	<div id="b10"></div>
</div>