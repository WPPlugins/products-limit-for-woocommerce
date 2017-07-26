<?php

if (! defined('ABSPATH')) {
	exit();
}

/**
 * Create the section beneath the products tab
 **/
add_filter( 'woocommerce_get_sections_products', 'products_lfw_number_field_add_section' );
function products_lfw_number_field_add_section( $sections ) {
	
	$sections['products_limit_woo'] = __( 'Min/max products limit', 'products-limit' );
	return $sections;
	
}

/**
 * Add settings to the specific section we created before
 */
add_filter( 'woocommerce_get_settings_products', 'products_lfw_number_field_all_settings', 10, 2 );
function products_lfw_number_field_all_settings( $settings, $current_section ) {
	/**
	 * Check the current section is what we want
	 **/
	if ( $current_section == 'products_limit_woo' ) {
		$settings_limit = array();
		// Add Title to the Settings
		$settings_limit[] = array( 'name' => __( 'Products limit', 'products-limit' ), 'type' => 'title', 'desc' => __( 'Setting to set minimum and maximum products quantity', 'products-limit' ), 'id' => 'products_limit_woo' );
		
		// Add number field option with minimun number limit
		$settings_limit[] = array(
			'name'     => __( 'Min products', 'products-limit' ),
			'desc_tip' => __( 'Add minimum limit', 'products-limit' ),
			'id'       => 'number_field',
			'type'     => 'number',
			'css'      => 'width:100px;',
			'desc'     => __( 'Add minimum limit of products', 'products-limit' ),
			'custom_attributes' => array(
				'step' 	=> '1',
				'min'	=> '0'
			) 
		);



		// Add number field option with max number limit
		$settings_limit[] = array(
			'name'     => __( 'Max products', 'products-limit' ),
			'desc_tip' => __( 'Add maximum limit', 'products-limit' ),
			'id'       => 'number_field2',
			'type'     => 'number',
			'css'      => 'width:100px;',
			'desc'     => __( 'Add maximum limit of products', 'products-limit' ),
			'custom_attributes' => array(
				'step' 	=> '1',
				'min' =>'1',
				'max'	=> '100'
			) 
		);


		// Add checkbox option for custom button "Continue Shopping"
		$settings_limit[] = array(
			'name'     => __( 'Enable/disable custom button', 'products-limit' ),
			'desc_tip' => __( 'Check to enable custom button', 'products-limit' ),
			'id'       => 'button_auto_insert',
			'type'     => 'checkbox',
			'desc'     => __( '&#34;Continue Shopping&#34; button in warning banner', 'products-limit' )
		);

		
		
		$settings_limit[] = array( 'type' => 'sectionend', 'id' => 'products_limit_woo' );
		return $settings_limit;
	
	/**
	 * If not, return the standard settings
	 **/
	} else {
		return $settings;
	}
}


/**
 * Add personalized message in the bottom of setting page
*/

add_filter( 'admin_footer_text', 'admin_footer_text' , 5 );
	function admin_footer_text( $footer_text ) {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
	 return;
	}

	$screen = get_current_screen();
	$screen_id = $screen ? $screen->id : '';

	if( $screen_id == 'woocommerce_page_wc-settings' && isset($_GET['section']) && $_GET['section'] == 'products_limit_woo' ) {
		$footer_text = sprintf( __( 'If you like <strong>Product limits for WooCommerce</strong> please leave a %s rating.', 'products-limit' ), '<a href="https://wordpress.org/support/plugin/products-limit-for-woocommerce/reviews/" target="_blank" class="wc-rating-link">&#9733;&#9733;&#9733;&#9733;&#9733;</a>' );
	}
		return $footer_text;
	}		
	
	
