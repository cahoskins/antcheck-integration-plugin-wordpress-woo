<?php
// block direct access to this file
if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

	/*
     * Permission Callback: Only allow access to the antcheck endpoints with the right key
     */
	function antcheck_permissions_callback($request) {
		$options_key = get_option('antcheck_integration_api_key');
		$auth_header = $request->get_header('authorization');
		if($auth_header){
			list($username, $password) = explode(':', base64_decode(substr($auth_header, 6)), 2);
			if($password === $options_key && $username === "antcheckintegrationplugin") {
				return true;
			}
		}
		return new WP_Error( 'not-logged-in', 'Unauthorized', array( 'status' => 401 ) );
	}



	/*
	 * 1. API Endpoint: Products_and_variations
	 * This endpoints includes all data needed for antcheck. But its output could be quite large, so the other endpoints are
	 * implemented as well.
	 */

	// Register the products_and_variations endpoint
	function products_and_variations_endpoint() {
		register_rest_route('antcheck/v1', '/products_and_variations/', array(
			'methods' => 'GET',
			'callback' => 'api_get_products_and_variations',
			'permission_callback' => 'antcheck_permissions_callback',
		));
	}
	add_action('rest_api_init', 'products_and_variations_endpoint');

	// Endpoint callback: function to retrieve products and variations
	function api_get_products_and_variations($data) {
		// Only published products
		$products = wc_get_products(array(
			'status' => 'publish',
			'limit' => -1
		));

		$formatted_products = array();
		foreach ($products as $product) {
			$formatted_product = $product->get_data();
			$formatted_product["permalink"] = $product->get_permalink();

			/* Add all variations if it is a variable product */
			if ($product->is_type('variable')) {
				$variations = $product->get_available_variations();

				/* Generate permalink for variation */
				foreach($variations as $key=>$variation){
					$variations[$key]["permalink"] = $formatted_product["permalink"]."?".http_build_query($variation["attributes"]);
				}
				$formatted_product['variations'] = $variations;
			}
			$formatted_products[] = $formatted_product;
		}
		return rest_ensure_response($formatted_products);
	}



	/*
	 * 2. API Endpoint: Products
	*/

	// Register the products endpoint
	function products_endpoint() {
		register_rest_route('antcheck/v1', '/products/', array(
			'methods' => 'GET',
			'callback' => 'api_get_products',
			'permission_callback' => 'antcheck_permissions_callback',
		));
	}
	add_action('rest_api_init', 'products_endpoint');

	// Endpoint callback: function to retrieve products and variations
	function api_get_products($data) {
		// Only published products
		$products = wc_get_products(array(
			'status' => 'publish',
			'limit' => -1
		));

		echo sizeof($products);

		exit();

		$formatted_products = array();
		foreach ($products as $product) {
			$formatted_product = $product->get_data();
			$formatted_product["permalink"] = $product->get_permalink();
			$formatted_products[] = $formatted_product;
		}
		return rest_ensure_response($formatted_products);
	}



	/*
	 * 3. API Endpoint: Single Product
	*/

	// Register the product endpoint
	function single_product_endpoint() {
		register_rest_route('antcheck/v1', '/products/(?P<product_id>\d+)/', array(
			'methods' => 'GET',
			'callback' => 'api_get_single_product',
			'permission_callback' => 'antcheck_permissions_callback',
		));
	}
	add_action('rest_api_init', 'single_product_endpoint');

	// Endpoint callback: function to retrieve products and variations
	function api_get_single_product($request) {

		// Product id parameter check
        $product_id = $request->get_param('product_id');
        if (!ctype_digit($product_id))
            return new WP_Error('invalid_parameter', 'Invalid parameter. Please provide an integer value.', array('status' => 400));

		// Fetch the product
		$product = wc_get_product($product_id);
        if(empty($product))
            return new WP_Error('no_product', 'Product with does not exist.', array('status' => 404));

    // Get product data and output
    $formatted_product = $product->get_data();
		$formatted_product["permalink"] = $product->get_permalink();
		return rest_ensure_response($formatted_product);
	}



	/*
	 * 4. API Endpoint: Variations
	*/

	// Register the products endpoint
	function variations_endpoint() {
		register_rest_route('antcheck/v1', '/products/(?P<product_id>\d+)/variations/', array(
			'methods' => 'GET',
			'callback' => 'api_get_variations',
			'permission_callback' => 'antcheck_permissions_callback',
		));
	}
	add_action('rest_api_init', 'variations_endpoint');

	// Endpoint callback: function to retrieve products and variations
	function api_get_variations($request) {

        // Product id parameter check
        $product_id = $request->get_param('product_id');
        if (!ctype_digit($product_id))
            return new WP_Error('invalid_parameter', 'Invalid parameter. Please provide an integer value.', array('status' => 400));

		// Fetch the product
		$product = wc_get_product($product_id);
        if(empty($product))
            return new WP_Error('no_product', "Product with id $product_id does not exist.", array('status' => 404));

        // Get variations and output
        $variations = [];
        if ($product->is_type('variable')) {
            $variations = $product->get_available_variations();
            foreach($variations as $key=>$variation){
                $variations[$key]["permalink"] = $product->get_permalink()."?".http_build_query($variation["attributes"]);
            }
        }
        return rest_ensure_response($variations);

	}


?>
