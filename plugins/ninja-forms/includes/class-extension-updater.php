<?php
/*
 *
 * This class handles all the update-related stuff for extensions, including adding a license section to the license tab.
 * It accepts two args: Product Name and Version.
 *
 * @param $product_name string
 * @param $version string
 * @since 2.2.47
 * @return void
 */

class NF_Extension_Updater
{
	
	/*
	 *
	 * Define our class variables
	 */
	public $product_nice_name = '';
	public $product_name = '';
	public $version = '';
	public $store_url = 'http://ninjaforms.com';
	public $file = '';
	public $author = '';

	/*
	 *
	 * Constructor function
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function __construct( $product_name, $version, $author, $file, $slug = '' ) {
		$this->product_nice_name = $product_name;
		if ( $slug == '' ) {
			$this->product_name = strtolower( $product_name );
			$this->product_name = preg_replace( "/[^a-zA-Z]+/", "", $this->product_name );			
		} else {
			$this->product_name = $slug;
		}

		$this->version = $version;
		$this->file = $file;
		$this->author = $author;

		$this->add_license_fields();
		$this->license_status();
		
		$this->auto_update();

	} // function constructor

	/*
	 *
	 * Function that adds the license entry fields to the license tab.
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function add_license_fields() {
		$args = array(
			'page' => 'ninja-forms-settings',
			'tab' => 'license_settings',
			'slug' => 'license_settings',
			'settings' => array(
				array(
					'name'          => $this->product_name.'_license',
					'type'          => 'text',
					'label'         => $this->product_nice_name.' '.__( 'License Key', 'ninja-forms' ),
					'desc'          => __( 'You will find this included with your purchase email.', 'ninja-forms' ),
					'save_function' => array( $this, 'check_license' )
				),
			),
		);
		if( function_exists( 'ninja_forms_register_tab_metabox_options' ) ){
			ninja_forms_register_tab_metabox_options( $args );
		}
	} // function add_license_fields

	/*
	 *
	 * Function that activates the license for this product
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function check_license( $data ) {
		$plugin_settings = nf_get_settings();

		if( isset( $plugin_settings[ $this->product_name.'_license_status' ] ) ){
			$status = $plugin_settings[ $this->product_name.'_license_status' ];
		}else{
			$status = 'invalid';
		}

		if( isset( $plugin_settings[ $this->product_name.'_license' ] ) ){
			$old_license = $plugin_settings[ $this->product_name.'_license'];
		}else{
			$old_license = '';
		}

		if ( $old_license != '' AND $old_license != $data[ $this->product_name.'_license' ] AND $status == 'valid' ) {
			$this->deactivate_license();
		}

		if( $old_license == '' OR ( $old_license != $data[ $this->product_name.'_license' ] ) OR $status == 'invalid' ){
	 		$this->activate_license( $data );
		}
	} // function check_license

	/*
	 *
	 * Function that activates our license
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function activate_license( $data ) {
		$plugin_settings = nf_get_settings();
		// retrieve the license from the database
		$license = $data[ $this->product_name.'_license' ];

		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( $this->product_nice_name ) // the name of our product in EDD
		);
 
		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, $this->store_url ) );
 
		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;
 
		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "valid" or "invalid"
 		$plugin_settings[  $this->product_name.'_license_status' ] = $license_data->license;

		update_option( 'ninja_forms_settings', $plugin_settings );
	}

	/*
	 *
	 * Function that deactivates our license if the user clicks the "Deactivate License" button.
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function deactivate_license() {
		$plugin_settings = nf_get_settings();

		if( isset( $plugin_settings[ $this->product_name.'_license_status' ] ) ){
			$status = $plugin_settings[ $this->product_name.'_license_status' ];
		}else{
			$status = 'invalid';
		}

		if( isset( $plugin_settings[ $this->product_name.'_license' ] ) ){
			$license = $plugin_settings[ $this->product_name.'_license'];
		}else{
			$license = '';
		}
		
		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'deactivate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( $this->product_nice_name ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, $this->store_url ), array( 'timeout' => 15, 'sslverify' => false ) );

 		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			// $license_data->license will be either "valid" or "invalid"
			$plugin_settings[  $this->product_name.'_license_status' ] = 'invalid';
	 		$plugin_settings[  $this->product_name.'_license' ] = '';
			update_option( 'ninja_forms_settings', $plugin_settings );
		}
	}

	/*
	 *
	 * Function that adds the green checkmark or red X to indicate license status
	 *
	 * @since 2.2.46
	 * @return void
	 */

	function license_status() {
		global $ninja_forms_tabs_metaboxes;

		for ($x=0; $x < count( $ninja_forms_tabs_metaboxes['ninja-forms-settings']['license_settings']['license_settings']['settings'] ); $x++) { 
			if( $ninja_forms_tabs_metaboxes['ninja-forms-settings']['license_settings']['license_settings']['settings'][$x]['name'] == $this->product_name.'_license' ){
				$plugin_settings = nf_get_settings();
				if( !isset( $plugin_settings[ $this->product_name.'_license_status' ] ) OR $plugin_settings[ $this->product_name.'_license_status' ] == 'invalid' ){
					$status = ' <img src="'.NINJA_FORMS_URL.'/images/no.png">';
				}else{
					$status = ' <img src="'.NINJA_FORMS_URL.'/images/yes.png">';
				}
				$ninja_forms_tabs_metaboxes['ninja-forms-settings']['license_settings']['license_settings']['settings'][$x]['label'] .= $status;
			}		
		}
	} // function license_status

	/*
	 *
	 * Function that runs all of our auto-update functionality
	 *
	 * @since 2.2.47
	 * @return void
	 */

	function auto_update() {
		$plugin_settings = nf_get_settings();

		// retrieve our license key from the DB
		if( isset( $plugin_settings[ $this->product_name.'_license' ] ) ){
		  $license = $plugin_settings[ $this->product_name.'_license' ];
		}else{
		  $license = '';
		}

		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater( $this->store_url, $this->file, array(
		    'version'   => $this->version,     // current version number
		    'license'   => $license,  // license key (used get_option above to retrieve from DB)
		    'item_name'     => $this->product_nice_name,  // name of this plugin
		    'author'  => $this->author,  // author of this plugin
		  )
		);
	} // function auto_update

} // class