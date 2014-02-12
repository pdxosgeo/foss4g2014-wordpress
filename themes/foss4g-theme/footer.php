<div class="container">
<div id="contact" class="down">
	<div id="contact-container">
		<div id="contact-form-button">Contact Us <span class="nav-arrow">&#9650;</span><span class="nav-arrow hide">&#9660;</span></div>
		<div id="contact-form">
			<?php if( function_exists( 'ninja_forms_display_form' ) ){ ninja_forms_display_form( 2 ); } ?>
		</div>
	</div>
</div>
</div>

<footer class="footer">
    <div class="container">
        <div class="col-md-6">
          <p>FOSS4G 2014 is a production of the <a href="http://www.osgeo.org/">OSGeo</a> organization. </p>
          <p>Site built by <a href="http://cugos.org">CUGOS</a> using <a href="#">Wordpress</a>.</p>
        </div>
        <div class="col-md-6" id="footer-icons">
          <a href="https://github.com/pdxosgeo" target="_blank"><img src="<?php bloginfo('template_url'); ?>/img/social-github.png"></a>
          <a href="https://twitter.com/foss4g" target="_blank"><img src="<?php bloginfo('template_url'); ?>/img/social-twitter.png"></a>
        </div>   
    </div>
</footer>

    </div>
</div>

<script src="<?php bloginfo('template_url'); ?>/js/jquery.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/bootstrap.min.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/main.js"></script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-43123250-1', '2014.foss4g.org');
  ga('send', 'pageview');

</script>

</body>
</html>
