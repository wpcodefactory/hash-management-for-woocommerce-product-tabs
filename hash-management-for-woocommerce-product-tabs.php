<?php
/*
Plugin Name: Hash management for WooCommerce product tabs
Description: Allow the WooCommerce product tabs to interact with the URL hash.
Version: 1.0.0
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: hash-management-for-woocommerce-product-tabs
Domain Path: /langs
WC tested up to: 6.4
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'hmwct_is_plugin_active' ) ) {
	/**
	 * hmwct_is_plugin_active.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function hmwct_is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}
}

// Check for active plugins
if ( ! hmwct_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}

/**
 * hmwct_add_hash_management_to_product_tabs.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
function hmwct_add_hash_management_to_product_tabs() {
	if ( is_product() ) {
		$settings = apply_filters( 'hmwct_settings', array(
			'change_hash_on_tab_click' => true,
			'open_tab_on_hash_change'  => true,
			'open_tab_on_page_load'    => false
		) );
		?>
		<script>
			jQuery(document).ready(function ($) {
				let settings = JSON.parse('<?php echo wp_json_encode( $settings )?>');
				let script = {
					data: null,
					init: function () {
						// Change hash on tab click.
						if (this.data.change_hash_on_tab_click) {
							$(".tabs a").click(function () {
								window.location.hash = $(this).attr('href');
							});
						}
						// Open tab on page load.
						if (this.data.open_tab_on_page_load && window.location.hash) {
							script.changeTabByHash(window.location.hash);
						}
						// Open tab on hash change.
						if (this.data.open_tab_on_hash_change) {
							$(window).on('hashchange', function () {
								script.changeTabByHash(window.location.hash);
							});
						}
					},
					changeTabByHash: function (hash) {
						if (hash) {
							let aFromTab = $(".tabs a[href='" + hash + "']");
							if (aFromTab.length && !aFromTab.parent().hasClass('active')) {
								aFromTab.trigger('click');
							}
						}
					}
				};
				script.data = settings;
				script.init();
			});
		</script>
		<?php
	}
}

add_action( 'wp_footer', 'hmwct_add_hash_management_to_product_tabs', 30 );