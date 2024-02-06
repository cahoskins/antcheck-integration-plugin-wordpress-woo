<?php
// block direct access to this file
if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}


	// Register and define the settings
	function antcheck_integration_settings_init() {
		// Register a setting and its sanitization callback
		register_setting('antcheck_integration_options', 'antcheck_integration_api_key', 'sanitize_text_field');

		// Add a section to the settings page
		add_settings_section(
			'antcheck_integration_settings_section',
			'Installation Settings',
			'antcheck_integration_settings_section_callback',
			'antcheck-integration-settings'
		);

		// Add a field to the section
		add_settings_field(
			'antcheck_integration_api_key',
			'API Key',
			'antcheck_integration_api_key_callback',
			'antcheck-integration-settings',
			'antcheck_integration_settings_section'
		);
	}

	add_action('admin_init', 'antcheck_integration_settings_init');


	// Section callback function
	function antcheck_integration_settings_section_callback() {
		echo '<p>Please send this key to the email above. We will store it secrectly on the AntCheck website. You do not need to change anything here, this key is random generated. Just copy/paste.</p>';
	}

	// Field callback function
	function antcheck_integration_api_key_callback() {
		$api_key = get_option('antcheck_integration_api_key');
		// Generate a random API key if it doesn't exist
		if (empty($api_key)) {
			$api_key = wp_generate_password(32, false);
			update_option('antcheck_integration_api_key', $api_key);
		}
		echo '<input type="text" id="antcheck_integration_api_key" name="antcheck_integration_api_key" value="' . esc_attr($api_key) . '" size="40" />';
	}

?>