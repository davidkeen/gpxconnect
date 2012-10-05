<?php

/*
 * Copyright 2012 David Keen <david@davidkeen.com>
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
 * Plugin Name: GPS Download
 * Plugin URI: https://github.com/davidkeen/gps-download
 * Description: Garmin Communicator WordPress plugin.
 * Version: 1.0
 * Author: David Keen
 * Author URI: http://davidkeen.com
*/

// Constants
define('GPS_DOWNLOAD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GPS_DOWNLOAD_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Includes
include_once GPS_DOWNLOAD_PLUGIN_DIR . 'includes/GpsDownload.php';

// The main plugin class
$gps = new GpsDownload();

// Actions
add_action('wp_enqueue_scripts', array($gps, 'wp_enqueue_scripts'));
add_action('wp_head', array($gps, 'wp_head'));
add_action('admin_menu', array($gps, 'admin_menu'));
add_action('admin_init', array($gps, 'admin_init'));

// Filters
add_filter('plugin_action_links_' . GPS_DOWNLOAD_PLUGIN_BASENAME, array($gps, 'add_settings_link'));

// Shortcodes
add_shortcode('gps_download', array($gps, 'gps_download_shortcode'));
