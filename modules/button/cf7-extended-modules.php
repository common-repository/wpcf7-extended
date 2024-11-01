<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

	require_once CF7E_PLUGIN_DIR . '/modules/button/cf7-extended-add-button.php';

	/* Shortcode handler */
	add_action( 'wpcf7_init', 'cf7e_add_shortcode_button' );
?>