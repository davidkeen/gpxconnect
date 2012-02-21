<?php
/*
	Plugin Name: WPcommunicator
	Plugin URI: https://github.com/davidkeen/WPcommunicator
	Description: Garmin Communicator WordPress plugin.
	Version: 0.1
	Author: David Keen
	Author URI: http://davidkeen.com
*/

// Get the GPX data from a custom field


function gpx_shortcode_handler($atts, $content = null, $code = "" ) {

    // Extract the shortcode arguments into local variables (setting defaults as required)
    extract(shortcode_atts(array('name' => uniqid()), $atts));

    // Create a div to show the import button.
    $ret = '<div id="garminDisplay">&#160;</div>';

    // Write out a javascript variable for the filename
    $ret .= "<script type='text/javascript'>var gpxFilename = $name </script>";

    // Insert the contents of custom field into a hidden text area
    $gpxData = get_post_meta($post->ID, 'gpx', true);
    $ret .= "<textarea id='dataString' style='display:none'>$gpxData</textarea>";

    return $ret;
}



add_shortcode('gpx', 'gpx_shortcode_handler');

?>
