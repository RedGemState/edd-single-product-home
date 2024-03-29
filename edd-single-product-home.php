<?php
/**
 * Plugin Name: Easy Digital Downloads - Single Product Home
 * Plugin URI:  https://github.com/astoundify/edd-single-product-home
 * Description: Display a single product page as the homepage with Easy Digital Downloads.
 * Author:      Astoundify
 * Author URI:  http://astoundify.com
 * Version:     1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Astoundify_EDD_SPH {

	/**
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Make sure only one instance is only running.
	 *
	 * @since EDD SPH 1.0
	 *
	 * @param void
	 * @return object $instance The one true class instance.
	 */
	public static function instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Start things up.
	 *
	 * @since EDD SPH 1.0
	 *
	 * @param void
	 * @return void
	 */
	public function __construct() {
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		add_action( 'admin_init', array( $this, 'add_settings' ) );
	}

	/**
	 * Force our product to be the homepage.
	 *
	 * @since EDD SPH 1.0
	 *
	 * @param void
	 * @return void
	 */
	public function pre_get_posts( $query ) {
		if ( ! $query->is_main_query() || is_admin() || ! ( $query->is_home || ( isset( $query->query_vars[ 'page_id' ] ) && $query->query_vars[ 'page_id' ] == get_option( 'page_on_front' ) ) ) )
			return;

		$query->set( 'p', get_option( 'edd_sph_product' ) );
		$query->set( 'page_id', 0 );

		$query->is_single = true;
		$query->is_front_page = false;
		$query->is_page = false;
	}

	/**
	 * Add settings to "Settings > Reading"
	 *
	 * @since EDD SPH 1.0
	 *
	 * @param void
	 * @return void
	 */
	public function add_settings() {
		add_settings_section( 'edd_sph', '', '__return_false', 'reading' );

		add_settings_field( 'edd_sph_product', __( 'Single Product Home' ), array( $this, 'settings_field' ), 'reading', 'edd_sph' );

		register_setting( 'reading', 'edd_sph_product', 'intval' );
	}

	/**
	 * Output a list of published downloads.
	 *
	 * @since EDD SPH 1.0
	 *
	 * @param void
	 * @return void
	 */
	public function settings_field() {
		echo EDD()->html->product_dropdown( array(
			'chosen'   => true,
			'name'     => 'edd_sph_product',
			'selected' => get_option( 'edd_sph_product', 0 )
		) );
	}

}
add_action( 'plugins_loaded', array( 'Astoundify_EDD_SPH', 'instance' ) );