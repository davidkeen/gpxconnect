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

class GpsDownload
{
    // Default values for all plugin options.
    // To add a new option just add it to this array.
    private $defaultOptions = array(
        'communicator_path' => 'http://example.com',
        'communicator_key' => '',
        'button_text' => 'Download to GPS',
        'after_write_text' => 'Transfer complete');
    private $options;

    public function __construct() {

        // Set up the options array
        $this->options = get_option('gps_download_options');
        if (!is_array($this->options)) {

            // We don't have any options set yet.
            $this->options = $this->defaultOptions;

            // Save them to the DB.
            update_option('gps_download_options', $this->options);
        } else if (count(array_diff_key($this->defaultOptions, $this->options)) > 0) {

            // The option was set but we don't have all the option values.
            foreach ($this->defaultOptions as $key => $val) {
                if (!isset($this->options[$key]) ) {
                    $this->options[$key] = $this->defaultOptions[$key];
                }
            }

            // Save them to the DB.
            update_option('gps_download_options', $this->options);
        }
    }

    /**
     * The wp_enqueue_scripts action callback.
     * This is the hook to use when enqueuing items that are meant to appear on the front end.
     * Despite the name, it is used for enqueuing both scripts and styles.
     */
    function wp_enqueue_scripts() {
        wp_register_script('garmin-device-display', plugins_url('js/garmin/device/GarminDeviceDisplay.js',
            dirname(__FILE__)), array('prototype'), '1.9');

        // TODO: Do we need to enqueue prototype here if we list it as a dependency above?
        wp_enqueue_script('prototype');
        wp_enqueue_script('garmin-device-display');
    }

    /**
     * The wp_head action callback.
     *
     * Outputs the javascript to show download button.
     *
     * TODO: Can we put this in the footer?
     */
    function wp_head() {

        // TODO: Extract this into a js file and use wp_localize_script

        // Create the new Garmin.DeviceDisplay object
        echo '
            <script type="text/javascript">
            //<![CDATA[
            function load() {
                var display = new Garmin.DeviceDisplay("garminDisplay", {
                    pathKeyPairsArray: ["' . $this->options['communicator_path'] . '", "' . $this->options['communicator_key'] . '"],
                    unlockOnPageLoad: false,
                    hideIfBrowserNotSupported: true,
                    showStatusElement: false,
                    autoFindDevices: false,
                    findDevicesButtonText: "' . $this->options['button_text'] . '",
                    showCancelFindDevicesButton: false,
                    showDeviceSelectOnLoad: false,
                    showDeviceSelectNoDevice: false,
                    autoReadData: false,
                    autoWriteData: true,
                    showReadDataElement: false,
                    useLinks: false,
                    getWriteData: function() { return $("dataString").value; },
                    getWriteDataFileName: function() { return gpxFileName; },
                    afterFinishWriteToDevice: function() { alert("' . $this->options['after_write_text'] . '"); }
                });
            }
            //]]>
            </script>';
    }

    /**
     * The [gps_download] shortcode handler.
     *
     * This shortcode inserts a button to download the GPX data stored in the post's gpx custom field.
     * The 'name' parameter should be used to give a unique filename (without .gpx extension) to store the data in on the device.
     * The 'src' parameter should be used to give the url containing the GPX data.
     * Eg: [gps_download src=http://www.example.com/my_file.gpx name=my_file]
     *
     * @param string $atts an associative array of attributes.
     * @return string the shortcode output to be inserted into the post body in place of the shortcode itself.
     */
    function gps_download_shortcode($atts) {

        // Extract the shortcode arguments into local variables named for the attribute keys (setting defaults as required)
        $defaults = array('name' => uniqid());
        extract(shortcode_atts($defaults, $atts));

        // Create a div to show the import button.
        $ret = '<div id="garminDisplay">&#160;</div>';

        // Write out a javascript variable for the filename and call the load() function.
        $ret .= "<script type='text/javascript'>var gpxFileName = '$name'; load();</script>";

        // Insert the contents of custom field into a hidden text area
        global $post;

        // TODO: change this to file_get_contents()
        $gpxData = get_post_meta($post->ID, 'gpx', true);
        $ret .= "<textarea id='dataString' style='display:none'>$gpxData</textarea>";

        return $ret;
    }

    /**
     * admin_init action callback.
     * Triggered before any other hook when a user access the admin area
     */
    function admin_init() {

        // Register a setting and its sanitization callback.
        // Parameters are:
        // $option_group - A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields().
        // $option_name - The name of an option to sanitize and save.
        // $sanitize_callback - A callback function that sanitizes the option's value.
        register_setting('gps_download_option_group', 'gps_download_options', array($this, 'validate_options'));

        // Add the 'General Settings' section to the options page.
        // Parameters are:
        // $id - String for use in the 'id' attribute of tags.
        // $title - Title of the section.
        // $callback - Function that fills the section with the desired content. The function should echo its output.
        // $page - The type of settings page on which to show the section (general, reading, writing, media etc.)
        add_settings_section('general', 'General Settings', array($this, 'general_section_content'), 'gps_download');


        // Register the options
        // Parameters are:
        // $id - String for use in the 'id' attribute of tags.
        // $title - Title of the field.
        // $callback - Function that fills the field with the desired inputs as part of the larger form.
        //             Name and id of the input should match the $id given to this function. The function should echo its output.
        // $page - The type of settings page on which to show the field (general, reading, writing, ...).
        // $section - The section of the settings page in which to show the box (default or a section you added with add_settings_section,
        //            look at the page in the source to see what the existing ones are.)
        // $args - Additional arguments
        add_settings_field('communicator_path', 'Site URL', array($this, 'communicator_path_input'), 'gps_download', 'general');
        add_settings_field('communicator_key', 'Key', array($this, 'communicator_key_input'), 'gps_download', 'general');
        add_settings_field('button_text', 'Button text', array($this, 'button_text_input'), 'gps_download', 'general');
        add_settings_field('after_write_text', 'After write text', array($this, 'after_write_text_input'), 'gps_download', 'general');
    }

    /**
     * Filter callback to add a link to the plugin's settings.
     *
     * @param $links
     * @return array
     */
    function add_settings_link($links) {
        $settings_link = '<a href="options-general.php?page=gps_download">' . __("Settings", "GPS Download") . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * admin_menu action callback.
     */
    function admin_menu() {
        add_options_page('GPS Download Options', 'GPS Download', 'manage_options', 'gps_download', array($this, 'options_page'));
    }

    /**
     * Creates the plugin options page.
     * See: http://ottopress.com/2009/wordpress-settings-api-tutorial/
     * And: http://codex.wordpress.org/Settings_API
     */
    function options_page() {

        // AUthorised?
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Start the settings form.
        echo '
            <div class="wrap">
            <h2>GPS Download Settings</h2>
            <form method="post" action="options.php">';

        // Display the hidden fields and handle security.
        settings_fields('gps_download_option_group');

        // Print out all settings sections.
        do_settings_sections('gps_download');

        // Finish the settings form.
        echo '
            <input class="button-primary" name="Submit" type="submit" value="Save Changes" />
            </form>
            </div>';
    }

    /**
     * Fills the section with the desired content. The function should echo its output.
     */
    function general_section_content() {
        // Nothing to see here.
    }

    /**
     * Fills the field with the desired inputs as part of the larger form.
     * Name and id of the input should match the $id given to this function. The function should echo its output.
     *
     * Name value must start with the same as the id used in register_setting.
     */
    function communicator_path_input() {
    	echo "<input id='communicator_path' name='gps_download_options[communicator_path]' size='40' type='text' value='{$this->options['communicator_path']}' />";
    }

    function communicator_key_input() {
        echo "<input id='communicator_key' name='gps_download_options[communicator_key]' size='40' type='text' value='{$this->options['communicator_key']}' />";
    }

    function button_text_input() {
        echo "<input id='button_text' name='gps_download_options[button_text]' size='40' type='text' value='{$this->options['button_text']}' />";
    }

    function after_write_text_input() {
        echo "<input id='after_write_text' name='gps_download_options[after_write_text]' size='40' type='text' value='{$this->options['after_write_text']}' />";
    }

    function validate_options($input) {

        // Validate communicator_path
        $newCommunicatorPath = trim($input['communicator_path']);
        if (preg_match('#^https?://.+#', $newCommunicatorPath)) {
            $this->options['communicator_path'] = $newCommunicatorPath;
        } else {
            // How do we display an error?
        }

        // Validate communicator_key
        $newCommunicatorKey = trim($input['communicator_key']);
        if (strlen($newCommunicatorKey) > 32) {
            $this->options['communicator_key'] = substr($newCommunicatorKey, 0, 32);
        } else {
            $this->options['communicator_key'] = $newCommunicatorKey;
        }

        // Validate button_text
        $this->options['button_text'] = $input['button_text'];

        // Validate after_write_text
        $this->options['after_write_text'] = $input['after_write_text'];

        return $this->options;
    }
}


