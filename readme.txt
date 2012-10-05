=== GPS Download ===
Contributors: davidkeen
Tags: geo, gpx, gps, navigation, maps, garmin
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Download GPX data to Garmin GPS devices.

== Description ==

This plugin uses the Garmin Communicator Plugin to allow downloading of GPX data to connected Garmin GPS devices.

== Installation ==

1.  Extract the zip file and drop the contents in the wp-content/plugins/ directory of your WordPress installation.
1.  Activate the plugin from Plugins page.
1.  Go to the plugin settings page and enter your Garmin site key.

You can get a Garmin site key from the [Garmin Developer website](http://developer.garmin.com/web-device/garmin-communicator-plugin/get-your-site-key/)

== Frequently Asked Questions ==

= How do I add a download link to a post? =

1.  Create a custom field called 'gpx' with GPX data.
1.  Insert the [gps_download] shortcode into your post. Use the 'name' parameter to specify the filename that is created on the device. This should be unique. Eg, [gpx name=my_route]

== Changelog ==

= 1.0 =
* Enhancement: Improve plugin layout.

= 0.1 =
* Initial release.