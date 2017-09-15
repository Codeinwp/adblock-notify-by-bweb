<?php
/**
 * The base class for the templates
 *
 * @package adblock-notify-pro
 */

/**
 * The base class for the templates
 *
 * @package adblock-notify-pro
 */
abstract class AnTemplate {

	/**
	 * Template class props
	 *
	 * @var string the options */
	protected $options;

	/**
		Get the options for the admin interface
	 */
	public function get_options() {
		return $this->options;
	}

	/**
		Build the common features
	 */
	abstract function setup_constants();

	/**
		The builder
	 */
	public final function build( $file ) {
		$this->setup_constants();
		include_once $file;
	}

	/**
		Add the extras
	 */
	protected function get_extra() {
		$an_option         = TitanFramework::getInstance( 'adblocker_notify' );
		// Closing cross
		$anOptionModalCross = $an_option->getOption( 'an_option_modal_cross' );
		$undismissable      = $an_option->getOption( 'an_option_modal_dismiss' );
		if ( intval( $anOptionModalCross ) === 2 && ( ! an_is_pro() || (an_is_pro() && ! $undismissable)) ) {
			return '<a class="close-modal close-' . an_get_random_selector( 'reveal-modal' ) . '">&#215;</a>';
		}
		return '';
	}
}
