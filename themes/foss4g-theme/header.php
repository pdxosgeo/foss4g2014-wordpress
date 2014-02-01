<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width" charset="UTF-8">
<title><?php get_bloginfo('name'); ?></title>
<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />
<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />
<!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php wp_head() ?>
</head>
<body class="" id="">
    <div class="outer-container">
      <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo home_url(); ?>"><img src="<?php bloginfo('template_url'); ?>/img/logo_nav_bw.png"></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	        <?php
			    wp_nav_menu( array(
			        'menu'              => 'primary',
			        'theme_location'    => 'primary',
			        'depth'             => 2,
			        'menu_class'        => 'nav navbar-nav',
			        'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
			        'walker'            => new wp_bootstrap_navwalker())
			    );
			?>
          <ul class="nav navbar-nav navbar-right">
	        	<?php
              if ( is_user_logged_in() ) {
              $user = wp_get_current_user();
              $profile_link = home_url();
              $logged_in = '<li><a href="' . $profile_link . '/profile" class="user-name">' . $user->user_firstname . '</a></li>';
              $logged_in .= '<li><a href="' . wp_logout_url( home_url() ) . '" title="Logout">Logout</a></li>';
              echo $logged_in; }
              else {
              $not_logged_in = '<li><a href="' . get_bloginfo('url') .'/register">Register</a></li>';
              $not_logged_in .= '<li><a href="' . wp_login_url( home_url() ) . '">Login</a></li>';
              echo $not_logged_in;

              }
            ?>
	      	</ul>
        </div><!-- /.navbar-collapse -->
      </nav>