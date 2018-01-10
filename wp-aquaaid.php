<?php
/*
	Plugin Name: WP AquaAid
	Plugin URI: https://github.com/webseo-online/aquaaid
	Description: AquaAid
	Author: Web SEO Online (PTY) LTD
	Author URI: https://webseo.co.za
	Version: 0.0.1

	Copyright: © 2016 Web SEO Online (PTY) LTD (email : supprt@webseo.co.za)
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

			public function __construct() {
				// Scripts to include
				add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
				// Plugin Settings page
				add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
				// Plugin Settings
				add_action( 'admin_init', array( $this, 'aa_plugin_settings' ) );				
			}


			/**
			 * Add scripts used on the front end
			 */
			public function frontend_scripts () {
				global $post; 
				// JS			 
				wp_enqueue_script( 'aquaaid_scripts', plugin_dir_url( __FILE__ ) . 'assets/js/aquaaid.js', array( 'jquery') );				
				// CSS register
				wp_register_style( 'aquaaid_css', plugin_dir_url( __FILE__ ) .'assets/css/aquaaid.css', array() );
				// CSS enqueue
				wp_enqueue_style( 'aquaaid_css' );				
				// Create local variables here TODO: get from plugin options
				wp_localize_script( 'aquaaid_scripts', 'aquaaid', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'post_id' => $post->ID,
					'gform_1' => get_option( 'g_input_1' ),
					'gform_2' => get_option( 'g_input_2' )			
				));
			}


			/**
			 * Add options page
			 */
			public function add_plugin_page() {
				// This page will be under "Settings"
				add_options_page(
					'AA Settings', 
					'AA Settings', 
					'manage_options', 
					'aa-setting-admin', 
					array( $this, 'create_admin_page' )
				);				
			}


			/**
			 * settings page callback
			 */
			public function create_admin_page() {
				?>
				<div class="wrap">

					<h1>Settings</h1>

					<form action="options.php" method="post">
						
						<?php settings_fields( 'aa-plugin-settings' ); ?>
						<?php do_settings_sections( 'aa-plugin-settings' ); ?>

						<table class="form-table">
							<tbody>
								<tr>
									<th><label for="g_input_1">Gravity Form 1 ID:</label></th>
									<td><input type="text" id="g_input_1" name="g_input_1" value="<?php echo esc_attr( get_option( 'g_input_1' ) ) ?>" /></td>
								</tr>
								<tr>
									<th><label for="g_input_2">Gravity Form 2 ID:</label></th>
									<td><input type="text" id="g_input_2" name="g_input_2" value="<?php echo esc_attr( get_option( 'g_input_2' ) ) ?>" /></td>
								</tr>								
							</tbody>
						</table>
								
						<?php submit_button(); ?>

					</form>
				</div> 
				<?php
			}
			
			public function aa_plugin_settings() {
				register_setting( 'aa-plugin-settings', 'g_input_1' );
				register_setting( 'aa-plugin-settings', 'g_input_2' );
				register_setting( 'aa-plugin-settings', 'upload_input' );
			}

		}

		// finally instantiate our plugin class and add it to the set of globals
		$GLOBALS['WP_AquaAid'] = new WP_AquaAid();
	}
}
