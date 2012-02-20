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

    // Extract the shortcode arguments.
    extract(shortcode_atts(array(
            'name' => uniqid()
        ), $atts));



    $gpxData = get_post_meta($post->ID, 'gpx', true);


}



add_shortcode('gpx', 'gpx_shortcode_handler');

?>
