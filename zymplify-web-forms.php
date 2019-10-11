<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              zymplify-web-form
 * @since             1.0.0
 * @package           Zymplify_Web_Forms
 *
 * @wordpress-plugin
 * Plugin Name:       Zymplify Web Forms 
 * Plugin URI:        zymplify-web-form
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Asad
 * Author URI:        zymplify-web-form
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zymplify-web-form
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ZYMPLIFY_WEB_FORM_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-zymplify-web-form-activator.php
 */
function activate_zwf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-restapi.php';
	Zymplify_Web_Forms_Activator::activate();
	Zymplify_Web_Forms_RestApi::activate();

	// run script after activation
	wp_enqueue_script( 'bts', plugin_dir_url( __FILE__ ) . 'admin/js/bts.js', array(), false, false );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-zymplify-web-form-deactivator.php
 */
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
function deactivate_zwf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	Zymplify_Web_Forms_Deactivator::deactivate();
}

// Active will work from Authenticate Button
// register_activation_hook( __FILE__, 'activate_zwf' );
register_deactivation_hook( __FILE__, 'deactivate_zwf' );
register_uninstall_hook( __FILE__, 'deactivate_zwf' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-forms.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_zymplify_web_forms() {

	$plugin = new ZWF_Forms();
	$plugin->run();

}
run_zymplify_web_forms();