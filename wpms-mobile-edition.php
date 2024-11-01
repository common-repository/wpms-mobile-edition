<?php
/*
Plugin Name: WPMS Mobile Edition 
Plugin URI: http://status301.net/wordpress-plugins/wpms-mobile-edition/ 
Description: Show your mobile visitors a site presentation designed just for them. Cache-friendly, rich experience for iPhone, Android, etc. and clean simple formatting for less capable mobile browsers. Works with multiple mobile themes. <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=WPMS%20Mobile%20Edition&item_number=0%2e4&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8&lc=us">Tip jar</a>.
Text Domain: wpms-mobile-edition
Version: 0.5alpha
Author:  RavanH, Crowd Favorite
Author URI: http://status301.net/

License: GPL http://www.opensource.org/licenses/gpl-license.php
Based on: WordPress Mobile Edition 3.1
*/

// WordPress Mobile Edition
//
// Copyright (c) 2002-2009 Crowd Favorite, Ltd.
// http://crowdfavorite.com
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// *****************************************************************

/* AVAILABLE FILTERS :
	mobile_browsers
		filters the mobile browsers user agents (array)
	touch_browsers
		filters the touch browsers user agents (array)
	mobile_theme
		filters the mobile theme (theme slug / dirname)
	touch_theme
		filters the touch theme (theme slug / dirname)
	check_mobile
		filters the check mobile ('mobile', 'touch' or 'desktop')
*/
/* AVAILABLE ACTIONS
	wpmsme_settings_form_top
		hook at the beginning of the settings page
	wpmsme_settings_form_bottom
		hook at the end of the settings page
*/

/* --------------------------
      CONSTANTS & SETTINGS
   -------------------------- */

define('WPMS_MOBILE_THEME',	'carrington-mobile');	// Default mobile theme (must be installed).
define('WPMS_MOBILE_SLUG',	'wpms-mobile-edition');	// Unique WPMS ME identifier.
define('WPMS_MOBILE_SECTION',	'themes.php');	// Set to options-general.php to move WPMS ME's
							// admin page to the Settings section. Use themes.php
							// for the Appearance section.
define('WPMS_MOBILE_CAN',	'edit_theme_options');	// Minimum user rights to edit WPMS ME options.

$wpmsme_themes = array(
	'mobile' => array(
			'carrington-mobile' => 'Carrington Mobile',
			'smooci-2' => 'Smooci 2'
		),
	'touch' => array(
			'carrington-mobile' => 'Carrington Mobile',
			'mbius' => 'Möbius',
			'smooci-2' => 'Smooci 2',
			'twentyeleven' => 'Twenty Eleven',
			'twentytwelve' => 'Twenty Twelve',
		)
);

$wpmsme_settings = array(
	'wpmsme_mobile_browsers' => array(
		'type' => 'textarea',
		'label' => 'Mobile Browsers',
		'default' => array(
				'2.0 MMP',
				'240x320',
				'400X240',
				'AvantGo',
				'BlackBerry',
				'Blazer',
				'Cellphone',
				'Danger',
				'DoCoMo',
				'Elaine/3.0',
				'EudoraWeb',
				'Googlebot-Mobile',
				'hiptop',
				'IEMobile',
				'KYOCERA/WX310K',
				'LG/U990',
				'MIDP-2.',
				'MMEF20',
				'MOT-V',
				'NetFront',
				'Newt',
				'Nintendo Wii',
				'Nitro', // Nintendo DS
				'Nokia',
				'Opera Mini',
				'Palm',
				'PlayStation Portable',
				'portalmmm',
				'Proxinet',
				'ProxiNet',
				'SHARP-TQ-GX10',
				'SHG-i900',
				'Small',
				'SonyEricsson',
				'Symbian OS',
				'SymbianOS',
				'TS21i-10',
				'UP.Browser',
				'UP.Link',
				'webOS', // Palm Pre, etc.
				'Windows CE',
				'WinWAP',
				'YahooSeeker/M1A1-R2D2',
				),
		'default-theme' => 'carrington-mobile',
		'help' => __('BlackBerry, IEMobile, Nintendo Wii, Nokia, Palm, Opera Mini, Playstation Portable, SymbianOS, Windows CE etc. Also include Mobile Search Engines such as Googlebot-Mobile and YahooSeeker/M1A1-R2D2.','wpms-mobile-edition').'<br /><br />'.__('Put every User Agent on a new line.','wpms-mobile-edition').'<br /><br /><a href="#" id="wpmsme_mobile_reset">'.__('Reset to Default','wpms-mobile-edition').'</a>'
	),
	'wpmsme_touch_browsers' => array(
		'type' => 'textarea',
		'label' => 'Touch Browsers',
		'default' => array(
				'iPhone',
				'iPod',
				'Android',
				'BlackBerry9530',
				'LG-TU915 Obigo', // LG touch browser
				'LGE VX',
				'webOS', // Palm Pre, etc.
				'Nokia5800',
				),
		'default-theme' => 'carrington-mobile',
		'help' => __('iPhone, Android G1, BlackBerry Storm, etc.','wpms-mobile-edition').'<br /><br />'.__('Put every User Agent on a new line.','wpms-mobile-edition').'<br /><br /><a href="#" id="wpmsme_touch_reset">'.__('Reset to Default','wpms-mobile-edition').'</a>'
	)
);

$wpmsme_user_agent = wpmsme_user_agent();

$wpmsme_check = wpmsme_check();


/* ----------------
      FUNCTIONS
   ---------------- */

function wpmsme_init() {
	global $wpmsme_check;
	load_plugin_textdomain('wpms-mobile-edition');
}

function wpmsme_mobile_template($theme) {
	global $wpmsme_settings;
	return apply_filters('mobile_theme', get_option('wpmsme_mobile_template', $wpmsme_settings['wpmsme_mobile_browsers']['default-theme']));
}

function wpmsme_touch_template($theme) {
	global $wpmsme_settings;
	return apply_filters('touch_theme', get_option('wpmsme_touch_template', $wpmsme_settings['wpmsme_touch_browsers']['default-theme']));
}

function wpmsme_desktop_template($theme) {
	return $theme;
}

function wpmsme_theme_installed($theme = false) {
	if (!$theme) {
		global $wpmsme_themes;
		$themes = array_merge($wpmsme_themes['mobile'],$wpmsme_themes['touch']);
		foreach ($themes as $dir => $name)
			if ( is_dir(WP_CONTENT_DIR.'/themes/'.$dir) )
				return true;
		return false;
	} else {
		return is_dir(WP_CONTENT_DIR.'/themes/'.$theme);
	}
}

function wpms_mobile_links($content) {
	global $wpmsme_check, $wpmsme_user_agent;
	switch ( $wpmsme_check ) {
		case 'mobile':
			$content .= '<div style="text-align:center;font-weight:700;padding:8px;white-space:nowrap"><span style="background-color:rgba(255,255,255,0.5);border-radius:18px;background-clip:padding-box;padding:4px 12px;box-shadow:0px 2px 6px rgba(0,0,0,0.4) inset;text-shadow:0px -1px 0px rgb(255, 255, 255);white-space:nowrap;">'.__('Mobile','wpms-mobile-edition').'</span> <a style="background-color:rgba(255,255,255,0.5);text-decoration:none;padding:4px 12px;width:auto;border-radius:18px;background-clip:padding-box;box-shadow:0px -4px 6px rgba(0,0,0,0.4) inset;text-shadow:0px 1px 0px rgb(255, 255, 255);white-space:nowrap;" href="'.wpms_mobile_link('touch', true ).'">'.__('Touch','wpms-mobile-edition').'</a> <a style="background-color:rgba(255,255,255,0.5);text-decoration:none;padding:4px 12px;width:auto;border-radius:18px;background-clip:padding-box;box-shadow:0px -4px 6px rgba(0,0,0,0.4) inset;text-shadow:0px 1px 0px rgb(255, 255, 255);white-space:nowrap;" href="'.wpms_mobile_link('desktop', true ).'">'.__('Standard','wpms-mobile-edition').'</a></div>';
			break;
		case 'touch':
			$content .= '<div style="text-align:center;font-weight:700;padding:8px;white-space:nowrap"><a style="background-color:rgba(255,255,255,0.5);text-decoration:none;padding:4px 12px;width:auto;border-radius:18px;background-clip:padding-box;box-shadow:0px -4px 6px rgba(0,0,0,0.4) inset;text-shadow:0px 1px 0px rgb(255, 255, 255);white-space:nowrap;" href="'.wpms_mobile_link('mobile', true ).'">'.__('Mobile','wpms-mobile-edition').'</a> <span style="background-color:rgba(255,255,255,0.5);border-radius:18px;background-clip:padding-box;padding:4px 12px;box-shadow:0px 2px 6px rgba(0,0,0,0.4) inset;text-shadow:0px -1px 0px rgb(255, 255, 255);white-space:nowrap;">'.__('Touch','wpms-mobile-edition').'</span> <a style="background-color:rgba(255,255,255,0.5);text-decoration:none;padding:4px 12px;width:auto;border-radius:18px;background-clip:padding-box;box-shadow:0px -4px 6px rgba(0,0,0,0.4) inset;text-shadow:0px 1px 0px rgb(255, 255, 255);white-space:nowrap;" href="'.wpms_mobile_link('desktop', true ).'">'.__('Standard','wpms-mobile-edition').'</a></div>';
			break;
		case 'desktop':
			if ( 'desktop' != $wpmsme_user_agent )
				$content .= '<div style="text-align:center;font-weight:700;padding:8px;white-space:nowrap"><a style="background-color:rgba(255,255,255,0.5);text-decoration:none;padding:4px 12px;width:auto;border-radius:18px;background-clip:padding-box;box-shadow:0px -4px 6px rgba(0,0,0,0.4) inset;text-shadow:0px 1px 0px rgb(255, 255, 255);white-space:nowrap;" href="'.wpms_mobile_link('mobile', true ).'">'.__('Mobile','wpms-mobile-edition').'</a> <a style="background-color:rgba(255,255,255,0.5);text-decoration:none;padding:4px 12px;width:auto;border-radius:18px;background-clip:padding-box;box-shadow:0px -4px 6px rgba(0,0,0,0.4) inset;text-shadow:0px 1px 0px rgb(255, 255, 255);white-space:nowrap;" href="'.wpms_mobile_link('touch', true ).'">'.__('Touch','wpms-mobile-edition').'</a> <span style="background-color:rgba(255,255,255,0.5);border-radius:18px;background-clip:padding-box;padding:4px 12px;box-shadow:0px 2px 6px rgba(0,0,0,0.4) inset;text-shadow:0px -1px 0px rgb(255, 255, 255);white-space:nowrap;">'.__('Standard','wpms-mobile-edition').'</span></div>';
			break;
		default:
			break;
	}
	echo $content;
}

function wpms_mobile_link($action = 'mobile', $return = false ) {

	// backward compatibility
	if ( 'show_mobile' == $action )
		$action = 'mobile';
	if ( 'reject_mobile' == $action )
		$action = 'desktop';

	$link = (isset($_SERVER['REDIRECT_QUERY_STRING']) && !$_GET['wpms_mobile']) ? '?'.$_SERVER["REDIRECT_QUERY_STRING"].'&amp;' : '?';
	$link .= 'wpms_mobile='.$action;

	if ($return) {
		return $link;
	} else {
		echo '<a href="'.$link.'">';

		if ( 'desktop' == $action )
			echo __('Standard','wpms-mobile-edition');
		elseif ( 'touch' == $action )
			echo __('Touch','wpms-mobile-edition');
		else
			echo __('Mobile','wpms-mobile-edition');	

		echo '</a>';
	}
}

// TODO - add sidebar widget for links, with some sort of graphic? for now: put links in footer

function wpmsme_user_agent() {
	global $wpmsme_settings, $wpmsme_themes;
	// got a user agent? roll with it!
	if ( isset($_SERVER["HTTP_USER_AGENT"]) ) {
		foreach ( $wpmsme_themes as $agent => $theme ) {
			$browsers = get_option('wpmsme_'.$agent.'_browsers', $wpmsme_settings['wpmsme_'.$agent.'_browsers']['default']);
			if (!is_array($browsers))
				$browsers = explode("\n", $browsers);

			$browsers = apply_filters($agent.'_browsers', $browsers);
			
			if (count($browsers))
				foreach ($browsers as $browser)
					if (!empty($browser) && strpos($_SERVER["HTTP_USER_AGENT"], trim($browser)) !== false)
						return $agent;
		}
		return 'desktop';
	}
	return 'desktop';
}

function wpmsme_setcookie($value, $expire) {
	$url = parse_url(get_bloginfo('home'));
	$domain = $url['host'];
	if (!empty($url['path']))
		$path = $url['path'];
	else
		$path = '/';

	setcookie(
		'wpms_mobile'
		, $value
		, $expire
		, $path
		, $domain
	);
}

function wpmsme_check() {
	// pre-determined user agent
	global $wpmsme_user_agent;
	
	// check request
	if (!empty($_GET['wpms_mobile'])) {
		$expire = time() + 300000;
		switch ($_GET['wpms_mobile']) {
			case 'mobile':
				if ( 'mobile' == $wpmsme_user_agent )
					$expire = time() - 300000;
				wpmsme_setcookie('mobile', $expire);
				return apply_filters('check_mobile', 'mobile');
			case 'touch':
				if ( 'touch' == $wpmsme_user_agent )
					$expire = time() - 300000;
				wpmsme_setcookie('touch', $expire);
				return apply_filters('check_mobile', 'touch');
			case 'desktop':
			case 'false':
			default:
				if ( 'desktop' == $wpmsme_user_agent )
					$expire = time() - 300000;
				wpmsme_setcookie('desktop', $expire);
				return apply_filters('check_mobile', 'desktop');
		}		
	}
	
	// check for and return cookie content
	if ( isset($_COOKIE['wpms_mobile']) ) {
		switch ($_COOKIE['wpms_mobile']) {
			case 'mobile':
				return apply_filters('check_mobile', 'mobile');
				break;
			case 'touch':
				return apply_filters('check_mobile', 'touch');
				break;
			case 'desktop':
			case 'false':
			default:
				return apply_filters('check_mobile', 'desktop');
		}
	}		

	// still here? return classed user agent...
	return apply_filters('check_mobile', $wpmsme_user_agent);
}

// ADMIN PAGE FUNCTIONS

function wpmsme_admin_js() {
	global $wpmsme_settings;
	$mobile = str_replace(array("'","\r", "\n"), array("\'", '', ''), implode('\\n', $wpmsme_settings['wpmsme_mobile_browsers']['default']));
	$touch = str_replace(array("'","\r", "\n"), array("\'", '', ''), implode('\\n', $wpmsme_settings['wpmsme_touch_browsers']['default']));
?>
<script type="text/javascript">
//<![CDATA[
jQuery(function($) {
	$('#wpmsme_mobile_reset').click(function() {
		$('#wpmsme_mobile_browsers').val('<?php echo $mobile; ?>');
		return false;
	});
	$('#wpmsme_touch_reset').click(function() {
		$('#wpmsme_touch_browsers').val('<?php echo $touch; ?>');
		return false;
	});
});
//]]>
</script>
<?php
}

function wpmsme_admin_init() {
	global $wpmsme_settings;
	
	if ( !wpmsme_theme_installed(get_option('wpmsme_mobile_template', $wpmsme_settings['wpmsme_mobile_browsers']['default-theme']))
		|| !wpmsme_theme_installed(get_option('wpmsme_mobile_template', $wpmsme_settings['wpmsme_mobile_browsers']['default-theme'])) )
		add_action( 'admin_notices', 'wpmsme_admin_notice' );
	
	foreach ($wpmsme_settings as $key => $config) {
		register_setting( WPMS_MOBILE_SLUG, $key, 'wpmsme_sanitize_callback_'.$config['type'] ); 
	}

	add_action('wpmsme_settings_form_top','wpmsme_admin_header');
	//add_action('wpmsme_settings_form_bottom','wpmsme_admin_footer');

}

function wpmsme_admin_notice() {
	if ( current_user_can(WPMS_MOBILE_CAN) ) {
		echo '<div class="error"><p>'.sprintf(__('The required mobile theme <strong>%s</strong> is not found.','wpms-mobile-edition'),apply_filters('mobile_theme', WPMS_MOBILE_THEME)).' '.sprintf(__('Please check the <a href="%s">settins</a>.','wpms-mobile-edition'),WPMS_MOBILE_SECTION.'?page='.WPMS_MOBILE_SLUG).'</p></div>';
	}
}

function wpmsme_admin_menu() {
	if (current_user_can(WPMS_MOBILE_CAN)) {
		$page = add_submenu_page(
			WPMS_MOBILE_SECTION
			, __('Mobile Edition', 'wmps-mobile-edition')
			, __('Mobile', 'wmps-mobile-edition')
			, WPMS_MOBILE_CAN
			, WPMS_MOBILE_SLUG
			, 'wpmsme_settings_form'
		);
		/* Using registered $page handle to hook stylesheet, script and action links loading */
        	add_action('admin_print_styles-'.$page, 'wpmsme_admin_style');
        	add_action('admin_head-'.$page, 'wpmsme_admin_js');

	$help = '<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=WPMS%20Mobile%20Edition&item_number=0%2e4&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8&lc=us"><img src="https://www.paypal.com/en_US/i/btn/x-click-but7.gif" style="border:none;float:left;margin:0 10px 10px 0" alt="Donate with PayPal - it\'s fast, free and secure!" /></a>'.__('Browsers that have a <a href="http://en.wikipedia.org/wiki/User_agent">User Agent</a> matching any key below will be shown your site using the preset mobile theme instead of the normal one.', 'wmps-mobile-edition').' 
	'.sprintf(__('The User Agent for your current browser is <strong>%s</strong>.','wpms-mobile-edition'), strip_tags($_SERVER['HTTP_USER_AGENT'])).'</p>
	<p>'.sprintf(__('<strong>Missing any User Agents in the Default list?</strong> Please contact support on the <a href="%s">WPMS Mobile Edition home page</a>. Thanks!','wpms-mobile-edition'),'http://status301.net/wordpress-plugins/wpms-mobile-edition/').'</p>';

        	add_contextual_help($page, $help);

        	/* Using $wpmsme_basename to add plugin action links */
        	add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wpmsme_plugin_action_links');
	}
}

function wpmsme_plugin_action_links($links) {
	$settings_link = '<a href="'.WPMS_MOBILE_SECTION.'?page='.WPMS_MOBILE_SLUG.'">'.__('Settings').'</a>';
	array_unshift($links, $settings_link);
	return $links;
}

function wpmsme_settings_field($key, $config) {
	global $wpmsme_settings;
	$option = get_option($key, $wpmsme_settings[$key]['default']);
	$label = '<tr valign="top"><th scope="row"><label for="'.$key.'">'.$config['label'].'</label></th><td>';
	$help = ' <span class="description">'.$config['help'].'</span></td></tr>';
	switch ($config['type']) {
		case 'select':
			$output = $label.'<select name="'.$key.'" id="'.$key.'">';
			foreach ($config['options'] as $val => $display) {
				$option == $val ? $sel = ' selected="selected"' : $sel = '';
				$output .= '<option value="'.$val.'"'.$sel.'>'.htmlspecialchars($display).'</option>';
			}
			$output .= '</select>'.$help;
			break;
		case 'textarea':
			if (is_array($option)) {
				$option = implode("\n", $option);
			}
			$output = $label.'<textarea name="'.$key.'" id="'.$key.'" style="height:200px;width:300px;vertical-align:text-top;float:left;margin-right:5px;">'.htmlspecialchars($option).'</textarea>'.$help;
			break;
		case 'string':
		case 'int':
		default:
			$output = $label.'<input name="'.$key.'" id="'.$key.'" value="'.htmlspecialchars($option).'" />'.$help;
			break;
	}
	return '<div class="option">'.$output.'<div class="clear"></div></div>';
}

function wpmsme_settings_form() {
	global $wpmsme_settings;
	print('
<div class="wrap">
	<div id="icon-themes" class="icon32"><br /></div>
	<h2>'.__('Mobile Edition', 'wmps-mobile-edition').'</h2>
	');

	do_action('wpmsme_settings_form_top');

	print('
	<form name="wpmsme_settings_form" action="options.php" method="post">
		<table class="form-table"><tr valign="top">
	');
	settings_fields( WPMS_MOBILE_SLUG );

	foreach ($wpmsme_settings as $key => $config) {
		echo wpmsme_settings_field($key, $config);
	}
	print('
		</table>
		<p class="submit">
			<input type="submit" name="Update" class="button-primary" value="'.__('Save Changes').'" />
		</p>
	</form>
	');

	do_action('wpmsme_settings_form_bottom');

	print('
</div>
	');
}

// Sanitize callback routines
function wpmsme_sanitize_callback_textarea($option) {
	// remove invalid utf8 carachters and strip slashes
	// convert to array, filter out empty values and return ...
	return array_filter(explode("\r\n", stripslashes(wp_check_invalid_utf8($option)))); 
}
function wpmsme_sanitize_callback_int($setting) {
	return intval($setting);
}
function wpmsme_sanitize_callback_select($setting) {
	return $setting;
}

function wpmsme_admin_style() {
	// this will be called only on our plugin admin page, enqueue our stylesheet here
	// wp_enqueue_style('wpmsme_admin_css');
	wp_enqueue_style( 'theme-install' );
}

function wpmsme_admin_header() {
	if ( !wpmsme_theme_installed() ) {
		print('
			<div id="message" class="updated">
			<p>'.__('Please install at least one <a href="#mobile-themes">compatible mobile theme</a>.').'</p>
			</div>
		');
	}

	if ( !empty($_GET['updated']) && $_GET['updated'] == "true" && WPMS_MOBILE_SECTION == "themes.php" ) {
		print('
			<div id="message" class="updated">
			<p>'.__('Changes saved.').'</p>
			</div>
		');	
	}

	print('
	<h3><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=WPMS%20Mobile%20Edition&item_number=0%2e4&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8&lc=us"><img src="https://www.paypal.com/en_US/i/btn/x-click-but7.gif" style="border:none;;margin:0 0 10px 10px;float:right" alt="Donate with PayPal - it\'s fast, free and secure!" /></a>'.__('Browsers','wpms-mobile-edition').'</h3>
	');

}

function wpmsme_admin_footer() {
	print('
		<a name="mobile-themes"></a><h3>'.__('Themes','wpms-mobile-edition').'</h3>
	');
		//<p>'.__('Install but <strong>do not activate</strong> your preferred mobile theme.','wpms-mobile-edition').'</p>
	
	//global $wpmsme_themes;
	//$themes = array_merge($wpmsme_themes['mobile'],$wpmsme_themes['touch']);
	/*foreach ($themes as $slug => $name)
		print('
		<iframe height="380" width="480" src="theme-install.php?tab=theme-information&theme='.$slug.'" style="float:left"></iframe>
		');*/
	// TODO : dropdown option lists for mobile and touch browsers with all available themes + 
}

/* ----------------
        HOOKS
   ---------------- */

add_filter('template', 'wpmsme_'.$wpmsme_check.'_template');
add_filter('option_template', 'wpmsme_'.$wpmsme_check.'_template');
add_filter('option_stylesheet', 'wpmsme_'.$wpmsme_check.'_template');

add_action('init', 'wpmsme_init');

add_action('admin_init', 'wpmsme_admin_init');
add_action('admin_menu', 'wpmsme_admin_menu');

add_action('wp_footer','wpms_mobile_links', 0);
