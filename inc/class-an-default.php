<?php
/**
 * The default template
 *
 * @package adblock-notify-by-bweb
 */


// include  generic template class
require_once AN_PATH . 'inc/an-template.php';

/**
 * The default template
 *
 * @package adblock-notify-by-bweb
 */
class AnDefault extends AnTemplate {

	/**
	 * Instance of the template class
	 *
	 * @var AnDefault The one true AnDefault template
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Return instance of the Template class
	 *
	 * @return AnDefault Instance of the template
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AnDefault ) ) {
			self::$instance = new AnDefault;
			self::$instance->setup_constants();
		}
		return self::$instance;
	}

	/**
		Build template-specific features
	 */
	public function setup_constants() {
		$an_option         = TitanFramework::getInstance( 'adblocker_notify' );
		$this->title        = $an_option->getOption( 'an_modal_title' );
		$this->content      = do_shortcode( $an_option->getOption( 'an_modal_text' ) );
		$anOptionModalBxtitle = $an_option->getOption( 'an_option_modal_bxtitle' );

		$headingStyle       = array();
		if ( $anOptionModalBxtitle ) {
			$headingStyle[] = 'color:' . $anOptionModalBxtitle;
		}

		$this->heading_style = implode( ';', $headingStyle );

		$this->footer       = '';
		$this->extra        = $this->get_extra();
	}
}
