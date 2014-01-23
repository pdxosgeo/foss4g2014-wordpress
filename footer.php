<div class="container">
<div id="contact">
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
    </div>
</footer>

    </div>
</div>

<script src="<?php bloginfo('template_url'); ?>/js/jquery.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/bootstrap.min.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/main.js"></script>

</body>
</html>