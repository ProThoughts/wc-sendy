<?php

/**
 * Add Customers to Sendy List from WooCommerce
 *
 * @package  PT_wc_sendy_Integration
 * @category Integration
 * @author   ProThoughts
 */

if (!class_exists('PT_wc_sendy_Integration')) :

	class PT_wc_sendy_Integration extends WC_Integration
	{

		/**
		 * Init and hook in the integration.
		 */
		public function __construct()
		{
			global $woocommerce;

			$this->id                 = 'pt-woocommerce-sendy-settings';
			$this->method_title       = __('Sendy Settings', 'pt-woocommerce-sendy-settings');
			$this->method_description = __('Adds WooCommerce Customers to Sendy List', 'pt-woocommerce-sendy-settings');

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables.
			$this->sendy_url         = $this->get_option('sendy_url');
			$this->sendy_list        = $this->get_option('sendy_list');
			$this->sendy_api_key     = $this->get_option('sendy_api_key');

			// Actions.
			add_action('woocommerce_update_options_integration_' .  $this->id, array(&$this, 'process_admin_options'));
			add_action('woocommerce_order_status_completed', array(&$this, 'add_to_sendy_mailer'));
		}


		/**
		 * Initialize integration settings form fields.
		 *
		 * @return void
		 */
		public function init_form_fields()
		{
			$this->form_fields = array(
				'sendy_url' => array(
					'title'             => __('Sendy URL', 'woocommerce-integration-demo'),
					'type'              => 'text',
					'description'       => __('URL of your Sendy installtion', 'woocommerce-integration-demo'),
					'desc_tip'          => true,
					'default'           => ''
				),
				'sendy_api_key' => array(
					'title'             => __('Sendy API Key', 'woocommerce-integration-demo'),
					'type'              => 'text',
					'default'           => '',
					'desc_tip'          => true,
					'description'       => __('Add your Sendy API Key', 'woocommerce-integration-demo'),
				),
				'sendy_list' => array(
					'title'             => __('Sendy List ID', 'woocommerce-integration-demo'),
					'type'              => 'text',
					'default'           => '',
					'desc_tip'          => true,
					'description'       => __('Add ID of your Sendy list', 'woocommerce-integration-demo'),
				),
				/*'customize_button' => array(
				'title'             => __( 'Customize!', 'woocommerce-integration-demo' ),
				'type'              => 'button',
				'custom_attributes' => array(
					'onclick' => "location.href='http://www.woothemes.com'",
				),
				'description'       => __( 'Customize your settings by going to the integration site directly.', 'woocommerce-integration-demo' ),
				'desc_tip'          => true,
				)*/
			);
		}


		public function add_to_sendy_mailer($order_id)
		{
			global $woocommerce;
			$order = new WC_Order($order_id);
			$url = rtrim($this->sendy_url, "/");
			$boolean = 'true';
			/*
			$opts = array('http' => array('method'  => 'POST', 'header'  => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
			$context  = stream_context_create($opts);
			$result = file_get_contents($url.'/subscribe', false, $context);
			*/
			$sendy_data = array(
				'name' => $order->billing_first_name . ' ' . $order->billing_last_name,
				'email' => $order->billing_email,
				'list' => $this->sendy_list,
				'api_key' => $this->sendy_api_key,
				'boolean' => 'true',
			);

			$sendy_url = $url . '/subscribe';

			$result = wp_remote_post($sendy_url, array('body' => $sendy_data));
			$result = $result['body'];

			if ($result == "1") {
				$order->add_order_note('Sendy: Customer ' . $order->billing_email . ' added to the list');
			} elseif ($result == "Already subscribed.") {
				$order->add_order_note('Sendy: Customer ' . $order->billing_email . ' is already in the list');
			} else {
				$order->add_order_note('Sendy: Failed to add ' . $order->billing_email . ' in the list. Error: ' . $result);
			}
			return $order_status;
		}

		/**
		 * Santize our settings
		 * @see process_admin_options()
		 */
		public function sanitize_settings($settings)
		{

			return $settings;
		}
	}

endif;
