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
	$an_option = TitanFramework::getInstance( 'adblocker_notify' );
	$output = '';

	// Retrieve options
	// General Options
	$anOptionChoice = $an_option->getOption( 'an_option_choice' );
	$anOptionStats = $an_option->getOption( 'an_option_stats' );
	$anOptionSelectors = $an_option->getOption( 'an_option_selectors' );
	$anOptionAdsSelectors = $an_option->getOption( 'an_option_ads_selectors' );
	$anOptionCookie = $an_option->getOption( 'an_option_cookie' );
	$anOptionCookieLife = $an_option->getOption( 'an_option_cookie_life' );
	$anPageRedirect = $an_option->getOption( 'an_page_redirect' );
	$anPageNojsActivation = $an_option->getOption( 'an_page_nojs_activation' );
	$anPageNojsRedirect = $an_option->getOption( 'an_page_nojs_redirect' );

	// Modal Options
	$anOptionModalEffect = $an_option->getOption( 'an_option_modal_effect' );
	$anOptionModalSpeed = $an_option->getOption( 'an_option_modal_speed' );
	$anOptionModalClose = $an_option->getOption( 'an_option_modal_close' );
	$anOptionModalBgcolor = $an_option->getOption( 'an_option_modal_bgcolor' );
	$anOptionModalBgopacity = $an_option->getOption( 'an_option_modal_bgopacity' );
	$anOptionModalBxcolor = $an_option->getOption( 'an_option_modal_bxcolor' );
	$anOptionModalBxtext = $an_option->getOption( 'an_option_modal_bxtext' );
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
	$anAlternativeElement = $an_option->getOption( 'an_alternative_elements' );
	$anAlternativeText = $an_option->getOption( 'an_alternative_text' );
	$anAlternativeClone = $an_option->getOption( 'an_alternative_clone' );
	$anAlternativeProperties = $an_option->getOption( 'an_alternative_properties' );
	$anAlternativeCss = $an_option->getOption( 'an_alternative_custom_css' );

	// redirect URL with JS
	$anPermalink = an_url_redirect( $anPageRedirect );

	// Modal box effect
	$anOptionModalEffect = an_modal_parameter( $anOptionModalEffect );
	// Modal box close
	$anOptionModalClose = an_modal_close( $anOptionModalClose );

	// Style construct
	// Overlay RGA color
	$anOptionModalOverlay = an_hex2rgba( $anOptionModalBgcolor, $anOptionModalBgopacity / 100 );

	// Load random selectors
	$anScripts = unserialize( an_get_option( 'adblocker_notify_selectors' ) );
	// DOM and Json
	if ( false == $anOptionSelectors  ) {
		$output .= '<div id="an-Modal" class="reveal-modal" ';
	} else {
		$output .= '<div id="' . $anScripts['selectors'][0] . '" class="' . $anScripts['selectors'][1] . '" ';
	}

	$output .= 'style="background:' . $anOptionModalBxcolor . ';';
	if ( ! empty( $anOptionModalBxtext ) ) {
		$output .= 'color:' . $anOptionModalBxtext . ';';
	}
	$anOptionModalBxWidth   = $an_option->getOption( 'an_option_modal_width' );
	if ( ! empty( $anOptionModalBxWidth ) ) {
		$output .= 'max-width:' . $anOptionModalBxWidth . 'px;';
	}

	$modalHTML  = apply_filters( 'an_get_modal_html', null, $an_option );

	$output .= '"></div>   ';

	$output .= '<script type="text/javascript">';
	$output .= '/* <![CDATA[ */';
	$output .= 'var anOptions =' .
			json_encode( array(
				'anOptionChoice' 			=> $anOptionChoice,
				'anOptionStats' 			=> $anOptionStats,
				'anOptionAdsSelectors' 		=> preg_replace( '/\s+/', '', $anOptionAdsSelectors ),
				'anOptionCookie' 			=> $anOptionCookie,
				'anOptionCookieLife' 		=> $anOptionCookieLife,
				'anPageRedirect' 			=> $anPageRedirect,
				'anPermalink' 				=> $anPermalink,
				'anOptionModalEffect' 		=> $anOptionModalEffect,
				'anOptionModalspeed' 		=> $anOptionModalSpeed,
				'anOptionModalclose' 		=> $anOptionModalClose,
				'anOptionModalOverlay' 		=> $anOptionModalOverlay,
				'anAlternativeActivation' 	=> $anAlternativeActivation,
				'anAlternativeElement' 		=> $anAlternativeElement,
				'anAlternativeText' 		=> do_shortcode( $anAlternativeText ),
				'anAlternativeClone' 		=> $anAlternativeClone,
				'anAlternativeProperties' 	=> $anAlternativeProperties,
				'anOptionModalShowAfter' 	=> $anOptionModalShowAfter,
				'anPageMD5' 	            => $anPageMD5,
				'anSiteID' 	                => $anSiteID,
				'modalHTML'                 => $modalHTML,
			) );
	$output .= '/* ]]> */';
	$output .= '</script>';

	// NO JS Redirect
	if ( ! empty( $anPageNojsActivation ) && ! $_COOKIE[ AN_COOKIE ] ) {

		// redirect URL with NO JS
		$anNojsPermalink = an_url_redirect( $anPageNojsRedirect );

		if (  'undefined' != $anNojsPermalink  ) {
			$output .= '<noscript><meta http-equiv="refresh" content="0; url=' . $anNojsPermalink . '" /></noscript>';
		}
	}

	$output .= '<div id="adsense" class="an-sponsored" style="position:absolute; z-index:-1; height:1px; width:1px; visibility: hidden; top: -1px; left: 0;"><img class="an-advert-banner" alt="sponsored" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"></div>';

	$output = apply_filters( 'an_prepare', $output );

	if ( false == $anScripts['temp-path']  && true == $an_option->getOption( 'an_option_selectors' ) ) {
		$output .= an_print_change_files_css_selectors( $an_option, $anScripts ); }

	echo $output;
}
add_action( 'wp_footer', 'an_prepare' );


/**

 * ************************************************************
 * Dealing with cookies before page load to
 * prevent Header already sent notice
 ***************************************************************/
function an_cookies_init() {
	$an_option = unserialize( an_get_option( 'adblocker_notify_options' ) );
	$anOptionCookie = $an_option['an_option_cookie'];
	$anOptionCookieLife = $an_option['an_option_cookie_life'];
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
	if ( ( isset( $_COOKIE[ AN_COOKIE ] ) && 2 == $anOptionCookie  ) || ( isset( $_COOKIE[ AN_COOKIE ] ) && '2' == $anOptionCookie   ) ) {
		unset( $_COOKIE[ AN_COOKIE ] );
		setcookie( AN_COOKIE, null, -1, '/' );
	}
}


/**

 * ************************************************************
 * Set cookie for No JS redirection.
 ***************************************************************/
function an_nojs_cookie( $expiration ) {
	$expiration = time() + ($expiration * 24 * 60 * 60);
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
		default :
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
		default :
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
		return $default; }

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
			$opacity = 1.0; }
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
	echo '<div class="updated top"><p><strong>Adblock Notify stats have been successfully cleared.</strong></p></div>';
}

/**
 * Reset statistics
 */
function an_reset_stats() {
	$prefix = an_is_pro() && is_multisite() ? '-network' : '';

	$screen = get_current_screen();
	if ( 'toplevel_page_' . AN_ID . $prefix != $screen->id ) {
		return;
	}

	if ( isset( $_GET['an-reset'] ) && 'true' == $_GET['an-reset']  ) {
		an_an_delete_option( 'adblocker_notify_counter' );
		add_action( 'admin_notices', 'an_stats_notice' );
	}
}
add_filter( 'admin_head', 'an_reset_stats' );

/**
 * Get option/site option
 */
function an_get_option( $key ) {
	if ( ! an_check_key( $key ) ) { return null;
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
	if ( ! an_check_key( $key ) ) { return null;
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
	if ( ! an_check_key( $key ) ) { return null;
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
	$all_keys   = array(
		'adblocker_notify_options',
		'adblocker_notify_selectors',
		'adblocker_notify_counter',
	);

	return in_array( $key, $all_keys );
}


/**
 * Check if pro is activated
 */
function an_is_pro() {
	return apply_filters( 'an_pro_activated', false );
}

add_filter( 'an_get_modal_html', 'an_get_modal_html', 10, 2 );
/**
 * Create the modal html
 */
function an_get_modal_html( $html, $an_option ) {
	if ( ! apply_filters( 'an_pro_activated', false ) ) {
		$anModalTitle   = $an_option->getOption( 'an_modal_title' );
		$anModalText    = do_shortcode( $an_option->getOption( 'an_modal_text' ) );
		$anOptionModalCross = $an_option->getOption( 'an_option_modal_cross' );
		$anOptionModalBxtitle = $an_option->getOption( 'an_option_modal_bxtitle' );

		$headingColor = '';
		if ( $anOptionModalBxtitle ) {
			$headingColor = 'style="color:' . $anOptionModalBxtitle . '"';
		}

		// Closing cross
		$closingCross = '';
		if ( intval( $anOptionModalCross ) === 2 ) {
			$closingCross = '<a class="close-reveal-modal">&#215;</a>';
		}
		return '<div class="reveal-modal-default"><h1 ' . $headingColor . '>' . $anModalTitle . '</h1>' . $anModalText . $closingCross . '</div>';
	} else {
		return apply_filters( 'an_build_selected_template', null );
	}
}


add_action( 'tf_admin_page_before_adblocker_notify','an_add_header_panel' );
/**
 *
 * Show the header of the option panel
 */
function an_add_header_panel() {
	?>

	<div class="adblock-notify-top">
		<p class="logo"><?php echo AN_NAME; ?></p>
		<span class="slogan">by <a href="http://themeisle.com/plugins/adblock-notify/?utm_source=topadmin&utm_medium=announce&utm_campaign=top">ThemeIsle</a></span>
		<div class="adblock-notify-actions">
			<a target="_blank" href="https://twitter.com/intent/tweet?text=Check-out%20this%20awesome%20plugin%20-%20&amp;url=http%3A%2F%2Frevive.social%2Fplugins%2Frevive-old-post%2F&amp;via=ReviveSocial" class="tweet-about-it"><span></span> Show your love</a>

			<a target="_blank" href="https://wordpress.org/support/plugin/adblock-notify-by-bweb/reviews/" class="leave-a-review"><span></span> Leave A Review</a>

			<?php
			$pro = apply_filters( 'an_pro_activated','' );
			if ( ! $pro ) {
				?>
				<a href="http://themeisle.com/plugins/adblock-notify/?utm_source=topadmin&utm_medium=announce&utm_campaign=top" title="Buy Now">Buy Now</a>
			<?php } ?>
		</div>
	</div>
	<?php
}
