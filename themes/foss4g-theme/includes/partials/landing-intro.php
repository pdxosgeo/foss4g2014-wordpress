<section id="landing-intro">

	<div class="container">
    <div class="row">
      <div class="col-sm-3"></div>
  		<div class="col-sm-6" style="text-align:center;">
  			<?php get_template_part( 'includes/partials/inline', 'logo.svg' ); ?>
        <?php get_template_part( 'includes/partials/inline', 'logotext.svg' ); ?>
        <h3><?php echo get_theme_mod( 'foss4g2014_conference_date' ); ?></h3>
        
      </div>
      <div class="col-sm-3">
        
      </div>
    </div>
    <div id="registration">
      <a class="btn reg" href="/registration">Register Now</a>
      <ul>
        <li><a href="<?php echo get_theme_mod( 'foss4g2014_button_one_link' ); ?>" <?php echo ( get_theme_mod( 'foss4g2014_button_one_display' ) ) ? "style='display:none;'" : "" ?> type="button" class="btn sub-reg" id="button-one"><?php echo get_theme_mod( 'foss4g2014_section1_title' ); ?></a><br>
        <span class="date"><?php echo get_theme_mod( 'foss4g2014_section1_desc' ); ?></span></li>
        <li><a href="<?php echo get_theme_mod( 'foss4g2014_button_two_link' ); ?>" <?php echo ( get_theme_mod( 'foss4g2014_button_two_display' ) ) ? "style='display:none;'" : "" ?> type="button" class="btn sub-reg" id="button-two"><?php echo get_theme_mod( 'foss4g2014_section2_title' ); ?></a><br>
        <span class="date"><?php echo get_theme_mod( 'foss4g2014_section2_desc' ); ?></span></li>
        <li><a href="<?php echo get_theme_mod( 'foss4g2014_button_three_link' ); ?>" <?php echo ( get_theme_mod( 'foss4g2014_button_three_display' ) ) ? "style='display:none;'" : "" ?> type="button" class="btn sub-reg" id="button-three"><?php echo get_theme_mod( 'foss4g2014_section3_title' ); ?></a><br>
        <span class="date"><?php echo get_theme_mod( 'foss4g2014_section3_desc' ); ?></span></li>
      </ul>
    </div>
    
  </div>
</section>