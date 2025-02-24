<?php
/**
 * Plugin Options.
 *
 * @package SCE
 */

namespace SCE\Includes\Admin;

/**
 * Class Options
 */
class Options {

	/**
	 * A list of options cached for saving.
	 *
	 * @var array $options
	 */
	private static $options = array();

	/**
	 * Get the options for Simple Comment Editing.
	 *
	 * @since 3.0.0
	 *
	 * @param bool   $force true to retrieve options directly, false to use cached version.
	 * @param string $key The option key to retrieve.
	 *
	 * @return string|array|bool Return a string if key is set, array of options (default), or false if key is set and option is not found.
	 */
	public static function get_options( $force = false, $key = '' ) {

		$options = self::$options;
		if ( ! is_array( $options ) || empty( $options ) || true === $force ) {
			$options       = get_site_option( 'sce_options', array() );
			self::$options = $options;
		}
		if ( false === $options || empty( $options ) || ! is_array( $options ) ) {
			$options = self::get_defaults();
		} else {
			$options = wp_parse_args( $options, self::get_defaults() );
		}
		self::$options = $options;

		// Return a key if set.
		if ( ! empty( $key ) ) {
			if ( isset( $options[ $key ] ) ) {
				return $options[ $key ];
			} else {
				return false;
			}
		}

		return self::$options;
	}

	/**
	 * Save options for the plugin.
	 *
	 * @param array $options array of options.
	 */
	public static function update_options( $options = array() ) {

		foreach ( $options as $key => &$option ) {
			switch ( $key ) {
				case 'timer':
					$timer = absint( $options[ $key ] );
					if ( 0 === $timer ) {
						$timer = 5;
					}
					$option = $timer;
					break;
				default:
					$option = sanitize_text_field( $options[ $key ] );
					break;
			}
		}
		if ( \Simple_Comment_Editing::is_multisite() ) {
			update_site_option( 'sce_options', $options );
		} else {
			update_option( 'sce_options', $options );
		}
	}

	/**
	 * Get the default options for Simple Comment Editing.
	 *
	 * @since 3.0.0
	 */
	private static function get_defaults() {
		$defaults = array(
			'timer' => 5,
		);

		/**
		 * Allow other plugins to add to the defaults.
		 *
		 * @since 3.0.0
		 *
		 * @param array $defaults An array of option defaults.
		 */
		$defaults = apply_filters( 'sce_options_defaults', $defaults );
		return $defaults;
	}
}
