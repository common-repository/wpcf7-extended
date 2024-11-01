<?php
/*
Plugin Name: WPCF7 Extended
Plugin URI: https://wordpress.org/plugins/wpcf7-extended/
Description: WPCF7拡張はお問い合わせフォーム7を追加できます確認, に確認画面を追加するプラグインです。 Extensions for Contact Form 7 Plugin: add confirm step. 
Author: TUTM
Author URI: https://tutm.dev/products/wpcf7-extended-contact-form-7-add-confirm/
Text Domain: wpcf7-extended
Domain Path: /languages/
Version: 1.0.6
*/

/* Copyright 2021 - TUTM */

if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
	return false;
}

define( 'CF7E_VERSION', '1.0.6' );

if ( ! defined( 'CF7E_PLUGIN_BASENAME' ) )
	define( 'CF7E_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'CF7E_PLUGIN_NAME' ) )
	define( 'CF7E_PLUGIN_NAME', trim( dirname( CF7E_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'CF7E_PLUGIN_DIR' ) )
	define( 'CF7E_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );

if ( ! defined( 'CF7E_PLUGIN_URL' ) )
	define( 'CF7E_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

if ( ! defined( 'CF7E_PLUGIN_PATH' ) )
define( 'CF7E_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

if ( ! defined( 'CF7E_PLUGIN_MODULES_DIR' ) )
	define( 'CF7E_PLUGIN_MODULES_DIR', CF7E_PLUGIN_DIR . '/modules' );

require_once CF7E_PLUGIN_DIR . '/wpcf7-extended-setting.php';