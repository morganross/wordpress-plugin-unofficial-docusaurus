<?php
/**
 * Plugin Name: Unofficial Docusaurus for WordPress
 * Plugin URI:  https://github.com/morganross/wordpress-plugin-unofficial-docusaurus
 * Description: A WordPress-native docs plugin that keeps the site shell while rendering markdown docs with a small React docs UI.
 * Version:     0.1.0
 * Author:      Morgan Ross
 * License:     GPL-2.0-or-later
 * Text Domain: unofficial-docusaurus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'UDOC_VERSION', '0.1.0' );
define( 'UDOC_PLUGIN_FILE', __FILE__ );
define( 'UDOC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UDOC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once UDOC_PLUGIN_DIR . 'includes/class-assets.php';
require_once UDOC_PLUGIN_DIR . 'includes/class-doc-registry.php';
require_once UDOC_PLUGIN_DIR . 'includes/class-doc-renderer.php';
require_once UDOC_PLUGIN_DIR . 'includes/class-router.php';
require_once UDOC_PLUGIN_DIR . 'includes/class-shortcode.php';
require_once UDOC_PLUGIN_DIR . 'includes/class-plugin.php';

UDOC_Plugin::init();
