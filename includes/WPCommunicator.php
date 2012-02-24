<?php

class WPCommunicator
{
    function on_activate() {

        $options = array(
            'communicator_path' => 'http://example.com',
            'communicator_key' => '',
            'button_text' => 'Download to GPS',
            'after_write_text' => 'Transfer complete');
        add_option('wp_communicator_options', $options);

        // Check we have the correct values if the option already existed.
        $wp_communicator_options = get_option('wp_communicator_options');
        foreach ($options as $key => $val) {
            if (!isset($wp_communicator_options[$key]) ) {
                $wp_communicator_options[$key] = $options[$key];
            }
        }
        update_option('wp_communicator_options', $wp_communicator_options);
    }

    function on_uninstall() {
        delete_option('wp_communicator_options');
    }


    function include_javascript() {

        wp_register_script('garmin-device-display', 'http://developer.garmin.com/web/communicator-api/garmin/device/GarminDeviceDisplay.js', array('prototype'), '1.9');

        wp_enqueue_script('prototype');
        wp_enqueue_script('garmin-device-display');
    }

    function wp_head() {

        $wp_communicator_options = get_option('wp_communicator_options');

        // Create the new Garmin.DeviceDisplay object
        echo '
            <script type="text/javascript">
            //<![CDATA[
            function load() {
                var display = new Garmin.DeviceDisplay("garminDisplay", {
                    pathKeyPairsArray: ["' . $wp_communicator_options['communicator_path'] . '", "' . $wp_communicator_options['communicator_key'] . '"],
                    unlockOnPageLoad: false,
                    hideIfBrowserNotSupported: true,
                    showStatusElement: false,
                    autoFindDevices: false,
                    findDevicesButtonText: "' . $wp_communicator_options['button_text'] . '",
                    showCancelFindDevicesButton: false,
                    showDeviceSelectOnLoad: false,
                    showDeviceSelectNoDevice: false,
                    autoReadData: false,
                    autoWriteData: true,
                    showReadDataElement: false,
                    useLinks: false,
                    getWriteData: function() { return $("dataString").value; },
                    getWriteDataFileName: function() { return gpxFileName; },
                    afterFinishWriteToDevice: function() { alert("' . $wp_communicator_options['after_write_text'] . '"); }
                });
            }
            //]]>
            </script>';
    }

    function admin_menu() {
        global $wpCommunicator;
        add_options_page('WPcommunicator Options', 'WPcommunicator', 'manage_options', __FILE__, array($wpCommunicator, 'options_page'));
    }

    function options_page() {
        echo '
            <div class="wrap">
            <div id="icon-plugins" class="icon32"></div><br /><h2>WPcommunicator Settings</h2>
            <form method="post" action="options.php">';

        settings_fields('wp-communicator-options');
        do_settings_sections('plugin');

        echo '
            <p class="submit">
            <input type="submit" class="button-primary" value="' . _e('Save Changes') . '" />
            </p>
            </form>
            </div>';
    }

    function admin_init() {
        register_setting('wp-communicator-options', 'plugin_options', 'plugin_options_validate' );
        add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
    	add_settings_field('plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main');
    }
}


