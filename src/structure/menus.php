<?php
/**
 * Genesis Customizer.
 *
 * This file contains navigation structure functions for Genesis Customizer.
 *
 * @package   GenesisCustomizer
 * @author    SEO Themes
 * @copyright 2019 SEO Themes
 * @license   GPL-3.0-or-later
 */

namespace GenesisCustomizer;

add_filter( 'body_class', __NAMESPACE__ . '\menu_body_classes', 100, 1 );
/**
 * Add menu-specific body classes.
 *
 * @since 1.0.0
 *
 * @param array $classes All body classes.
 *
 * @return array
 */
function menu_body_classes( $classes ) {
	$classes[] = _get_value( 'menus_mobile_animation', 'has-mobile-menu-top' );
	$classes[] = 'social-menu-' . _get_value( 'menus_social_color-style', '' );

	return $classes;
}

add_action( 'genesis_before', __NAMESPACE__ . '\reposition_menus' );
/**
 * Reposition navigation menus.
 *
 * @since 1.0.0
 *
 * @return void
 */
function reposition_menus() {

	// Nav primary.
	remove_action( 'genesis_after_header', 'genesis_do_nav' );

	$header_layout = _get_value( 'header_primary_layout' );

	if ( 'has-logo-right' === $header_layout ) {
		add_action( 'genesis_before_title_area', 'genesis_do_nav' );

	} elseif ( 'has-logo-above' === $header_layout ) {
		add_action( 'genesis_header', 'genesis_do_nav', 14 );

	} else {
		add_action( 'genesis_after_title_area', 'genesis_do_nav' );
	}

	// Nav secondary.
	remove_action( 'genesis_after_header', 'genesis_do_subnav' );
	add_action( 'genesis_after_header_wrap', 'genesis_do_subnav' );

	// Nav footer.
	$value    = _get_value( 'menus_footer_position' );
	$position = [
		'above-footer'  => [ 'genesis_footer', 9 ],
		'above-widgets' => [ 'genesis_footer', 10 ],
		'above-credits' => [ 'genesis_footer', 12 ],
		'below-credits' => [ 'genesis_footer', 14 ],
	];

	add_action( $position[ $value ][0], __NAMESPACE__ . '\add_footer_menu', $position[ $value ][1] );
}

add_filter( 'genesis_attr_nav-primary', __NAMESPACE__ . '\menu_alignment' );
/**
 * Set the mobile and primary menu alignment.
 *
 * @since 1.0.0
 *
 * @param array $atts Primary nav attributes.
 *
 * @return array
 */
function menu_alignment( $atts ) {
	$mobile  = _get_value( 'menus_mobile_alignment' );
	$desktop = _get_value( 'menus_primary_alignment' );
	$desktop = str_replace( 'flex-', '', $desktop );
	$space   = $mobile ? ' ' : '';

	$atts['class'] .= $space . $mobile . ' flex-' . $desktop . '-desktop';

	return $atts;
}

/**
 * Display footer menu if set.
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_footer_menu() {
	genesis_nav_menu(
		[
			'theme_location' => 'footer',
			'depth'          => 1,
		]
	);
}

add_filter( 'widget_nav_menu_args', __NAMESPACE__ . '\nav_menu_widget' );
/**
 * Wrap nav menu widget links in span tags.
 *
 * @since 1.0.2
 *
 * @param array $nav_menu_args Nav menu widget args (wp_nav_menu).
 *
 * @return array
 */
function nav_menu_widget( $nav_menu_args ) {
	$custom = [
		'link_before' => genesis_markup(
			[
				'open'    => '<span %s>',
				'context' => 'nav-link-wrap',
				'echo'    => false,
			]
		),
		'link_after'  => genesis_markup(
			[
				'close'   => '</span>',
				'context' => 'nav-link-wrap',
				'echo'    => false,
			]
		),
	];

	return array_merge_recursive( $nav_menu_args, $custom );
}
