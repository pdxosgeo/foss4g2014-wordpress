<?php 
get_header();
get_template_part( 'includes/partials/landing-reg', 'landing-reg' );
// get_template_part( 'includes/partials/landing-info', 'landing-info' );
get_template_part( 'includes/partials/inline', 'downtownportland.svg' );
get_template_part( 'includes/partials/landing-map', 'landing-map' );           
get_template_part( 'includes/partials/landing-keynote', 'landing-keynote' );
// get_template_part( 'includes/partials/landing-speakers', 'landing-speakers' );
// get_template_part( 'includes/partials/landing-sponsors', 'landing-sponsors' );
get_template_part( 'includes/partials/loop-slides', 'loop-slides' );
get_template_part( 'includes/partials/landing-recent', 'landing-recent' );     
get_footer();
?>
