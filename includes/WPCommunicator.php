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

class WPcommunicator
{
    function on_activate() {

        $defaultOptions = array(
            'communicator_path' => 'http://example.com',
            'communicator_key' => '',
            'button_text' => 'Download to GPS',
            'after_write_text' => 'Transfer complete');
        add_option('wp_communicator_options', $defaultOptions);

        // Check we have the correct values if the option already existed.
        $options = get_option('wp_communicator_options');
        foreach ($defaultOptions as $key => $val) {
            if (!isset($options[$key]) ) {
                $options[$key] = $defaultOptions[$key];
            }
        }
        update_option('wp_communicator_options', $options);
    }

    function include_javascript() {

        wp_register_script('garmin-device-display', plugins_url('js/communicator-api-1.9/garmin/device/GarminDeviceDisplay.js' , dirname(__FILE__)), array('prototype'), '1.9');

        wp_enqueue_script('prototype');
        wp_enqueue_script('garmin-device-display');
    }

    function wp_head() {

        $options = get_option('wp_communicator_options');

        // Create the new Garmin.DeviceDisplay object
        echo '
            <script type="text/javascript">
            //<![CDATA[
            function load() {
                var display = new Garmin.DeviceDisplay("garminDisplay", {
                    pathKeyPairsArray: ["' . $options['communicator_path'] . '", "' . $options['communicator_key'] . '"],
                    unlockOnPageLoad: false,
                    hideIfBrowserNotSupported: true,
                    showStatusElement: false,
                    autoFindDevices: false,
                    findDevicesButtonText: "' . $options['button_text'] . '",
                    showCancelFindDevicesButton: false,
                    showDeviceSelectOnLoad: false,
                    showDeviceSelectNoDevice: false,
                    autoReadData: false,
                    autoWriteData: true,
                    showReadDataElement: false,
                    useLinks: false,
                    getWriteData: function() { return $("dataString").value; },
                    getWriteDataFileName: function() { return gpxFileName; },
                    afterFinishWriteToDevice: function() { alert("' . $options['after_write_text'] . '"); }
                });
            }
            //]]>
            </script>';
    }

    function add_settings_link($links, $file) {
        static $this_plugin;
        if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

        if ($file == $this_plugin){
            $settings_link = '<a href="admin.php?page=wp-communicator">' . __("Settings", "WPcommunicator") . '</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }

    function admin_menu() {
        global $wpCommunicator;
        add_options_page('WPcommunicator Options', 'WPcommunicator', 'manage_options', 'wp-communicator', array($wpCommunicator, 'options_page'));
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
            <h2>WPcommunicator Settings</h2>
            <form method="post" action="options.php">';

        // Display the hidden fields and handle security.
        settings_fields('wp-communicator-options');

        // Print out all settings sections.
        do_settings_sections('wp-communicator');

        // Finish the settings form.
        echo '
            <input class="button-primary" name="Submit" type="submit" value="Save Changes" />
            </form>
            </div>';
    }

    function admin_init() {
        global $wpCommunicator;

        // Register a setting and its sanitization callback.
        // Parameters are:
        // $option_group - A settings group name. Must exist prior to the register_setting call. (settings_fields() call)
        // $option_name - The name of an option to sanitize and save.
        // $sanitize_callback - A callback function that sanitizes the option's value.
        register_setting('wp-communicator-options', 'wp_communicator_options', array($wpCommunicator, 'validate_options'));

        // Add the 'General Settings' section to the options page.
        // Parameters are:
        // $id - String for use in the 'id' attribute of tags.
        // $title - Title of the section.
        // $callback - Function that fills the section with the desired content. The function should echo its output.
        // $page - The type of settings page on which to show the section (general, reading, writing, media etc.)
        add_settings_section('general', 'General Settings', array($wpCommunicator, 'general_section_content'), 'wp-communicator');


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
    	add_settings_field('communicator_path', 'Site URL', array($wpCommunicator, 'communicator_path_input'), 'wp-communicator', 'general');
    	add_settings_field('communicator_key', 'Key', array($wpCommunicator, 'communicator_key_input'), 'wp-communicator', 'general');
    	add_settings_field('button_text', 'Button text', array($wpCommunicator, 'button_text_input'), 'wp-communicator', 'general');
    	add_settings_field('after_write_text', 'After write text', array($wpCommunicator, 'after_write_text_input'), 'wp-communicator', 'general');
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
     *
     * TODO: Genericise this to take a name param.
     *
     */
    function communicator_path_input() {
        $options = get_option('wp_communicator_options');
    	echo "<input id='communicator_path' name='wp_communicator_options[communicator_path]' size='40' type='text' value='{$options['communicator_path']}' />";
    }

    function communicator_key_input() {
        $options = get_option('wp_communicator_options');
        echo "<input id='communicator_key' name='wp_communicator_options[communicator_key]' size='40' type='text' value='{$options['communicator_key']}' />";
    }

    function button_text_input() {
        $options = get_option('wp_communicator_options');
        echo "<input id='button_text' name='wp_communicator_options[button_text]' size='40' type='text' value='{$options['button_text']}' />";
    }

    function after_write_text_input() {
        $options = get_option('wp_communicator_options');
        echo "<input id='after_write_text' name='wp_communicator_options[after_write_text]' size='40' type='text' value='{$options['after_write_text']}' />";
    }

    function validate_options($input) {
        $options = get_option('wp_communicator_options');

        // Validate communicator_path
        $newCommunicatorPath = trim($input['communicator_path']);
        if (preg_match('#^https?://.+#', $newCommunicatorPath)) {
            $options['communicator_path'] = $newCommunicatorPath;
        } else {
            // How do we display an error?
        }

        // Validate communicator_key
        $newCommunicatorKey = trim($input['communicator_key']);
        if (strlen($newCommunicatorKey) > 32) {
            $options['communicator_key'] = substr($newCommunicatorKey, 0, 32);
        }

        // Validate button_text
        $options['button_text'] = $input['button_text'];

        // Validate after_write_text
        $options['after_write_text'] = $input['after_write_text'];

        return $options;
    }
}


