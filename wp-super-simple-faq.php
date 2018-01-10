<?php
/*
Plugin Name: WP AquaAid
Plugin URI: https://github.com/webseo-online/wpaquaaid
Description: AquaAid
Author: Web SEO Online (PTY) LTD
Author URI: https://webseo.co.za
Version: 0.0.1

	Copyright: © 2016 Web SEO Online (PTY) LTD (email : michael@webseo.co.za)
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Check if gravityforms is active
 */
if ( in_array( 'gravityforms/gravityforms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	if ( ! class_exists( 'WP_AquaAid' ) ) {
		
		/**
		 * Localisation
		 **/
		load_plugin_textdomain( 'WP_AquaAid', false, dirname( plugin_basename( __FILE__ ) ) . '/' );

		class WP_AquaAid {

			public function __construct() { }


			/**
			 * Add scripts used on the front end
			 */
			public function frontend_scripts () {
				global $post; 
				// JS			 
				wp_enqueue_script( 'aquaaid_scripts', plugin_dir_url( __FILE__ ) . 'assets/js/aquaaid.js', array( 'jquery', 'angular_js' ) );				
				// CSS
				wp_register_style( 'aquaaid_css', plugin_dir_url( __FILE__ ) .'assets/css/aquaaid.css', array(), '20161026' );
				wp_enqueue_style( 'aquaaid_css' );				

				// Create local variables here TODO: get from plugin options
				wp_localize_script( 'aquaaid_scripts', 'aquaaid', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'post_id' => $post->ID					
				));
			}


			/**
			 * Class Members
			 */
			public function doSomething() {}

		}

		// finally instantiate our plugin class and add it to the set of globals
		$GLOBALS['WP_AquaAid'] = new WP_AquaAid();
	}
}