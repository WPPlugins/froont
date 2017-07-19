<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://froont.com
 * @since             1.0.0
 * @package           Froont
 *
 * @wordpress-plugin
 * Plugin Name:       Froont for WordPress
 * Plugin URI:        http://froont.com
 * Description:       Publish your <a href="http://froont.com">Froont</a> projects as posts or pages.
 * Version:           1.0.0
 * Author:            Froont
 * Author URI:        http://froont.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       froont
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-froont-activator.php
 */
function activate_froont() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-froont-activator.php';
	Froont_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-froont-deactivator.php
 */
function deactivate_froont() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-froont-deactivator.php';
	Froont_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_froont' );
register_deactivation_hook( __FILE__, 'deactivate_froont' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-froont.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_froont() {

	$plugin = new Froont();
	$plugin->run();

}
run_froont();
