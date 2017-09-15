<?php
/**
 * ************************************************************
 *
 * @package adblock-notify
 * SECURITY : Exit if accessed directly
 ***************************************************************/
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct acces not allowed!' );
}
/**
 * ************************************************************
 * Insert elements in the DOM : HTML & SCRIPT
 ***************************************************************/
function an_prepare() {
	if ( an_check_views() ) {
		return;
	}
	$an_option = TitanFramework::getInstance( 'adblocker_notify' );
	$output    = '';
	// Retrieve options
	// General Options
	$anOptionChoice       = $an_option->getOption( 'an_option_choice' );
	$anOptionStats        = $an_option->getOption( 'an_option_stats' );
	$anOptionSelectors    = $an_option->getOption( 'an_option_selectors' );
	$anOptionAdsSelectors = $an_option->getOption( 'an_option_ads_selectors' );
	$anOptionCookie       = $an_option->getOption( 'an_option_cookie' );
	$anOptionCookieLife   = $an_option->getOption( 'an_option_cookie_life' );
	$anPageRedirect       = $an_option->getOption( 'an_page_redirect' );
	$anPageNojsActivation = $an_option->getOption( 'an_page_nojs_activation' );
	$anPageNojsRedirect   = $an_option->getOption( 'an_page_nojs_redirect' );
	// Modal Options
	$anOptionModalEffect    = $an_option->getOption( 'an_option_modal_effect' );
	$anOptionModalSpeed     = $an_option->getOption( 'an_option_modal_speed' );
	$anOptionModalClose     = $an_option->getOption( 'an_option_modal_close' );
	$anOptionModalBgcolor   = $an_option->getOption( 'an_option_modal_bgcolor' );
	$anOptionModalBgopacity = $an_option->getOption( 'an_option_modal_bgopacity' );
	$anOptionModalBxcolor   = $an_option->getOption( 'an_option_modal_bxcolor' );
	$anOptionModalBxtext    = $an_option->getOption( 'an_option_modal_bxtext' );
	$anOptionModalCustomCSS = $an_option->getOption( 'an_option_modal_custom_css' );
	$anOptionModalShowAfter = $an_option->getOption( 'an_option_modal_after_pages' );
	$anPageMD5              = '';
	$anSiteID               = 0;
	if ( ! $anOptionModalShowAfter ) {
		$anOptionModalShowAfter = 0;
	} else {
		$anOptionModalShowAfter = intval( $anOptionModalShowAfter );
		$anPageMD5              = md5( $_SERVER['REQUEST_URI'] );
		$anSiteID               = an_is_pro() && is_multisite() ? get_current_blog_id() : 0;
		if ( ! an_is_pro() && is_multisite() ) {
			// if only free is active on a multsite, disable modal per X pages behavior
			$anOptionModalShowAfter = 0;
		}
	}
	// Modal Options
	$anAlternativeActivation = $an_option->getOption( 'an_alternative_activation' );
	$anAlternativeElement    = $an_option->getOption( 'an_alternative_elements' );
	$anAlternativeText       = $an_option->getOption( 'an_alternative_text' );
	$anAlternativeClone      = $an_option->getOption( 'an_alternative_clone' );
	$anAlternativeProperties = $an_option->getOption( 'an_alternative_properties' );
	$anAlternativeCss        = $an_option->getOption( 'an_alternative_custom_css' );
	// redirect URL with JS
	$anPermalink = an_url_redirect( $anPageRedirect );
	// Modal box effect
	$anOptionModalEffect = an_modal_parameter( $anOptionModalEffect );
	// Modal box close
	$anOptionModalClose = an_modal_close( $anOptionModalClose );
	$undismissable      = $an_option->getOption( 'an_option_modal_dismiss' );
	if ( an_is_pro() && $undismissable ) {
		$anOptionModalClose = false;
	}
	// Style construct
	// Overlay RGA color
	$anOptionModalOverlay = an_hex2rgba( $anOptionModalBgcolor, $anOptionModalBgopacity / 100 );
	// Load random selectors
	$anScripts = unserialize( an_get_option( 'adblocker_notify_selectors' ) );
	// DOM and Json
	if ( false == $anOptionSelectors ) {
		$output .= '<div id="an-Modal" class="reveal-modal" ';
	} else {
		$output .= '<div id="' . $anScripts['selectors'][0] . '" class="' . $anScripts['selectors'][1] . '" ';
	}
	$output .= 'style="background:' . $anOptionModalBxcolor . ';';
	if ( ! empty( $anOptionModalBxtext ) ) {
		$output .= 'color:' . $anOptionModalBxtext . ';';
	}
	$anOptionModalBxWidth = $an_option->getOption( 'an_option_modal_width' );
	if ( ! empty( $anOptionModalBxWidth ) ) {
		$output .= 'max-width:' . $anOptionModalBxWidth . 'px;';
	}
	$output    .= 'z-index:9999999; ';
	$modalHTML = apply_filters( 'an_get_modal_html', null, $an_option );
	$output    .= '"></div>   ';
	$output    .= '<script type="text/javascript">';
	$output    .= '/* <![CDATA[ */';
	$output    .= 'var anOptions =' .
				  json_encode(
					  array(
						  'anOptionChoice'          => $anOptionChoice,
						  'anOptionStats'           => $anOptionStats,
						  'anOptionAdsSelectors'    => preg_replace( '/\s+/', '', $anOptionAdsSelectors ),
						  'anOptionCookie'          => $anOptionCookie,
						  'anOptionCookieLife'      => $anOptionCookieLife,
						  'anPageRedirect'          => $anPageRedirect,
						  'anPermalink'             => $anPermalink,
						  'anOptionModalEffect'     => $anOptionModalEffect,
						  'anOptionModalspeed'      => $anOptionModalSpeed,
						  'anOptionModalclose'      => $anOptionModalClose,
						  'anOptionModalOverlay'    => $anOptionModalOverlay,
						  'anAlternativeActivation' => $anAlternativeActivation,
						  'anAlternativeElement'    => $anAlternativeElement,
						  'anAlternativeText'       => do_shortcode( $anAlternativeText ),
						  'anAlternativeClone'      => $anAlternativeClone,
						  'anAlternativeProperties' => $anAlternativeProperties,
						  'anOptionModalShowAfter'  => $anOptionModalShowAfter,
						  'anPageMD5'               => $anPageMD5,
						  'anSiteID'                => $anSiteID,
						  'modalHTML'               => $modalHTML,
					  )
				  );
	$output    .= '/* ]]> */';
	$output    .= '</script>';
	// NO JS Redirect
	if ( ! empty( $anPageNojsActivation ) && ! $_COOKIE[ AN_COOKIE ] ) {
		// redirect URL with NO JS
		$anNojsPermalink = an_url_redirect( $anPageNojsRedirect );
		if ( 'undefined' != $anNojsPermalink ) {
			$output .= '<noscript><meta http-equiv="refresh" content="0; url=' . $anNojsPermalink . '" /></noscript>';
		}
	}
	$output .= '<div id="adsense" class="an-sponsored" style="position:absolute; z-index:-1; height:1px; width:1px; visibility: hidden; top: -1px; left: 0;"><img class="an-advert-banner" alt="sponsored" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"></div>';
	$output = apply_filters( 'an_prepare', $output );
	if ( false == $anScripts['temp-path'] && true == $an_option->getOption( 'an_option_selectors' ) ) {
		$output .= an_print_change_files_css_selectors( $an_option, $anScripts );
	}
	echo $output;
}

add_action( 'wp_footer', 'an_prepare' );
/**
 * ************************************************************
 * Dealing with cookies before page load to
 * prevent Header already sent notice
 ***************************************************************/
function an_cookies_init() {
	$an_option          = unserialize( an_get_option( 'adblocker_notify_options' ) );
	$anOptionCookie     = isset( $an_option['an_option_cookie'] ) ? $an_option['an_option_cookie'] : '';
	$anOptionCookieLife = isset( $an_option['an_option_cookie_life'] ) ? $an_option['an_option_cookie_life'] : '';
	if ( isset( $an_option['an_page_nojs_activation'] ) ) {
		$anPageNojsActivation = $an_option['an_page_nojs_activation'];
	} else {
		$anPageNojsActivation = '';
	}
	if ( isset( $an_option['an_page_nojs_redirect'] ) ) {
		$anPageNojsRedirect = $an_option['an_page_nojs_redirect'];
	} else {
		$anPageNojsRedirect = '';
	}
	if ( ! empty( $anPageNojsActivation ) && isset( $_COOKIE[ AN_COOKIE ] ) && ! $_COOKIE[ AN_COOKIE ] ) {
		// redirect URL with NO JS
		$anNojsPermalink = an_url_redirect( $anPageNojsRedirect );
		if ( 'undefined' != $anNojsPermalink ) {
			// Set new cookie value
			an_nojs_cookie( $anOptionCookieLife );
		}
	}
	// remove cookie if deactivate
	an_remove_cookie( $anOptionCookie );
}

add_action( 'init', 'an_cookies_init' );
/**
 * ************************************************************
 * Generate redirection URL with page ID
 ***************************************************************/
function an_url_redirect( $pageId ) {
	if ( is_main_query() ) {
		$currentPage = get_queried_object_id();
	} else {
		global $wp;
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		$currentPage = url_to_postid( $current_url );
	}
	if ( ! empty( $pageId ) && $pageId != $currentPage ) {
		$anPermalink = get_permalink( $pageId );
	} else {
		$anPermalink = 'undefined';
	}

	return $anPermalink;
}

/**
 * ************************************************************
 * Remove cookie when option is disabled
 ***************************************************************/
function an_remove_cookie( $anOptionCookie ) {
	if ( ( isset( $_COOKIE[ AN_COOKIE ] ) && 2 == $anOptionCookie ) || ( isset( $_COOKIE[ AN_COOKIE ] ) && '2' == $anOptionCookie ) ) {
		unset( $_COOKIE[ AN_COOKIE ] );
		setcookie( AN_COOKIE, null, - 1, '/' );
	}
}

/**
 * ************************************************************
 * Set cookie for No JS redirection.
 ***************************************************************/
function an_nojs_cookie( $expiration ) {
	$expiration = time() + ( $expiration * 24 * 60 * 60 );
	if ( ! isset( $_COOKIE[ AN_COOKIE ] ) ) {
		setcookie( AN_COOKIE, true, $expiration, '/' );
	}
}

/**
 * ************************************************************
 * Modal Box effect parameter
 ***************************************************************/
function an_modal_parameter( $key ) {
	switch ( $key ) {
		case '':
		case 1:
			$key = 'fadeAndPop';
			break;
		case 2:
			$key = 'fade';
			break;
		case 3:
			$key = 'none';
			break;
		default:
			$key = 'fadeAndPop';
			break;
	}

	return $key;
}

/**
 * ************************************************************
 * Modal Boxe closing option
 ***************************************************************/
function an_modal_close( $key ) {
	switch ( $key ) {
		case '':
		case 1:
			$key = true;
			break;
		case 2:
			$key = false;
			break;
		default:
			$key = true;
			break;
	}

	return $key;
}

/**
 * ************************************************************
 * Convert hexdec color string to rgb(a) string
 * Src: http://mekshq.com/how-to-convert-hexadecimal-color-code-to-rgb-or-rgba-using-php/
 ***************************************************************/
function an_hex2rgba( $color, $opacity = false ) {
	$default = 'rgb(0,0,0)';
	// Return default if no color provided
	if ( empty( $color ) ) {
		return $default;
	}
	// Sanitize $color if "#" is provided
	if ( '#' == $color[0] ) {
		$color = substr( $color, 1 );
	}
	// Check if color has 6 or 3 characters and get values
	if ( strlen( $color ) == 6 ) {
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( strlen( $color ) == 3 ) {
		$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	} else {
		return $default;
	}
	// Convert hexadec to rgb
	$rgb = array_map( 'hexdec', $hex );
	// Check if opacity is set(rgba or rgb)
	if ( $opacity ) {
		if ( abs( $opacity ) > 1 ) {
			$opacity = 1.0;
		}
		$output = 'rgba( ' . implode( ',', $rgb ) . ',' . $opacity . ' )';
	} else {
		$output = 'rgb( ' . implode( ',', $rgb ) . ' )';
	}

	// Return rgb(a) color string
	return $output;
}

/**
 * ************************************************************
 * Reset plugin options
 ***************************************************************/
function an_stats_notice() {
	echo '<div class="updated top"><p><strong>Ad Blocker Notify stats have been successfully cleared.</strong></p></div>';
}

/**
 * Reset statistics
 */
function an_reset_stats() {
	$prefix = an_is_pro() && is_multisite() ? '-network' : '';
	$screen = get_current_screen();
	if ( $screen && $screen->id != 'toplevel_page_' . AN_ID . $prefix ) {
		return;
	}
	if ( isset( $_GET['an-reset'] ) && 'true' == $_GET['an-reset'] ) {
		an_delete_option( 'adblocker_notify_counter' );
		add_action( 'admin_notices', 'an_stats_notice' );
	}
}

add_filter( 'admin_head', 'an_reset_stats' );
/**
 * Get option/site option
 */
function an_get_option( $key ) {
	if ( ! an_check_key( $key ) ) {
		return null;
	}

	return apply_filters( 'an_get_option_' . $key, apply_filters( 'an_get_option', $key ) );
}

/**
 * Get option
 */
function an_get_option_free( $key ) {
	return get_option( $key );
}

add_filter( 'an_get_option', 'an_get_option_free', 10, 1 );
/**
 * Update option/site option
 */
function an_update_option( $key, $value ) {
	if ( ! an_check_key( $key ) ) {
		return null;
	}

	return apply_filters( 'an_update_option_' . $key, apply_filters( 'an_update_option', $key, $value ) );
}

/**
 * Update option
 */
function an_update_option_free( $key, $value ) {
	return update_option( $key, $value );
}

add_filter( 'an_update_option', 'an_update_option_free', 10, 2 );
/**
 * Delete option/site option
 */
function an_delete_option( $key ) {
	if ( ! an_check_key( $key ) ) {
		return null;
	}

	return apply_filters( 'an_delete_option_' . $key, apply_filters( 'an_delete_option', $key ) );
}

/**
 * Delete option
 */
function an_delete_option_free( $key ) {
	return delete_option( $key );
}

add_filter( 'an_delete_option', 'an_delete_option_free', 10, 1 );
/**
 * Check if key exists
 */
function an_check_key( $key ) {
	$all_keys = array(
		'adblocker_notify_options',
		'adblocker_notify_selectors',
		'adblocker_notify_counter',
		'adblocker_upgrade_200',
		'adblocker_upgrade_205',
		'adblocker_upgrade_2010',
		'adblocker_upgrade_2012',
		'adblock_notify_global_counter',
		'adblock_notify_month_reset',
	);

	return in_array( $key, $all_keys );
}

/**
 * Check if pro is activated
 */
function an_is_pro() {
	return apply_filters( 'an_pro_activated', false );
}

/**
 * Check if last plan is active
 */
function an_is_bussiness() {
	$plan = apply_filters( 'an_pro_current_plan', 1 );

	return ( $plan == 3 ) && is_multisite() && an_is_pro();
}

add_filter( 'an_get_modal_html', 'an_get_modal_html', 10, 2 );
/**
 * Create the modal html
 */
function an_get_modal_html( $html, $an_option ) {
	return apply_filters( 'an_build_selected_template', null );

}

add_action( 'tf_admin_page_before_adblocker_notify', 'an_add_header_panel' );
/**
 *
 * Show the header of the option panel
 */
function an_add_header_panel() {
	?>

	<div class="adblock-notify-top">
		<p class="logo"><?php echo AN_NAME; ?></p>
		<span class="slogan">by <a
					href="https://getadmiral.com?utm_medium=plugin&utm_campaign=abn&utm_source=abnlinks">Admiral</a></span>
		<div class="adblock-notify-actions">
			<a target="_blank"
			   href="https://twitter.com/intent/tweet?text=Detect Adblock%20and%20nofity%20users%20with%20this%20awesome%20wordpress%20plugin%20-%20&amp;url=<?php echo urlencode( AN_PRO_URL ); ?>&amp;via=getadmiral"
			   class="tweet-about-it"><span></span><?php _e( 'Show your love', 'an-translate' ); ?></a>

			<a target="_blank" href="https://wordpress.org/support/plugin/adblock-notify-by-bweb/reviews/?filter=5"
			   class="leave-a-review"><span></span><?php _e( 'Leave A Review', 'an-translate' ); ?></a>

			<?php
			if ( ! an_is_pro() ) {
				?>
				<a target="_blank" href="<?php echo AN_PRO_URL; ?>"
				   class="buy-now"><span></span><?php _e( 'More features', 'an-translate' ); ?></a>
			<?php } ?>
			<div class="features">
				<?php _e( 'Multi-Site Support', 'an-translate' ); ?>

				<?php
				if ( ! an_is_pro() ) {
?>
<sup>PRO</sup> <?php } ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<?php
}

add_filter( 'an_build_selected_template', 'an_build_selected_template' );
/**
 * ************************************************************
 * Build the selected template and return the html
 ***************************************************************/
function an_build_selected_template() {
	$selected_template = an_get_template();
	$dir               = trailingslashit( get_template_directory() );
	if (
	! (
		file_exists( $dir )
		&&
		is_dir( $dir )
		&&
		file_exists( $dir . AN_TEMPLATES_DIRECTORY )
		&&
		is_dir( $dir . AN_TEMPLATES_DIRECTORY )
		&&
		file_exists( $dir . AN_TEMPLATES_DIRECTORY . $selected_template . '.php' )
	)
	) {
		$dir = ( $selected_template == 'an-default' ) ? AN_PATH : AN_PRO_PLUGIN_DIR;

	}
	if ( $selected_template == 'an-default' ) {
		require_once AN_PATH . 'inc/class-' . $selected_template . '.php';
	} else {
		require_once AN_PRO_PLUGIN_DIR . 'inc/class-' . $selected_template . '.php';
	}
	$template = an_get_template_class( $selected_template );
	$template = new $template;
	ob_start();
	$template->build( $dir . AN_TEMPLATES_DIRECTORY . $selected_template . '.php' );

	return ob_get_clean();
}

add_action( 'an_upgrade_routine', 'an_upgrade_routine_200' );
/**
 * Upgrade routine from version <= 2.0.1
 */
function an_upgrade_routine_200() {
	$upgrade = an_get_option( 'adblocker_upgrade_200', 'no' );
	if ( $upgrade != 'yes' ) {
		$anTempDir = unserialize( an_get_option( 'adblocker_notify_selectors' ) );
		if ( isset( $anTempDir['temp-path'] ) ) {
			an_delete_temp_folder( $anTempDir['temp-path'] );
			an_save_setting_random_selectors( true );
			an_update_option( 'adblocker_upgrade_200', 'yes' );
		}
	}
}

add_action( 'an_upgrade_routine', 'an_upgrade_routine_205' );
/**
 * Upgrade routine from version <= 2.0.5
 */
function an_upgrade_routine_205() {
	$upgrade = an_get_option( 'adblocker_upgrade_205', 'no' );
	if ( $upgrade != 'yes' ) {
		$anTempDir = unserialize( an_get_option( 'adblocker_notify_selectors' ) );
		if ( isset( $anTempDir['temp-path'] ) ) {
			an_delete_temp_folder( $anTempDir['temp-path'] );
			an_save_setting_random_selectors( true );
			an_update_option( 'adblocker_upgrade_205', 'yes' );
		}
	}
}

add_action( 'an_upgrade_routine', 'an_upgrade_routine_2010' );
/**
 * Upgrade routine from version <= 2.0.10
 */
function an_upgrade_routine_2010() {
	$upgrade = an_get_option( 'adblocker_upgrade_2010', 'no' );
	if ( $upgrade != 'yes' ) {
		$anTempDir = unserialize( an_get_option( 'adblocker_notify_selectors' ) );
		if ( isset( $anTempDir['temp-path'] ) ) {
			an_delete_temp_folder( $anTempDir['temp-path'] );
			an_save_setting_random_selectors( true );
			an_update_option( 'adblocker_upgrade_2010', 'yes' );
		}
	}
}

add_action( 'an_upgrade_routine', 'an_upgrade_routine_2012' );
/**
 * Upgrade routine from version <= 2.0.12
 */
function an_upgrade_routine_2012() {
	$upgrade = an_get_option( 'adblocker_upgrade_2012', 'no' );
	if ( $upgrade != 'yes' ) {
		$anTempDir = unserialize( an_get_option( 'adblocker_notify_selectors' ) );
		if ( isset( $anTempDir['temp-path'] ) ) {
			an_delete_temp_folder( $anTempDir['temp-path'] );
			an_save_setting_random_selectors( true );
			an_update_option( 'adblocker_upgrade_2012', 'yes' );
		}
	}
}

add_action( 'tf_admin_page_after', 'an_add_sidebar' );
/**
 * Adds sidebar in the plugin.
 */
function an_add_sidebar() {
	?>
	<div id="an-sidebar">
		<div id="an-testimonial">
			<h3>
				Over 9.000 happy clients
			</h3>
			<p> Useful for all people who display ads on their websites! </p>
			<p> Why lose the ad views or clicks when you can ‘politely’ ask the people to disable the AdBlock and
				support you?!</P>
			<p>
				This plugin just does that for you. Quit blabbering non-sense and make use of this wonderful plugin!
			</P><img class='testimonial-author'
					 src="data:image/jpeg;base64,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">
			<img class='review'
				 src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAUCAMAAABxjAnBAAAAflBMVEVMaXH+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg7+sg4pu5NeAAAAKXRSTlMAEfm6QqdF/ngBBd1mqVvcAk7TyIYzsaThHTCqwwQtjZBdXGhnrfyu+8txdKYAAACySURBVHjavYlHEsJAEAPXiV3nnAM57P8/iOUqsM0d6TAjdYtvbFtsQxNunrv4dJFqneKzRZJpnSVoZHHWcy5oXOHFgLGHzhPGQVl6iaUOhiCJIPT1Ln4YCIawTf0T0xbI30Uk90xGAiEItYdKIBRx2rLjgjjCdVbmuCAscV3hbQEsUaywwKaJeoU1Nk0My5Y4AzZNNDOpyrKacYPNEuNLPjuU7i4fIwpJtP308VPf4hHEG9EKi4RNAuoaAAAAAElFTkSuQmCC"
			<br/> <span> by <strong>Antony Agnel</strong></span>
			<div class="clear"></div>
		</div>
	</div>

	<?php
}

add_action( 'wp_ajax_an_track_url', 'an_save_track_status' );
/**
 * Save tracking option.
 */
function an_save_track_status() {
	check_admin_referer( 'an-nonce', 'nonce' );
	$status = $_POST['status'];
	if ( $status == 'yes' ) {
		update_option( 'an_logger_flag', 'yes' );
	} else {
		update_option( 'an_logger_flag', 'no' );
	}
	wp_send_json_success(
		array(
			'status' => $status,
		)
	);
}

add_filter( 'adblock_notify_by_bweb_logger_flag', 'an_check_logger' );
/**
 * Return the logger status.
 *
 * @return bool Logger status.
 */
function an_check_logger() {
	return ( get_option( 'an_logger_flag', 'no' ) == 'yes' ) ? true : false;
}

add_action( 'admin_init', 'an_flag_user' );
/**
 * Marks the new user if he is before 2.1 or not
 */
function an_flag_user() {
	$check = get_option( 'adblock_notify_new_user', '' );
	if ( $check === '' ) {
		if ( get_option( 'adblocker_notify_options' ) === false ) {
			update_option( 'adblock_notify_new_user', 'yes' );
		} else {
			update_option( 'adblock_notify_new_user', 'no' );
		}
	}
}

/**
 * Helper to see if the user is new or not.
 *
 * @return bool If the user is new or not.
 */
function an_is_new() {
	return ( get_option( 'adblock_notify_new_user', 'yes' ) === 'yes' );
}

/**
 * Helper to check if the user is using personal plan or not
 *
 * @return bool If he is using personal plan or not
 */
function an_is_personal() {
	return defined( 'AN_PRO_VERSION' );
}

/**
 * Helper to check if the user is using marketer plan or not
 *
 * @return bool If he is using marketer plan or not
 */
function an_is_marketer() {
	if ( an_is_personal() ) {
		$plan = apply_filters( 'an_pro_current_plan', '1' );
		$plan = intval( $plan );
		if ( $plan > 1 ) {
			return true;
		}

		return false;
	} else {
		return false;
	}
}

/**
 * Helper to check if the user is using agency plan or not
 *
 * @return bool If he is using agency plan or not
 */
function an_is_agency() {
	if ( an_is_marketer() ) {
		$plan = apply_filters( 'an_pro_current_plan', '1' );
		$plan = intval( $plan );
		if ( $plan > 2 ) {
			return true;
		}

		return false;
	} else {
		return false;
	}
}

/**
 * Returns the number of views used.
 *
 * @return int The current views
 */
function an_get_current_views() {
	return intval( an_get_option( 'adblock_notify_global_counter' ) ) > an_get_limit() ? an_get_limit() : intval( intval( an_get_option( 'adblock_notify_global_counter' ) ) );
}

/**
 * Return the user current limit.
 *
 * @return int The current limit.
 */
function an_get_limit() {
	$limit = '5000';
	if ( an_is_personal() ) {
		$limit = '30000';
	}
	if ( an_is_marketer() ) {
		$limit = '70000';
	}
	if ( an_is_agency() ) {
		$limit = '250000';
	}

	return intval( $limit );
}

/**
 * Return true if users reached the limit.
 *
 * @return bool State of the views.
 */
function an_check_views() {
	return false;
	if ( ! an_is_new() ) {
		return false;
	}
	$limit   = an_get_limit();
	$current = an_get_current_views();
	if ( $current === 0 ) {
		an_update_option( 'adblock_notify_month_reset', time() );

	} else {
		$reset_time = an_get_option( 'adblock_notify_month_reset' );
		if ( ( time() - $reset_time ) > ( 31 * 24 * 3600 ) ) {
			$current = 0;
			an_update_option( 'adblock_notify_global_counter', '0' );
			an_update_option( 'adblock_notify_month_reset', time() );
		}
	}
	if ( $limit <= $current ) {
		return true;
	}

	return false;
}
