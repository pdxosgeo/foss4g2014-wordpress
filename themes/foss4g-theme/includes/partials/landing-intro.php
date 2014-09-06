<section id="landing-intro">

	<div class="container">
    <div class="row">
      <div class="col-sm-3"></div>
  		<div class="col-sm-6" style="text-align:center;">
  			<?php get_template_part( 'includes/partials/inline', 'logo.svg' ); ?>
        <?php get_template_part( 'includes/partials/inline', 'logotext.svg' ); ?>
        <p><span>Portland, Oregon, USA</span> • September 8th - 13th, 2014</p>
        <p id="announcements"><a href="http://eepurl.com/N5Q6X"><span class="glyphicon glyphicon-envelope"></span> Sign up for announcements.</a> </p>
        <p id="donations"><a href="http://www.eventbrite.com/e/foss4g-travel-fund-tickets-10102856917"><span class="glyphicon glyphicon-heart-empty"></span>Donate to our Travel Grant Fund.</a> </p>
        <a class="btn reg" style="margin-bottom: 5px" href="/news">Latest News</a><br>
        <?php 
          if (date_create('2014-09-10') < time() ) {
            echo '<a class="btn reg" href="/live">Watch Now!</a>';
          }
        ?>
        <span> </span>
      </div>
      <div class="col-sm-3"></div>
    </div>
  </div>
</section>
