<?php
/*
Plugin Name: AntCheck Integration
Plugin URI: https://antcheck.info
Description: This tiny plugin enables the integration of the WooCommerce products to Antcheck
Version: 0.2
Requires at least: 5.0
Author: Darvin
Author URI: https://darvin.de/
License: Public Domain
License URI: https://wikipedia.org/wiki/Public_domain
Text Domain: antcheck-integration-plugin
*/

// block direct access to this file
if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}



/*
 * Plugin updater from GitHub
 */
include_once('updater.php');
if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
	$config = array(
		'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
		'proper_folder_name' => 'antcheck-integration', // this is the name of the folder your plugin lives in
		'api_url' => 'https://api.github.com/repos/darvinde/antcheck-integration-plugin-wordpress-woo', // the GitHub API url of your GitHub repo
		'raw_url' => 'https://raw.github.com/darvinde/antcheck-integration-plugin-wordpress-woo/main', // the GitHub raw url of your GitHub repo
		'github_url' => 'https://github.com/darvinde/antcheck-integration-plugin-wordpress-woo', // the GitHub url of your GitHub repo
		'zip_url' => 'https://github.com/darvinde/antcheck-integration-plugin-wordpress-woo/zipball/main', // the zip url of the GitHub repo
		'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
		'requires' => '3.0', // which version of WordPress does your plugin require?
		'tested' => '3.3', // which version of WordPress is your plugin tested up to?
		'readme' => 'README.md', // which file to use as the readme for the version number
		'access_token' => '', // Access private repositories by authorizing under Plugins > GitHub Updates when this example plugin is installed
	);
	new WP_GitHub_Updater($config);
}




// Only run plugin WooCommerce is active (including network activated).
$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';
if (in_array( $plugin_path, wp_get_active_and_valid_plugins() ) || in_array( $plugin_path, wp_get_active_network_plugins() ) ) {



	/*
	 * Short plugin activation message
	 */

	register_activation_hook(__FILE__, 'plugin_activation_message');
	function plugin_activation_message() {
		$activation_message = "Thanks for activating the plugin! Please navigate to Tools -> AntCheck integration to complete the installation.";
		set_transient('plugin_activation_message', $activation_message, 5); // Transient expires after 5 seconds
	}
	add_action('admin_notices', 'display_activation_message');
	function display_activation_message() {
		$activation_message = get_transient('plugin_activation_message');
		if ($activation_message) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($activation_message) . '</p></div>';
			// Delete the transient to avoid displaying the message on every admin page load
			delete_transient('plugin_activation_message');
		}
	}



	/*
	 * Handle of multiple product and variations apis
	 */

	include "apis.php";

	/*
	 * Create a info dashboard page and add it to the admin menu under Tools.
	 */

	include "admin-dashboard.php";


	/*
	 * Create a settings field for the api key
	 */
	include "settings.php";

}

