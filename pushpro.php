<?php
/**
 * Plugin Name:       PushPro | Push Notifications
 * Plugin URI:        https://pushpro.io
 * Description:       Send powerful Push Notifications from your current website, reaching all your visitors directly on their mobile device. Installation is done within a matter of minutes! With our intuitive "What You See Is What You Get" editor, you can create and send a message easy and fast.
 * Version:           1.0.3
 * Author:            PushPro
 * Author URI:        https://www.pushpro.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'PUSHPRO_VERSION', '1.0.3' );

/**
 * plugin name
 */
define( 'PUSHPRO_NAME', 'Pushpro' );

/**
 * plugin slug
 */
define( 'PUSHPRO_SLUG', 'pushpro' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pushpro-activator.php
 */
function activate_pushpro() {
	require_once plugin_dir_path( __FILE__ )
	             . 'includes/class-pushpro-activator.php';
	Pushpro_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pushpro-deactivator.php
 */
function deactivate_pushpro() {
	require_once plugin_dir_path( __FILE__ )
	             . 'includes/class-pushpro-deactivator.php';
	Pushpro_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pushpro' );
register_deactivation_hook( __FILE__, 'deactivate_pushpro' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pushpro.php';

/**
 *  Begins execution of the plugin.
 */
function run_pushpro() {
	$plugin = new Pushpro();
	$plugin->run();
}

run_pushpro();