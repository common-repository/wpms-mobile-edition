<?php
/* ------------------------------
 * TODO : WPMSMobileEdition CLASS
 * ------------------------------ */

class WPMSMobileEdition {

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
			'help' => __('BlackBerry, IEMobile, Nintendo Wii, Nokia, Palm, Opera Mini, Playstation Portable, SymbianOS, Windows CE etc. Also include Mobile Search Engines such as Googlebot-Mobile and YahooSeeker/M1A1-R2D2.','wpms-mobile-edition').'<br /><br />'.__('Put every User Agent on a new line. Please do not leave empty lines between them.','wpms-mobile-edition').'<br /><br /><a href="#" id="wpmsme_mobile_reset">'.__('Reset to Default','wpms-mobile-edition').'</a>',
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
			'help' => __('iPhone, Android G1, BlackBerry Storm, etc.','wpms-mobile-edition').'<br /><br />'.__('Put every User Agent on a new line. Please do not leave empty lines between them.','wpms-mobile-edition').'<br /><br /><a href="#" id="wpmsme_touch_reset">'.__('Reset to Default','wpms-mobile-edition').'</a>',
		),
	);

	/* ----------------
	      FUNCTIONS
	   ---------------- */

	function wpmsme_install() {
		global $wpmsme_settings;
		add_option('wpmsme_mobile_browsers', implode("\n", $wpmsme_settings['wpmsme_mobile_browsers']['default']));
		add_option('wpmsme_touch_browsers', implode("\n", $wpmsme_settings['wpmsme_touch_browsers']['default']));
	}

	function wpmsme_init() {
		load_plugin_textdomain('wpms-mobile-edition');

		if ( !get_option('wpmsme_mobile_browsers') || !get_option('wpmsme_touch_browsers') ) 
			wpmsme_install();

		if ( isset($_COOKIE['wpms_mobile']) ) {
			if ( $_COOKIE['wpms_mobile'] == 'false' )
				add_filter( 'the_content', 'wpms_mobile_return' );
			else
				add_filter( 'the_content', 'wpms_mobile_exit' );
		}
	}

	function wpmsme_check_mobile() {
		// check for and return cookie content
		if ( isset($_COOKIE['wpms_mobile']) ) {
			if ( $_COOKIE['wpms_mobile'] == 'true' )
				return apply_filters('check_mobile', true);
			else
				return apply_filters('check_mobile', false);
		}		
	
		if ( wpmsme_mobile_user_agent() )
			return apply_filters('check_mobile', true);
	
		// still here? return false...
		return apply_filters('check_mobile', false);
	}

	function wpmsme_mobile_user_agent() {
		// got a user agent? roll with it!
		if ( isset($_SERVER["HTTP_USER_AGENT"]) ) {
			$mobile = explode("\n", trim(get_option('wpmsme_mobile_browsers')));
			$wpmsme_mobile_browsers = apply_filters('mobile_browsers', $mobile);
			$touch = explode("\n", trim(get_option('wpmsme_touch_browsers')));
			$wpmsme_touch_browsers = apply_filters('touch_browsers', $touch);

			$browsers = array_merge($wpmsme_mobile_browsers, $wpmsme_touch_browsers);

			if (count($browsers))
				foreach ($browsers as $browser) {
					if (!empty($browser) && strpos($_SERVER["HTTP_USER_AGENT"], trim($browser)) !== false) {
						return true;
					}
				}
		}
		return false;
	}

	function wpmsme_template($theme) {
		return apply_filters('mobile_theme', WPMS_MOBILE_THEME);
	}

	function wpmsme_installed() {
		return is_dir(WP_CONTENT_DIR.'/themes/'.apply_filters('mobile_theme', WPMS_MOBILE_THEME));
	}

	function wpms_mobile_exit($content) {
		$content .= '<p><a href="'.wpms_mobile_link('reject_mobile', true ).'">'.__('Standard Edition','wpms-mobile-edition').'</a></p>';
		return $content;
	}

	function wpms_mobile_return($content) {
		$content .= '<p><a href="'.wpms_mobile_link('show_mobile', true ).'">'.__('Mobile Edition','wpms-mobile-edition').'</a></p>';
		return $content;
	}

	function wpms_mobile_link($action = 'show_mobile', $return = false ) {
			$link = isset($_SERVER['REDIRECT_QUERY_STRING']) ? '?'.$_SERVER["REDIRECT_QUERY_STRING"].'&amp;' : '?';
			$link .= 'wpmsme_action='.$action;

		if ($return)
			return $link;
		else
			echo '<a href="'.$link.'">'.__('Mobile Edition','wpms-mobile-edition').'</a>';
	}

	// TODO - add sidebar widget for link, with some sort of graphic?

	function wpmsme_request_handler() { 
		if (!empty($_GET['wpmsme_action'])) {
			$url = parse_url(get_bloginfo('home'));
			$domain = $url['host'];
			if (!empty($url['path'])) {
				$path = $url['path'];
			}
			else {
				$path = '/';
			}
			$redirect = false;
			switch ($_GET['wpmsme_action']) {
				case 'reject_mobile':
					if ( wpmsme_mobile_user_agent() ) {
						setcookie(
							'wpms_mobile'
							, 'false'
							, time() + 300000
							, $path
							, $domain
						);
					} else { // expire cookie when browser is not a mobile 	
						setcookie(
							'wpms_mobile'
							, 'false'
							, time() - 300000
							, $path
							, $domain
						);
					}
					$redirect = true;
					break;
				case 'show_mobile':
					setcookie(
						'wpms_mobile'
						, 'true'
						, time() + 300000
						, $path
						, $domain
					);
					$redirect = true;
					break;
				default:
					break;
			}
			if ($redirect) {
				if (!empty($_SERVER['HTTP_REFERER'])) {
					$go = $_SERVER['HTTP_REFERER']; // so if referrer is another site and link has ?wpmsme_action=show_mobile apended to the URL, visitors will get redirected straight back with only a yummy cookie to show for it !!?? 
				}
				else {
					$go = get_bloginfo('home');
				}
				header('Location: '.$go);
				die();
			}
		}
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
	
		if ( !wpmsme_installed() )
			add_action( 'admin_notices', 'wpmsme_admin_notice' );
	
		wp_enqueue_script('jquery'); // for use with wpmsme_admin_js javascript
	
		foreach ($wpmsme_settings as $key => $config) {
			register_setting( WPMS_MOBILE_SLUG, $key, 'wpmsme_sanitize_callback_'.$config['type'] ); 
		}

		add_action('wpmsme_settings_form_top','wpmsme_admin_header', 0);
		add_action('wpmsme_settings_form_bottom','wpmsme_admin_footer', 0);
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
		$option = get_option($key);
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
		// do_settings_sections(WPMS_MOBILE_SLUG); ??

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
	// do we really need to sanitize when passing options like this through WP internal options routine?
	function wpmsme_sanitize_callback_textarea($setting) {
		$setting = wp_check_invalid_utf8($setting);
		$setting = str_replace("   "," ",$setting);
		$setting = str_replace("  "," ",$setting);
		$setting = str_replace("\r\n \r\n"," ",$setting);
		$setting = str_replace("\r\n\r\n\r\n","\r\n",$setting);
		$setting = str_replace("\r\n\r\n","\r\n",$setting);
		return stripslashes(str_replace("\r\n\r\n","\r\n",$setting));
	}
	function wpmsme_sanitize_callback_int($setting) {
		return intval($setting);
	}
	function wpmsme_sanitize_callback_select($setting) {
		return $setting;
	}

	//if (!function_exists('get_snoopy')) {
	//	function get_snoopy() {
	//		include_once(ABSPATH.'/wp-includes/class-snoopy.php');
	//		return new Snoopy;
	//	}
	//}

	function wpmsme_admin_style() {
		// this will be called only on our plugin admin page, enqueue our stylesheet here
		wp_enqueue_style('wpmsme_admin_css');
	}

	function wpmsme_admin_header() {

		if ( !wpmsme_installed() ) {
			print('
				<div id="message" class="updated">
				<p>'.__('Please install a <a href="#mobile-themes">compatible mobile theme</a>.').'</p>
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
		<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=WPMS%20Mobile%20Edition&item_number=0%2e4&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8&lc=us"><img src="https://www.paypal.com/en_US/i/btn/x-click-but7.gif" style="border:none; vertical-align:text-bottom;float:right" alt="Donate with PayPal - it\'s fast, free and secure!" /></a><h3>'.__('Browsers','wpms-mobile-edition').'</h3>
		<p>'.__('Browsers that have a <a href="http://en.wikipedia.org/wiki/User_agent">User Agent</a> matching any key below will be shown your site using the preset mobile theme of instead of the normal one.', 'wmps-mobile-edition').'</p>
		');	
	}

	function wpmsme_admin_footer() {
		print('
		<p>'.sprintf(__('The User Agent for your current browser: <strong>%s</strong>','wpms-mobile-edition'), strip_tags($_SERVER['HTTP_USER_AGENT'])).'</p>
		<p>'.sprintf(__('<strong>Missing any User Agents in the Default list?</strong> Please contact support on the <a href="%s">WPMS Mobile Edition home page</a>. Thanks!','wpms-mobile-edition'),'http://4visions.nl/en/wordpress-plugins/wpms-mobile-edition/').'</p>
		');

		print('
			<a name="mobile-themes"></a><h3>'.__('Themes','wpms-mobile-edition').'</h3>
			<p>'.__('Install but do not activate the mobile theme.','wpms-mobile-edition').'</p>
			<iframe height="380" width="480" src="theme-install.php?tab=theme-information&theme='.apply_filters('mobile_theme', WPMS_MOBILE_THEME).'"></iframe>
		');
	}

}
