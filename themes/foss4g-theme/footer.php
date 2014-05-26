<div class="container">
<div id="contact" class="down">
	<div id="contact-container">
		<div id="contact-form-button">Contact Us <span class="nav-arrow">&#9650;</span><span class="nav-arrow hide">&#9660;</span></div>
		<div id="contact-form">
                  <form id="zFormer" method="POST" action="<?php echo get_template_directory_uri()?>/contact.php" name="former">
                  <div id="box_form">
                   <label for="z_name">Your Name:</label>
                   <input type="text" name="z_name" required>
                   <label for="z_requestor">Your Email Address: </label>
                   <input type="email"  name="z_requester" required>
                   <label for="z_subject">Subject: </label>
                   <input type="text" name="z_subject" required>
                   <label for="z_description">What's up? </label>
                   <textarea name="z_description" required></textarea>
                   <input type="submit" value="submit" id="submitter">
                 </div>
                </form>
               </div>
	</div>
</div>
</div>

<footer class="footer">
<!--     <div class="footer-section col-sm-4"></div>
    <div class="footer-section col-sm-4"></div>
    <div class="footer-section col-sm-4"></div>
 -->    <section id="footer-content">
      <div class="container">
          <div class="col-md-6">
            <p>FOSS4G 2014 is a production of the <a href="http://www.osgeo.org/">OSGeo</a> organization. </p>
            <p>Site built by <a href="http://cugos.org">CUGOS</a> using <a href="#">Wordpress</a>.</p>
          </div>
          <div class="col-md-6" id="footer-icons">
            <!-- Place this code where you want the badge to render. -->
            <a href="//plus.google.com/u/0/115019016550251907241?prsrc=3" rel="publisher" target="_blank"><img src="<?php bloginfo('template_url'); ?>/img/social-gplus.svg" alt="Google+"/></a>
            <a href="https://github.com/pdxosgeo" target="_blank"><img src="<?php bloginfo('template_url'); ?>/img/social-github.svg" alt="Github"></a>
            <a href="https://twitter.com/foss4g" target="_blank"><img src="<?php bloginfo('template_url'); ?>/img/social-twitter.svg" alt="Twitter"></a>
          </div>   
      </div>
    </section>
</footer>

    </div>
</div>

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
