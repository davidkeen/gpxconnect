<?php

/**
 * The [gpx] shortcode handler.
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
    $ret .= "<script type='text/javascript'>var gpxFilename = $name; load();</script>";

    // Insert the contents of custom field into a hidden text area
    global $post;
    $gpxData = get_post_meta($post->ID, 'gpx', true);
    $ret .= "<textarea id='dataString' style='display:none'>$gpxData</textarea>";

    return $ret;
}

add_shortcode('gpx', 'wp_communicator_gpx_shortcode_handler');
