<?php

class WPCommunicator
{
    /**
     * Runs when the plugin is activated - creates options etc.
     */
    function register_activation() {

        $options = array(
            'communicator_path' => 'http://example.com',
            'communicator_key' => '',
            'button_text' => 'Download to GPS',
            'after_write_text' => 'Transfer complete');
        add_option('wp_communicator_options', $options);

        $wp_communicator_options = get_option('wp_communicator_options');
        foreach ($options as $key => $val) {
            if (!isset($wp_communicator_options[$key]) ) {
                $wp_communicator_options[$key] = $options[$key];
            }
        }
        update_option('wp_communicator_options', $wp_communicator_options);
    }


    /**
     * Include required javascript.
     */
    function include_javascript() {

        wp_register_script('garmin-device-display', 'http://developer.garmin.com/web/communicator-api/garmin/device/GarminDeviceDisplay.js', array('prototype'), '1.9');

        wp_enqueue_script('prototype');
        wp_enqueue_script('garmin-device-display');
    }

    function wp_head() {
        $wp_communicator_options = get_option('wp_communicator_options');
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
}


