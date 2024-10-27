<?php
/*
Plugin Name: 3xpush - Push subscribing and Sending (HTTPS)
Plugin URI: https://3xpush.com/page.php?section=3
Description: Connecting a subscription to a push notification, sending to subscribers according to the conditions, additional monetization of subscriptions through advertising, the ability to disable monetization, traffic exchange.
Version: 1.0
Author: Evgenii Zakhodyakin
Author URI: https://3xpush.com
License: GPLv2 or later
Domain Path: /languages/
 */

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
	exit;
}

define('PLUGIN_XPUSH_VERSION', '1.1.0' );
define('PLUGIN_XPUSH_PATH', plugin_dir_path(dirname(__FILE__).'/3xpush/'));
define('PLUGIN_XPUSH_URL', plugin_dir_url( __FILE__ ));


function xpush_plugin_activate() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-xpush-activator.php';
    $activator = new xPush_Activator_Class;
    $activator->xpush_activate();
}

function xpush_plugin_deactivate() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-xpush-deactivator.php';
    $deactivator = new xPush_Deactivator_Class();
    $deactivator->xpush_deactivate();
}
add_action( 'plugins_loaded', function(){
	load_plugin_textdomain( '3xpush', false, dirname(plugin_basename(__FILE__)) . '/languages' );
});

register_activation_hook( __FILE__, 'xpush_plugin_activate' );
register_deactivation_hook( __FILE__, 'xpush_plugin_deactivate' );

require plugin_dir_path( __FILE__ ) . 'includes/class-xpush.php';

function xpush_run_plugin() {
	$xpush = new xPush_Main_Class();
	$xpush->xpush_run();
}

xpush_run_plugin();
