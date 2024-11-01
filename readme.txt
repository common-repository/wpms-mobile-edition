=== WordPress Multi Site Mobile Edition ===
Contributors: RavanH
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=WPMS%20Mobile%20Edition&item_number=0%2e4&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8&lc=us
Tags: mobile, pda, wireless, cellphone, phone, iphone, touch, webkit, android, blackberry, carrington, multi site, multisite, multi-site, smooci-2
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 0.4

DISCONTINUED! Makes sites use the Carrington Mobile theme designed for mobile devices when visitors come to any site on your network with a mobile device.

== Description ==

THIS PLUGIN HAS BEEN DISCONTINUED. There are better plugins available that either have more features or, in the case of Jetpack's Mobile module, are easier to manage. Even if none of them are made for Multisite in particular, I feel no immediate need to continue development of this plugin. Besides, switching to one of the many new responsive themes is probably the best approach...

WordPress Multi Site Mobile Edition is a conversion of the famous WordPress Mobile Edition plugin, suitable for WP3+ in both **normal** _and_ **multi-site / network mode**. It lets you easily install one or more themes designed for mobile devices and make WordPress use a mobile theme when visitors come to your site _or any site on your network_ with a mobile device.

See your single site or all sites in your network jump from less than 2 to nearly 5 out of 5 score on [MobiReady](http://ready.mobi/) with any of the compatible mobile themes:
* [Carrington Mobile](http://wordpress.org/extend/themes/carrington-mobile)
* [Smooci-2](http://wordpress.org/extend/themes/smooci-2) 
* [Möbius](http://wordpress.org/extend/themes/mbius)

Mobile browsers are automatically detected. The list of mobile browsers can be customized on either **Super Admin > Mobile** or **Appearance > Mobile** depending on your setup.

TODO: 

- Let Super Admin allow / disallow individual site owners to manage Mobile settings for their own site.

= Plugin/Theme Developers =

If you have a mobile theme hosted on WordPress Extend, please let me know so I can make WPMS Mobile Edition compatible with it. See [Other Notes](http://wordpress.org/extend/plugins/wpms-mobile-edition/other_notes/) for a description of available API action and filter hooks.

= Translations =

None yet... Please submit yours and get mentioned here :)

== Installation ==

Quick installation: [Install now](http://coveredwebservices.com/wp-plugin-install/?plugin=wpms-mobile-edition) !

 &hellip; OR &hellip;

Search for "wpms mobile edition" and install from your slick **Plugins > Add New** back-end page.

 &hellip; OR &hellip;

Follow these steps:

1. Download the archive and either drop the included /wpms-mobile-edition/ directory with its content in your /plugins/ directory or drop only the content of wpms-mobile-edition.php file in your /mu-plugins/ directory.
2. If you installed in /plugins/ you can now choose to either 'Activate' or 'Network Activate' from the main site.
3. Follow the instructions on **Appearance > Mobile** to ensure at least one mobile theme is installed.

Done!

== Frequently Asked Questions ==

= Does this plugin include any mobile themes ? =

No, you need to install one yourself. After plugin activation, you will get instructions for easy automated installation of compatible Mobile themes hosted on WordPress Extend. At this point ( version 0.4 ) this is only Carrington Mobile.

= Is this compatible with the WP plugin auto-upgrade feature? =

Yes.

= Is this compatible with Multi Site mode? =

Yes. It can even be Network Activated or installed in the /mu-plugins/ folder.

= Is this compatible with WP (Super) Cache or others ? =

Yes, it is compatible with **WP Super Cache** 0.9+ (using WP Cache mode). Be sure to activate the Mobile option in WP Super Cache. 

It has also been tested on **Quick Cache**. You need to copy the list of Mobile and Touch User Agents to the _No-Cache User-Agent Patterns section_ to make it work. 

= Does this create a mobile admin interface too? =

No, it does not.

= Does this serve a mobile interface to mobile web search crawlers? =

Yes, to Google and Yahoo Mobile search crawlers. You can add any others by adding their user agents in the plugin's Settings page.

= Does this support iPhones and other "touch" browsers? =

Yes, the mobile theme Carrington Mobile has a customized interface for advanced mobile browsers and special styling to make things "finger-sized" for touch browsers.

= My mobile device isn't automatically detected, what do I do? =

Visit the settings page and use the info there to identify your mobile browser's User Agent. Then add that to the list of mobile browsers in your settings.

= Does this conflict with other iPhone theme plugins? =

Remove the iPhone from the list of detected browsers, then the other iPhone theme should work as normal.

= Can I create a link normal visitors can see the mobile version? =

Yes. The link can be added to your theme by using the wpms_mobile_link() template tag:

`
<?php if (function_exists('wpms_mobile_link')) { wpms_mobile_link(); } ?>
`

This will output HTML code like `<a href="?wpms_mobile=mobile">Mobile Edition</a>` on you blog page.

If you prefer not to edit template files, you can simply insert the **HTML code** (not the template tag code!) into a ordinary Text widget and place that in any sidebar widget area.

When a user follows that link, the mobile version will be displayed with after each post/page content a link back to the **Standard Edition**.

Note: this does not work well if you have WP Cache enabled.

= Why are recent posts shown on every page? =

This is a feature of the Carrington Mobile theme to allow easy access to recent content.

= How do I customize the display of the mobile interface? =

You will need to edit the templates in you mobile theme folder. Any changes you make there will affect the display of the mobile interface. Be aware that these changes will get overwritten when updating the mobile theme.

== Screenshots ==

You can see the mobile theme in action here: http://mobile.carringtontheme.com

== Other Notes ==

If you have a mobile theme hosted on WordPress Extend, please let me know so I can make WPMS Mobile Edition compatible with it.

= API =

The following action and filter hooks are available for interaction with WPMS Mobile Edition.

**FILTERS**

	mobile_browsers
		filters the mobile browsers user agents (array)
	touch_browsers
		filters the touch browsers user agents (array)
	mobile_theme
		filters the mobile theme (theme slug / dirname)
	touch_theme
		filters the touch theme (theme slug / dirname)
	check_mobile
		filters the check mobile ('mobile' , 'touch' or 'desktop')

Example: filter `check_mobile` 
`
function your_mobile_check_function($mobile_status) {
	if ('touch'==$mobile_status)
		$mobile_status = 'mobile';
	return $mobile_status;
}
add_filter('check_mobile', 'your_mobile_check_function');
`
This will force the plugin to use the mobile browser theme instead of the touch browser theme when a touch browser is detected.

**ACTIONS**

	wpmsme_settings_form_top
		action hook at the beginning of the settings page
	wpmsme_settings_form_bottom
		action hook at the end of the settings page

Example: action `wpmsme_settings_form_bottom` allows you to add to the settings page for this plugin. Handling form posts and other activities from anything you add to this form should be done in your plugin.
`
function your_settings_form_bottom() {
	// create your form or output here - don't forget to do register_setting as well or any submitted form data will be lost
}
add_action('wpmsme_settings_form_bottom', 'your_settings_form');
`

== Upgrade Notice ==

= 0.5 =
Added Smooci-2 mobile theme support.

== Changelog ==

= 0.? =
* TODO: add Super Admin options page

= 0.5 =
* added smooci-2 theme support
* sanitize callback improvement

= 0.4 =
* bugfix: mobile link incomplete

= 0.3 =
* improved options sanitation
* moved Mobile settings page to Appearance section
* removed external css and js back-end files

= 0.2 =
* conversion to WP register_setting for options handling
* added carrington-mobile theme installation routine

= 0.1 =
* conversion from original WordPress Mobile Edition for Multi-Site mode


