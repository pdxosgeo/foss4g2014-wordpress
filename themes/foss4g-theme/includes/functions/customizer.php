<?php

/**
 * This file builds the options used in the /wp-admin/customize.php UI
 * Access by going to your wp-admin dashboard > Appearance > Customize
 * Most options exist puretly for the landing page
**/

// instantiate foss4g2014 theme customization register
function foss4g2014_theme_customizer( $wp_customize ) {
	// remove default static_front_page register and navigation
	$wp_customize->remove_section( 'static_front_page' );
	$wp_customize->remove_section( 'nav' );

// SITE TITLE & TAGLINE - no need for code since this is default :)
	// show this first

// LANDING PAGE DATES
	// main conference date
	$wp_customize->add_setting( 'foss4g2014_conference_title', array(
		'default' 		=> '',
	) );
	$wp_customize->add_control( 'foss4g2014_conference_title', array(
		'label' 		=> 'Main Conference Text',
		'section' 		=> 'foss4g2014_header',
		'priority' 		=> 10,
	) );
	$wp_customize->add_setting( 'foss4g2014_conference_location', array(
		'default' 		=> '',
	) );
	$wp_customize->add_control( 'foss4g2014_conference_location', array(
		'label' 		=> 'Conference Location',
		'section' 		=> 'foss4g2014_header',
		'priority' 		=> 11,
	) );
	$wp_customize->add_setting( 'foss4g2014_conference_date', array(
		'default' 		=> '',
	) );
	$wp_customize->add_control( 'foss4g2014_conference_date', array(
		'label' 		=> 'Main Conference Dates',
		'section' 		=> 'foss4g2014_header',
		'priority' 		=> 15,
	) );

	// registration deadlines
	$wp_customize->add_setting( 'foss4g2014_registration_title', array(
		'default' 		=> '',
	) );
	$wp_customize->add_control( 'foss4g2014_registration_title', array(
		'label' 		=> 'Registration Text',
		'section' 		=> 'foss4g2014_header',
		'priority' 		=> 30,
	) );
	$wp_customize->add_setting( 'foss4g2014_registration_date', array(
		'default' 		=> '',
	) );
	$wp_customize->add_control( 'foss4g2014_registration_date', array(
		'label' 		=> 'Registration Deadline',
		'section' 		=> 'foss4g2014_header',
		'priority' 		=> 35,
	) );
	$wp_customize->add_setting('foss4g2014_registration_display', array(
        'default' 		=> 0,
	    ));
    $wp_customize->add_control('foss4g2014_registration_display', array(
        'settings' 		=> 'foss4g2014_registration_display',
        'label'    		=> 'Hide Registration Info',
        'section'  		=> 'foss4g2014_header',
        'type'     		=> 'checkbox',
        'priority'		=> 36,
    ));


	// register section for conference times
	$wp_customize->add_section( 'foss4g2014_header', array(
        'title' 		=> 'Conference Date Information',
        'description' 	=> 'Located under landing page headline and footer',
        'priority' 		=> 110,
    ) );

// DESCRIPTION
	$wp_customize->add_setting( 'foss4g2014_description', array(
		'default' 		=> 'foss4g2014 description',
	) );
	$wp_customize->add_control( 'foss4g2014_description', array(
		'label' 		=> 'Description Text',
		'section' 		=> 'foss4g2014_description',
		'priority' 		=> 10,
	) );
	$wp_customize->add_setting('foss4g2014_description_display', array(
        'default' 		=> 0,
	    ));
    $wp_customize->add_control('foss4g2014_description_display', array(
        'settings' 		=> 'foss4g2014_description_display',
        'label'    		=> 'Hide Submission Info',
        'section'  		=> 'foss4g2014_description',
        'type'     		=> 'checkbox',
        'priority'		=> 20,
    ));
	// description section
	$wp_customize->add_section( 'foss4g2014_description', array(
        'title' 		=> 'Description',
        'description' 	=> 'Description section',
        'priority' 		=> 120,
    ) );

// SECTION ONE
	// section title
	$wp_customize->add_setting( 'foss4g2014_section1_title', array(
		'default' 		=> '',
	) );
	$wp_customize->add_control( 'foss4g2014_section1_title', array(
		'label' 		=> 'Title',
		'section' 		=> 'foss4g2014_section1',
		'priority' 		=> 10,
	) );
	// section description
	$wp_customize->add_setting( 'foss4g2014_section1_desc', array(
		'default' 		=> '',
	) );
	$wp_customize->add_control( 'foss4g2014_section1_desc', array(
		'label' 		=> 'Description',
		'section' 		=> 'foss4g2014_section1',
		'priority' 		=> 20,
	) );
	// button hyperlink text
	$wp_customize->add_setting( 'foss4g2014_button_one_text', array(
		'default' 		=> 'Button Text',
	) );
	$wp_customize->add_control( 'foss4g2014_button_one_text', array(
		'label' 		=> 'Text',
		'section' 		=> 'foss4g2014_section1',
		'priority' 		=> 30,
	) );
	// button hyperlink
	$wp_customize->add_setting( 'foss4g2014_button_one_link', array(
		'default' 		=> 'http:// ...',
	) );
	$wp_customize->add_control( 'foss4g2014_button_one_link', array(
		'label' 		=> 'Link',
		'section' 		=> 'foss4g2014_section1',
		'priority' 		=> 40,
	) );
	// button color
	$wp_customize->add_setting( 'foss4g2014_button_one_color', array(
		'default'		=> 'red',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'button_one_color', array(
		'label'        => __( 'Button Color', 'foss4g2014' ),
		'section'    => 'foss4g2014_section1',
		'settings'   => 'foss4g2014_button_one_color',
		'priority'	 => 45,
	) ) );
	// button display button
	$wp_customize->add_setting('foss4g2014_button_one_display', array(
        'default' 		=> 0,
	    ));
    $wp_customize->add_control('foss4g2014_button_one_display', array(
        'settings' 		=> 'foss4g2014_button_one_display',
        'label'    		=> 'Hide Button',
        'section'  		=> 'foss4g2014_section1',
        'type'     		=> 'checkbox',
        'priority'		=> 50,
    ));

	// button section
	$wp_customize->add_section( 'foss4g2014_section1', array(
        'title' 		=> 'Section One',
        'description' 	=> 'Landing Page Description - Column 1',
        'priority' 		=> 130,
    ) );

// SECTION TWO
	// section title
	$wp_customize->add_setting( 'foss4g2014_section2_title', array(
		'default' 		=> '',
	) );
	$wp_customize->add_control( 'foss4g2014_section2_title', array(
		'label' 		=> 'Title',
		'section' 		=> 'foss4g2014_section2',
		'priority' 		=> 10,
	) );
	// section description
	$wp_customize->add_setting( 'foss4g2014_section2_desc', array(
		'default' 		=> '',
	) );
	$wp_customize->add_control( 'foss4g2014_section2_desc', array(
		'label' 		=> 'Description',
		'section' 		=> 'foss4g2014_section2',
		'priority' 		=> 20,
	) );
	// button hyperlink text
	$wp_customize->add_setting( 'foss4g2014_button_two_text', array(
		'default' 		=> 'Button Text',
	) );
	$wp_customize->add_control( 'foss4g2014_button_two_text', array(
		'label' 		=> 'Text',
		'section' 		=> 'foss4g2014_section2',
		'priority' 		=> 30,
	) );
	// button hyperlink
	$wp_customize->add_setting( 'foss4g2014_button_two_link', array(
		'default' 		=> 'http:// ...',
	) );
	$wp_customize->add_control( 'foss4g2014_button_two_link', array(
		'label' 		=> 'Link',
		'section' 		=> 'foss4g2014_section2',
		'priority' 		=> 40,
	) );
	// button color
	$wp_customize->add_setting( 'foss4g2014_button_two_color', array(
		'default'		=> 'red',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'button_two_color', array(
		'label'        => __( 'Button Color', 'foss4g2014' ),
		'section'    => 'foss4g2014_section2',
		'settings'   => 'foss4g2014_button_two_color',
		'priority'	 => 45,
	) ) );
	// button display button
	$wp_customize->add_setting('foss4g2014_button_two_display', array(
        'default' 		=> 0,
	    ));
    $wp_customize->add_control('foss4g2014_button_two_display', array(
        'settings' 		=> 'foss4g2014_button_two_display',
        'label'    		=> 'Hide Button',
        'section'  		=> 'foss4g2014_section2',
        'type'     		=> 'checkbox',
        'priority'		=> 50,
    ));

	// button section
	$wp_customize->add_section( 'foss4g2014_section2', array(
        'title' 		=> 'Section Two',
        'description' 	=> 'Landing Page Description - Column 2',
        'priority' 		=> 140,
    ) );

// SECTION THREE
	// section title
	$wp_customize->add_setting( 'foss4g2014_section3_title', array(
		'default' 		=> '',
	) );
	$wp_customize->add_control( 'foss4g2014_section3_title', array(
		'label' 		=> 'Title',
		'section' 		=> 'foss4g2014_section3',
		'priority' 		=> 10,
	) );
	// section description
	$wp_customize->add_setting( 'foss4g2014_section3_desc', array(
		'default' 		=> '',
	) );
	$wp_customize->add_control( 'foss4g2014_section3_desc', array(
		'label' 		=> 'Description',
		'section' 		=> 'foss4g2014_section3',
		'priority' 		=> 20,
	) );
	// button hyperlink text
	$wp_customize->add_setting( 'foss4g2014_button_three_text', array(
		'default' 		=> 'Button Text',
	) );
	$wp_customize->add_control( 'foss4g2014_button_three_text', array(
		'label' 		=> 'Text',
		'section' 		=> 'foss4g2014_section3',
		'priority' 		=> 30,
	) );
	// button hyperlink
	$wp_customize->add_setting( 'foss4g2014_button_three_link', array(
		'default' 		=> 'http:// ...',
	) );
	$wp_customize->add_control( 'foss4g2014_button_three_link', array(
		'label' 		=> 'Link',
		'section' 		=> 'foss4g2014_section3',
		'priority' 		=> 40,
	) );
	// button color
	$wp_customize->add_setting( 'foss4g2014_button_three_color', array(
		'default'		=> 'black',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'button_three_color', array(
		'label'        => __( 'Button Color', 'foss4g2014' ),
		'section'    => 'foss4g2014_section3',
		'settings'   => 'foss4g2014_button_three_color',
		'priority'	 => 45,
	) ) );
	// button display button
	$wp_customize->add_setting('foss4g2014_button_three_display', array(
        'default' 		=> 0,
	    ));
    $wp_customize->add_control('foss4g2014_button_three_display', array(
        'settings' 		=> 'foss4g2014_button_three_display',
        'label'    		=> 'Hide Button',
        'section'  		=> 'foss4g2014_section3',
        'type'     		=> 'checkbox',
        'priority'		=> 50,
    ));

	// button section
	$wp_customize->add_section( 'foss4g2014_section3', array(
        'title' 		=> 'Section Three',
        'description' 	=> 'Landing Page Description - Column 3',
        'priority' 		=> 150,
    ) );



}
add_action( 'customize_register', 'foss4g2014_theme_customizer', 11 );
function foss4g2014_customize_css() {

?>
	<style type="text/css">
		#button-one { background-color:<?php echo get_theme_mod('foss4g2014_button_one_color'); ?>; }
		#button-two { background-color:<?php echo get_theme_mod('foss4g2014_button_two_color'); ?>; }
		#button-three { background-color:<?php echo get_theme_mod('foss4g2014_button_three_color'); ?>; }
	</style>

<?php

}
add_action( 'wp_head', 'foss4g2014_customize_css');

?>
