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

/**
 * The [gpx] shortcode handler.
 *
 * This shortcode inserts a button to download the GPX data stored in the post's gpx custom field.
 * The 'name' parameter should be used to give a unique filename (without .gpx extension) to store the data in on the device.
 * Eg: [gpx name="my_file"]
 *
 * @param string $atts shortcode attributes.
 * @param string $content content between shortcode tags.
 * @param string $code
 * @return string
 */
function wp_communicator_gpx_shortcode_handler($atts, $content = null, $code = "" ) {

    // Extract the shortcode arguments into local variables (setting defaults as required)
    extract(shortcode_atts(array('name' => uniqid()), $atts));

    // Create a div to show the import button.
    $ret = '<div id="garminDisplay">&#160;</div>';

    // Write out a javascript variable for the filename
    $ret .= "<script type='text/javascript'>var gpxFileName = '$name'; load();</script>";

    // Insert the contents of custom field into a hidden text area
    global $post;
    $gpxData = get_post_meta($post->ID, 'gpx', true);
    $ret .= "<textarea id='dataString' style='display:none'>$gpxData</textarea>";

    return $ret;
}

add_shortcode('gpx', 'wp_communicator_gpx_shortcode_handler');
