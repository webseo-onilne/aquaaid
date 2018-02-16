<?php
/*
Plugin Name: WP AquaAid
Plugin URI: https://github.com/webseo-online/aquaaid
Description: AquaAid
Author: Web SEO Online (PTY) LTD
Author URI: https://webseo.co.za
Version: 0.0.1

  Copyright: © 2018 Web SEO Online (PTY) LTD (email : supprt@webseo.co.za)
  License: GNU General Public License v3.0
  License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


/**
 * No direct access
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No access' );
};


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

			//TODO: https://www.elegantthemes.com/blog/tips-tricks/using-the-wordpress-debug-log

			// $aa_pluginlog = plugin_dir_path(__FILE__).'debug.log';
			// $aa_message = 'An undefined ERROR has occurred'.PHP_EOL;
			// error_log($aa_message, 3, $aa_pluginlog);

			// Scripts to include
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
			// Plugin Settings page
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
			// Plugin Settings
			add_action( 'admin_init', array( $this, 'aa_plugin_settings' ) );
			// Create database on plugin installation
			register_activation_hook( __FILE__, array( $this, 'aa_plugin_install' ) );
			// Admin Upload ajax
			add_action( 'wp_ajax_do_ajax_upload', array( $this, 'do_ajax_upload' ) );
			// Admin ajax get locations
			add_action( 'wp_ajax_aa_get_locations', array( $this, 'aa_get_locations' ) );
			// Admin ajax get data by locations
			add_action( 'wp_ajax_aa_get_data_by_location', array( $this, 'aa_get_data_by_location' ) );
			// Admin ajax update data by locations
			add_action( 'wp_ajax_aa_update_data_by_location', array( $this, 'aa_update_data_by_location' ) );										
			// Frontend DB Fetch with ajax call
			add_action( 'wp_ajax_aa_ajax_fetch_from_db', array( $this, 'aa_ajax_fetch_from_db' ) );
			add_action( 'wp_ajax_nopriv_aa_ajax_fetch_from_db', array( $this, 'aa_ajax_fetch_from_db' ) );
		}


		/**
		 * Add scripts used on the front end
		 *
		 * @return void
		 */

		public function frontend_scripts () {
			global $post; 
			// JS      
			wp_enqueue_script( 'aquaaid_scripts', plugin_dir_url( __FILE__ ) . 'assets/js/aquaaid.js', array('jquery') );        
			// CSS register
			wp_register_style( 'aquaaid_css', plugin_dir_url( __FILE__ ) .'assets/css/aquaaid.css', array() );
			// CSS enqueue
			wp_enqueue_style( 'aquaaid_css' );

			// Create variables
			wp_localize_script( 'aquaaid_scripts', 'aquaaid', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'post_id' => $post->ID,
				'gform_1' => get_option( 'g_select_1' ),
				'gform_2' => get_option( 'g_select_2' )     
			));
		}


		/**
		 * Add scripts used in the admin area
		 *
		 * @return void
		 */

		public function enqueue_admin_assets(){
			wp_enqueue_script( 
				'aa_admin_scripts', 
				plugin_dir_url( __FILE__ ) . 'assets/js/aquaaid_admin.js',
				array( 'jquery' ) 
			);

			wp_enqueue_script( 
				'nprogress',
				'https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js',
				array( 'jquery' )
			);

			wp_enqueue_script( 
				'toastr',
				'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.js',
				array( 'jquery' )
			);			

			wp_register_style( 
				'nprogress_css',
				plugin_dir_url( __FILE__ ) . 'assets/css/nprogress.css',
				false,
				'1.0.0'
			);
			wp_enqueue_style( 'nprogress_css' );

			wp_register_style( 
				'toastrcss',
				'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.css',
				false,
				'1.0.0'
			);
			wp_enqueue_style( 'toastrcss' );							
		}


		/**
		 * This function is only called when our plugin's page loads
		 *
		 * @return void
		 */

		public function lazy_load_admin_js() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		}


		/**
		 * Add options page
		 *
		 * @return void
		 */

		public function add_plugin_page() {
			// This page will be under "Settings"
			$admin_page = add_options_page(
				'AA Settings', 
				'AA Settings', 
				'manage_options', 
				'aa-setting-admin', 
				array( $this, 'create_admin_page' )
			);
			
			// Load the JS only on this admin page
			add_action( 'load-' . $admin_page, array( $this, 'lazy_load_admin_js' ) );      
		}


		/**
		 * settings page html
		 *
		 * @return void
		 */

		public function create_admin_page() {
			include plugin_dir_path( __FILE__ ) . "aa-options-html.php";
		}

      
		/**
		 * Register plugin settings
		 *
		 * @return void
		 */

		public function aa_plugin_settings() {
			register_setting( 'aa-plugin-settings', 'g_select_2' );
			register_setting( 'aa-plugin-settings', 'g_select_1' );
		}


		/**
		 * Add DB table on plugin install
		 *
		 * @return void
		 */

		public function aa_plugin_install() {
			global $wpdb;
			global $aa_db_version;
		
			$table_name = $wpdb->prefix . 'aa_post_codes_uk';
			$charset_collate = $wpdb->get_charset_collate();
		
			$sql = "CREATE TABLE IF NOT EXISTS $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				email varchar(55) DEFAULT '' NOT NULL,
				postarea varchar(255) DEFAULT '' NOT NULL,
				postcode varchar(55) DEFAULT '' NOT NULL,
				message text NOT NULL,
				PRIMARY KEY  (id)
			) ENGINE=InnoDB $charset_collate;";
		
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		
			add_option( 'aa_db_version', $aa_db_version );
		}


		/**
		 * Get Locations for admin area
		 *
		 * @return void
		 */

		public function aa_get_locations() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				global $wpdb;

				$query = $wpdb->get_results(
					$wpdb->prepare( "SELECT postarea FROM {$wpdb->prefix}aa_post_codes_uk GROUP BY postarea" )
				);
				
				echo json_encode( $query ? $query : $wpdb->last_error );				
			}
			wp_die();		
		}


		/**
		 * Get data by location for admin area data table
		 *
		 * @return void
		 */

		public function aa_get_data_by_location() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				global $wpdb;

				$location = $_GET['location'];

				$query = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}aa_post_codes_uk WHERE postarea = '{$location}' GROUP BY postarea" );
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}aa_post_codes_uk WHERE postarea = '{$location}'" );
				
				echo json_encode( $query ? array( "results" => $query, "count" => $count ) : $wpdb->last_error );				
			}
			wp_die();			
		}


		/**
		 * Update data by location for admin area data table
		 *
		 * @return void
		 */

		public function aa_update_data_by_location() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				global $wpdb;

				$email = $_POST['email'];
				$msg   = $_POST['msg'];
				$area  = $_POST['area'];

				$query = $wpdb->update(
					// Table 
					$wpdb->prefix . "aa_post_codes_uk",
					// Data 
					array(
						"email"    => $email,
						"message"  => $msg
					),
					// Where 
					array(
						"postarea" => $area
					) 
				);
				
				echo json_encode( $query ? array( "response" => 'Success', "result" => $query ) : $wpdb->last_error );			
			}
			wp_die();			
		}		


		/**
		 * Admin Ajax DB Import
		 *
		 * @return void
		 */

		public function do_ajax_upload() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				global $wpdb;

				$handle = fopen( $_FILES['aa_file_upload']['tmp_name'], "r" );
				$counter = 0;
				while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== FALSE ) {
				
					// Skip the first row as is likely column names
					if ( $counter === 0 ) {
						$counter++;
						continue;
					}

					//Insert the row into the database
					$query = $wpdb->insert(
						$wpdb->prefix . "aa_post_codes_uk", 
						array(
							"email"       => $data[0],
							"postarea"    => $data[1],
							"postcode"    => $data[2],
							"message"  	  => $data[3],
							"email_copy"  => $data[4],
						)
					);
				}
				fclose( $handle );

				echo json_encode( $query ? 'success' : $wpdb->last_error );   
			}
			wp_die();
		}


		/**
		 * Frontend Select relevant data as per ajax request
		 *
		 * @return void
		 */

		public function aa_ajax_fetch_from_db() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				global $wpdb;

				$aa_userInput = $_POST['aa_userInput'];

				try {
				
					$query = $wpdb->get_results(
						$wpdb->prepare("SELECT email, message, email_copy FROM {$wpdb->prefix}aa_post_codes_uk WHERE postcode = '{$aa_userInput}' GROUP BY email")
					);
					
					echo json_encode( $query ? $query : $wpdb->last_error );
				
				} 
				catch( Exception $e ) {
					// catch any errors that may be thrown
					echo json_encode( array( 'exception response:' => $e->getMessage() ) );
				}
			}
			wp_die();
		}

    }

    // finally instantiate our plugin class and add it to the set of globals
    $GLOBALS['WP_AquaAid'] = new WP_AquaAid();
  }
}