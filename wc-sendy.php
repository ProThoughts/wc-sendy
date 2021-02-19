<?php
/*
Plugin Name: Integration of Sendy with WooCommerce
Plugin URI: http://ProThoughts.com
Description: Add Customers to Sendy List from WooCommerce
Version: 1.0
Author: ProThoughts
Author URI: http://prothoughts.com
Text Domain: wc-sendy
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('PT_wc_sendy')) :

	class PT_wc_sendy
	{

		/**
		 * Construct the plugin.
		 */
		public function __construct()
		{
			add_action('plugins_loaded', array($this, 'init'));
		}

		/**
		 * Initialize the plugin.
		 */
		public function init()
		{
			// Checks if WooCommerce is installed.
			if (class_exists('WC_Integration')) {
				// Include our integration class.
				include_once 'includes/class-pt-wc-sendy.php';

				// Register the integration.
				add_filter('woocommerce_integrations', array($this, 'pt_add_integration'));
			} else {
				// throw an admin error if you like
			}
		}

		/**
		 * Add a new integration to WooCommerce.
		 */
		public function pt_add_integration($integrations)
		{
			$integrations[] = 'PT_wc_sendy_Integration';
			return $integrations;
		}
	}

	$PT_wc_sendy = new PT_wc_sendy(__FILE__);

endif;
