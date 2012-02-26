<?php

/*
 * Copyright 2012 David Keen <david@sharedmemory.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/*
 * Plugin Name: WPcommunicator
 * Plugin URI: https://github.com/davidkeen/WPcommunicator
 * Description: Garmin Communicator WordPress plugin.
 * Version: 0.1
 * Author: David Keen
 * Author URI: http://davidkeen.com
*/

include_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
include_once plugin_dir_path(__FILE__) . 'includes/WPcommunicator.php';

$wpCommunicator = new WPcommunicator();

// Hooks
register_activation_hook(__FILE__, array($wpCommunicator, 'on_activate'));
register_uninstall_hook(__FILE__, array($wpCommunicator, 'on_uninstall'));

// Actions
add_action('wp_enqueue_scripts', array($wpCommunicator, 'include_javascript'));
add_action('wp_head', array($wpCommunicator, 'wp_head'));
add_action('admin_menu', array($wpCommunicator, 'admin_menu'));
add_action('admin_init', array($wpCommunicator, 'admin_init'));