=== Safe Editor ===
Contributors: 
Donate link: https://www.paypal.com/us/cgi-bin/webscr?cmd=_flow&SESSION=nIyyyhMYEuiGkrlM9jvdIijCINVW4xHXKQBFh-UGEnQU2K_q5B5cV7qc4vC&dispatch=5885d80a13c0db1f8e263663d3faee8d5402c249c5a2cfd4a145d37ec05e9a5e
Tags: css changes, javascript chnages, editor, updates
Requires at least: 3.8
Tested up to: 3.9.1
Stable tag: trunk/plugin
License: GPL2

Add custom css/javascript to your website without worrying that your changes will be overwritten with the future theme/plugin updates.

== Description ==

Safe Editor allows you to write custom CSS / Javascript to manipulate the appearance and behavior of themes / plugins on your website without worrying that your changes will be overwritten with the future theme / plugin updates. 

== Installation ==

You can install this plugin via the admin's Plugins screen, or...

	1. Upload the `safe-editor` directory to the `/wp-content/plugins/` directory.
	2. Activate the plugin through the 'Plugins' menu in WordPress.

== Support ==

konrad@forde.pl

== Screenshots ==

1. CSS editor tab

== Change Log ==

= 1.1 =
* Solarized light theme for css and javascript editor added

= 1.0 =
* First release.


== Upgrade Notice ==


== Frequently Asked Questions ==

1. I installed the Safe Editor, where can I edit the css & javascript?
    - The Safe Editor settings panel is located under Tools/Safe Editor in your WordPress admin panel.

2. The javascript I write in the editor is not being executed. Why?
    - Make sure you have the wp_footer() hook in your footer.php file (in your theme).

3. I'm geting the '$ is not defined' error on frontend when I use the  javascript editor.
     - If you are writing jQuery in the javascript editor make sure you wrap it in 'jQuery(document).ready(function( $ ) { YOURCODE });"