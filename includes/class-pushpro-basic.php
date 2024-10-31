<?php

/**
 * Class Pushpro_Basic
 *
 * Abstract class. Mostly for prevent duplication of logic.
 */
abstract class Pushpro_Basic {

	/**
	 * Get stored token
	 *
	 * @return array
	 */
	public function pushpro_get_settings() {
		$default_settings = [
			'token' => '',
		];
		$settings = get_option( 'pushpro_settings', [] );

		return wp_parse_args( $settings, $default_settings );
	}

}