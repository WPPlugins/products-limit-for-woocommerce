<?php
/*
Plugin Name: Products limit for WooCommerce
Plugin URI: http://iacopocutino.it/products-limit-for-woocommerce/
Description: Allow to set minimum and maximum quantity of products in Woocommerce and display a warning banner in the cart or checkout page.
Author: Iacopo Cutino
Version: 3.2
Domain Path: /languages
Author URI: www.iacopocutino.it
License: GPL2

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (! defined('ABSPATH')) {
	exit();
}

/**
 * Languages support
 **/
function products_lfw_language() {
	load_plugin_textdomain('products-limit', false, dirname( plugin_basename( __FILE__ )) . '/languages/');
}
add_action('init','products_lfw_language');


/**
 * Enqueue css
 **/
function products_lfw_admin_style() {
        wp_register_style( 'style.css', plugins_url('/css/style.css', __FILE__), false, '1.0.0' );
        wp_enqueue_style( 'style.css' );
}
add_action( 'admin_enqueue_scripts', 'products_lfw_admin_style' );



/**
 * Check if WooCommerce is active or if is enabled Multisite
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || array_key_exists('woocommerce/woocommerce.php', get_site_option('active_sitewide_plugins')) ) {

	
// Include WooCommerce settings
include 'settings.php';



// Function for min product in  check out or cart

add_action( 'woocommerce_check_cart_items', 'products_lfw_set_min_num_products' );
function products_lfw_set_min_num_products() {
	// Only run in the Cart or Checkout pages
	if( is_cart() || is_checkout() ) {
		global $woocommerce, $post;

		$min_num_products = get_option( 'number_field' );
		
		$cart_num_products = WC()->cart->cart_contents_count;

		//Variable that contain the shop permalink for the button in the warning banner
	    $return_to  = get_permalink(wc_get_page_id('shop'));
		

	    // Compare values and add an error is Cart's total number of products

		if( $min_num_products != '' && $cart_num_products < $min_num_products ) {
			
			// If the checkbox for button is enabled add the shop button to the warning message in cart or checkout page display our error message

			if(get_option('button_auto_insert') == 'yes') {

	        wc_add_notice( sprintf(__( 'A Minimum of %s products is required before checking out. Current number of items in the cart: %s. <a href="%s" class="button wc-forwards">Continue Shopping</a>', 'products-limit'),
	        	$min_num_products,
	        	$cart_num_products, $return_to ),
	        'error' );

	    	} else {

	    	wc_add_notice( sprintf(__( 'A Minimum of %s products is required before checking out. Current number of items in the cart: %s.', 'products-limit'),
	        	$min_num_products,
	        	$cart_num_products ),
	        'error' );	

	    }

	    }


		}

	}







// Function for max products in check out or cart

add_action( 'woocommerce_check_cart_items', 'products_lfw_set_max_num_products' );
function products_lfw_set_max_num_products() { 
// Only run in the Cart or Checkout pages
if( is_cart() || is_checkout() ) {
		global $woocommerce, $post;

		//variable that contain the shop permalink for the button in the warning banner
		$return_to  = get_permalink(wc_get_page_id('shop'));

		$max_num_products = get_option( 'number_field2' );

		$cart_num_products = WC()->cart->cart_contents_count;

		// Compare values and add an error is Cart's total number of products

		if( $max_num_products != '' && $cart_num_products > $max_num_products ) {

		// If the checkbox for button is enabled add the shop button to the warning message in cart or checkout page display our error message

			if(get_option('button_auto_insert') == 'yes') {

	        wc_add_notice( sprintf(__( 'A Maximum of %s products is allowed before checking out. Current number of items in the cart: %s. <a href="%s" class="button wc-forwards">Continue Shopping</a>', 'products-limit'),
	        	$max_num_products,
	        	$cart_num_products, $return_to ),
	        'error' );
	    
	    	} else {

	    	wc_add_notice( sprintf(__( 'A Maximum of %s products is allowed before checking out. Current number of items in the cart: %s.', 'products-limit'),
	        	$max_num_products,
	        	$cart_num_products),
	        'error' );
	    
	    }

		}

	}
}


	
/**
* Products limit status widget.
*/

function products_lfw_dashboard_widget_function() {
	
	$min_num_products = get_option( 'number_field' );
	
	$max_num_products = get_option( 'number_field2' );
	?>
		<ul class="products-limit-box">
			
			<li class="maximum-count">
			<span class="dashicons dashicons-arrow-up-alt"></span>
			<?php
	
		// Check if maximum limit is enabled
				if ($max_num_products != '') { 
				printf(__( 'You have set a maximum limit of %s products before checking out.', 'products-limit'), $max_num_products );
				} else {
				echo _e('You have not set a maximum limit of products','products-limit');
				}
			?>
			</li>
			
			<li class="minimum-count">
			<span class="dashicons dashicons-arrow-down-alt"></span>
			<?php
	
		// Check if minimum limit is enabled
				if ($min_num_products != '') {
				printf(__( 'You have set a minimum limit of %s products before checking out.', 'products-limit'), $min_num_products );
				} else {
				echo _e('You have not set a minimum limit of products','products-limit');
				}
			?>
			</li>
			
		</ul>		
	<ul class="sub-settings">	
	<li><a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=products&section=products_limit_woo'); ?>">
	<?php printf('Settings','products-limit'); ?></a></li>
	</ul>
	<?php
	
}
	
	
	
// Action hook for status widget
function products_lfw_add_dashboard_widgets() {
	wp_add_dashboard_widget('dashboard_widget', 'Products limit for WooCommerce status', 'products_lfw_dashboard_widget_function');
}
add_action('wp_dashboard_setup', 'products_lfw_add_dashboard_widgets' );
}
