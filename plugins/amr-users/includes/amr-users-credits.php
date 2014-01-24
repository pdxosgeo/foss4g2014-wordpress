<?php
/* -----------------------------------------------------------*/
function amr_users_give_credit () {  // check if the web owner is okay to give credit on  a public list
	global $amain;
//		'no_credit' => true,
//	'givecreditmessage' => 
	
	if (empty($amain['no_credit'])  OR  ($amain['no_credit'] == 'give_credit')) {
		if (empty($amain['givecreditmessage'])) 
			$message = amr_users_random_message();
		else 
			$message =$amain['givecreditmessage'];
		return ('<a class="credits" style="font-weight: lighter;
			font-style: italic; font-size:0.7em; line-height:0.8em; float:right;" '
			.'href="http://wpusersplugin.com" title="'
			.$message
			.' - amr-users from wpusersplugin.com'
			.'">'.__('credits','amr-users').'</a>');
	}
	else return '';

}
/* -----------------------------------------------------------*/
function amr_users_random_message () { // offer a number of ways to meaningfully give thanks for the plugin - an seo experiment
	$messages = array(
		__('wordpress searchable user directory plugin','amr-users'),
		__('wordpress searchable member directory plugin','amr-users'),
		__('wordpress user list plugin','amr-users'),
		__('wordpress member list plugin','amr-users'),
		__('wordpress users statistics plugin','amr-users'),
		__('wordpress member statistics plugin','amr-users'),
		__('wordpress sortable member list plugin','amr-users'),
		__('wordpress sortable user list plugin','amr-users'),
		__('wordpress team list plugin','amr-users')
	);
	$randkey = array_rand($messages);
	return $messages[$randkey];
}
/* -----------------------------------------------------------*/
function amr_users_say_thanks_opportunity_form () {
global $amain;

	if (empty($amain['no_credit'])  OR  ($amain['no_credit'] == 'give_credit'))
		$givecredit = true;
	else 	
		$givecredit = false;
		
	echo '<h3>'.__('Acknowledgements', 'amr-users').'</h3>';
	echo '<div style="width: 200px; float:left; padding-right: 50px;">'.amr_users_give_credit ().'</div>';
	
	
	echo '<br />';		
	echo '<label for="give_credit">';	
	echo '<input id="give_credit" type="radio" name="no_credit" value="give_credit"';
	if ($givecredit) echo ' checked="checked" ';
	echo '>';
	_e('Very discreetly, give credit', 'amr-users');
	echo '</label>';	
	
	echo '<br />';	
	echo '<label for="no_credit">';	
	echo '<input type="radio" id="no_credit" name="no_credit" value="no_credit"';
	if (!$givecredit) echo ' checked="checked" ';
	echo '>';
	_e('Do not give credit', 'amr-users');
	echo '</label>';
	echo '<br /><br />';


	_e('Express thanks in other ways:', 'amr-users' );
	echo ' <a target="_blank" href="http://wpusersplugin.com/downloads/buy-it/" title="Support development by purchasing membership and gaining access to add on functionality.">';
	_e('Buy it','amr-users');
	echo '</a>,&nbsp; ';
	
	echo '<a target="_blank" href="http://wordpress.org/extend/plugins/amr-users/#compatibility" title="Tell others this version works for you!">';
	_e('Work it','amr-users');	
	echo '</a>,&nbsp; ';	
	echo '<a target="_blank" href="http://wppluginmarket.com/24736/plugins-that-give-credit-to-plugins/" title="Plug all the plugins you use.">';
	_e('Plug it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a target="_blank" href="http://wordpress.org/extend/plugins/amr-users/" title="Rate it at wordpress">';
	_e('Rate it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a target="_blank" href="http://wpusersplugin.com/rss" title="Stay in touch at least - monitor the rss feed">';
	_e('Watch it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a href="'.admin_url('post-new.php?post_type=post').'" title="Write a post about it.">';
	_e('Press it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a target="_blank" href="https://www.paypal.com" title="Send via paypal to anmari@anmari.com.">';
	_e('Send it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a target="_blank" href="http://twitter.com/?status='.esc_attr('amr-users plugin from http://wpusersplugin.com').'" title="Share something positive.">';
	_e('Tweet it','amr-users');
	echo '</a>,&nbsp; ';
	echo '<a target="_blank" href="http://wpusersplugin.com/" title="Like it from the plugin website.">';
	_e('Like it','amr-users');
	echo '</a>,&nbsp; ';

	echo '<g:plusone size="small" annotation="inline" width="120" href="http://wpusersplugin.com"></g:plusone>';

	
echo '<!-- Place this render call where appropriate -->
<script type="text/javascript">
  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/plusone.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>';
// links policy
// http://wordpress.org/extend/plugins/about/
//http://codex.wordpress.org/Theme_Review#Credit_Links

}

