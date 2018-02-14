<?php

namespace BroOptimizeImage\Classes;


use WeDevs_Settings_API;

class OptionPage {

	private $settings_api;

	public static $settings_prefix = "BroOptimizeImage__";

	function __construct() {
		$this->settings_api = new WeDevs_Settings_API;
		add_action( 'admin_init', [$this, 'admin_init'] );
		add_action( 'admin_menu', [$this, 'admin_menu'] );
	}

	function admin_init() {
		//set the settings
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );
		//initialize settings
		$this->settings_api->admin_init();
	}

	function admin_menu() {
		add_options_page(
			'Optimize image settings',
			'Optimize image',
			'activate_plugins',
			'BroOptimizeImage',
			[ $this, 'plugin_page' ]
		);
	}

	function get_settings_sections() {
		$sections = [
			[
				'id'    => self::$settings_prefix . 'api_keys',
				'title' => 'API KEY Settings'
			],
//			[
//				'id'    => self::$settings_prefix . 'advanced',
//				'title' => 'Advanced Settings'
//			]
		];

		return $sections;
	}

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	function get_settings_fields() {
		$settings_fields = [

			self::$settings_prefix . 'api_keys' => [
				[
					'name'        => 'keys',
					'label'       => 'API keys',
					'desc'        => "A Tiny PNG key is required. Get a free key from <a href='https://tinypng.com/developers' target='_blank'>Tiny PNG</a> if needed. (works for both PNG and JPEG)",
					'placeholder' => 'Key:count...',
					'type'        => 'textarea'
				]
			],
//			self::$settings_prefix . 'advanced' => [
//				[
//					'name'  => 'checkbox',
//					'label' => __( 'Checkbox', 'wedevs' ),
//					'desc'  => __( 'Checkbox Label', 'wedevs' ),
//					'type'  => 'checkbox'
//				]
//			]
		];

		return $settings_fields;
	}

	function plugin_page() {
		echo '<div class="wrap">';
		$this->settings_api->show_navigation();
		$this->settings_api->show_forms();
		echo '</div>';
	}

	/**
	 * Get all the pages
	 *
	 * @return array page names with key value pairs
	 */
	function get_pages() {
		$pages         = get_pages();
		$pages_options = [];
		if ( $pages ) {
			foreach ( $pages as $page ) {
				$pages_options[ $page->ID ] = $page->post_title;
			}
		}

		return $pages_options;
	}

}



