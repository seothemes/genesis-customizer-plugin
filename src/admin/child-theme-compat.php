<?php
/**
 * Genesis Customizer.
 *
 * This file checks for child theme compatibility with Genesis Customizer.
 *
 * @package   GenesisCustomizer
 * @author    SEO Themes
 * @copyright 2019 SEO Themes
 * @license   GPL-3.0-or-later
 */

namespace GenesisCustomizer;

/**
 * Adds admin notice if requirements are not met.
 *
 * Themes can build in support for Genesis Customizer by either adding a
 * `genesis-customizer` tag to the stylesheet header comment and/or by
 * adding theme support for the `genesis-customizer` feature, e.g:
 *
 * `add_theme_support( 'genesis-customizer' );`
 *
 * @since 1.0.0
 *
 * @return bool
 */
function child_theme_is_compatible() {
	$handle  = _get_handle();
	$tags    = wp_get_theme()->get( 'Tags' ) ?: [];
	$support = get_theme_support( $handle );

	if ( in_array( $handle, $tags, true ) || $support ) {
		return true;
	}

	return false;
}

add_action( 'admin_notices', __NAMESPACE__ . '\child_theme_notice' );
/**
 * Displays the admin notice warning message.
 *
 * @since 1.0.0
 *
 * @return void
 */
function child_theme_notice() {
	if ( ! child_theme_is_compatible() ) {
		printf(
			'<div class="notice notice-warning"><p><strong>%s</strong> %s <a href="%s" target="_blank">%s</a> %s </p></div>',
			esc_html( _get_name() ),
			esc_html__( ' is not supported by the active child theme and may not work as expected. Please install a compatible child theme or refer to the', 'genesis-customizer' ),
			esc_attr__( 'https://docs.seothemes.com/article/199-how-to-add-theme-support-for-genesis-customizer' ),
			esc_html__( 'adding theme support guide', 'genesis-customizer' ),
			esc_html__( 'for instructions on how to declare Genesis Customizer support in your theme.', 'genesis-customizer' )
		);
	}
}
