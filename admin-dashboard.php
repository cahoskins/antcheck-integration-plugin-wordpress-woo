<?php
// block direct access to this file
if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}



	// Hook into the admin menu
	add_action('admin_menu', 'custom_admin_page');

	// Callback function to create the custom admin page
	function custom_admin_page() {
		// Add a top-level menu item
		add_submenu_page(
			'tools.php', // Parent menu slug (Tools)
			'AntCheck Integration',
			'AntCheck Integration',
			'manage_options', // Minimum capability required to access
			'antcheck_integration_dashboard',
			'antcheck_integration_dashboard_callback'
		);
	}

	// Callback function to display content on the custom admin page
	function antcheck_integration_dashboard_callback() {
		global $default_creditals;

		?>
		<div class="wrap">
			<h2>AntCheck Integration</h2>
			<p>This tiny plugin enables the integration of the WooCommerce products to <a href="https://antcheck.info/">AntCheck</a>.</p>

			<h3>Explaination</h3>
			<p>This plugin creates a new Rest API endpoint (/wp-json/antcheck/v1/products_and_variations/, .../products/, .../products/{id}/, .../products/{id}/variations/) for exchanging the product data.
				This does not include any sensible data and only products and product variations "Read permissions" data. The access to the api endpoint is only possible with the key at settings.<br>
			</p>
			<p>AntCheck fetches this endpoint periodically (every few hours) to update the products.<p>


			<h3>Contact</h3>
			<p>We are available for any request at <a href="contact@antcheck.info">contact@antcheck.info</a>.<br>

			<form method="post" action="options.php">
            <?php
            // Output security fields for the registered setting "antcheck_integration_options"
            settings_fields('antcheck_integration_options');

            // Output setting sections and their fields
            do_settings_sections('antcheck-integration-settings');

            // Submit button
            submit_button('Save Settings');
            ?>
        </form>

		<small>Plugin version 0.1 - Because of the simplicity, this plugin may do not need any updates for a long time. AntCheck will contact you if there is an mandatory update available.</small>
		</div>
		<?php
	}

?>