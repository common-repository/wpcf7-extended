<?php
add_action( 'plugins_loaded', 'cf7e_load_textdomain' );
function cf7e_load_textdomain( $locale = null ) {
	global $l10n;
	$domain = 'wpcf7-extended';
	$mofile = $domain . '-' . get_locale() . '.mo';
	$path = WP_PLUGIN_DIR . '/' . $domain . '/languages';
	if( load_plugin_textdomain( $domain, false, $domain . '/languages/' ) === false ){
		if ( load_textdomain( $domain, $path . '/'. $mofile ) === false ) {
			$mofile = WP_LANG_DIR . '/plugins/' . $mofile;
			load_textdomain( $domain, $mofile );
		}
	}
}


function cf7e_load_modules() {
	$dir = CF7E_PLUGIN_MODULES_DIR;

	if ( empty( $dir ) || ! is_dir( $dir ) ) {
		return false;
	}

	$modules = array( 'confirm', 'button' );
	$modules = apply_filters( 'cf7e_load_modules', $modules );

	foreach ( $modules as $module ) {
		$file = trailingslashit( $dir ) . $module . '/cf7-extended-modules.php';

		if ( file_exists( $file ) ) {
			include_once $file;
		}
	}
}

//add_action('admin_menu', 'cf7e_add_admin_submenu');
function cf7e_add_admin_submenu()
{
    add_submenu_page ('wpcf7', 'CF7 Extended', 'CF7 Extended', 'manage_options', 'wpcf7-cf7e', 'cf7e_setting_page' );
}
 
function cf7e_setting_page()
{
    echo '<h1>Contact Form 7 Extended - '. __('General Settings') .'</h1>';
} 
