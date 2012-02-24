<?php
/*
	Plugin Name: WPcommunicator
	Plugin URI: https://github.com/davidkeen/WPcommunicator
	Description: Garmin Communicator WordPress plugin.
	Version: 0.1
	Author: David Keen
	Author URI: http://davidkeen.com
*/

include_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
include_once plugin_dir_path(__FILE__) . 'includes/WPCommunicator.php';

$wpCommunicator = new WPCommunicator();

register_activation_hook(__FILE__, array($wpCommunicator, 'on_activate'));
register_uninstall_hook(__FILE__, array($wpCommunicator, 'on_uninstall'));

add_action('wp_enqueue_scripts', array($wpCommunicator, 'include_javascript'));
add_action('wp_head', array($wpCommunicator, 'wp_head'));
add_action('admin_menu', array($wpCommunicator, 'admin_menu'));
add_action('admin_init', array($wpCommunicator, 'admin_init'));