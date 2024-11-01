<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once CF7E_PLUGIN_DIR . '/includes/functions.php';

add_action( 'plugins_loaded', 'cf7e_loaded', 9 );

function cf7e_loaded() {
	cf7e_load_modules();
}