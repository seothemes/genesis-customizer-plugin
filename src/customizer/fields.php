<?php
/**
 * Genesis Customizer.
 *
 * This file adds Customizer fields to Genesis Customizer.
 *
 * @package   GenesisCustomizer
 * @author    SEO Themes
 * @copyright 2019 SEO Themes
 * @license   GPL-3.0-or-later
 */

namespace GenesisCustomizer;

add_action( 'genesis_setup', __NAMESPACE__ . '\add_fields', 15 );
/**
 * Add fields defined in config to Customizer.
 *
 * @since 1.0.0
 *
 * @return array|mixed
 */
function add_fields() {
	$fields  = [];
	$path    = _get_path();
	$handle  = _get_handle();
	$configs = apply_filters( 'genesis_customizer_config', [ $path . 'config' ] );

	// Loop through config directories.
	foreach ( $configs as $config ) {
		foreach ( glob( $config . '/*', GLOB_ONLYDIR ) as $panel ) {
			foreach ( glob( $panel . '/*.php' ) as $section_path ) {
				$counter = 0;
				$module  = strpos( $section_path, 'pro/config' ) === false ? 0 : 1;
				$panel   = basename( dirname( $section_path ) );
				$section = basename( $section_path, '.php' );
				$prefix  = $handle . '_' . $panel . '_' . $section;
				$fields  = require_once $section_path;

				if ( ! is_array( $fields ) ) {
					continue;
				}

				if ( $module && ! apply_filters( $prefix . '_config', false ) ) {
					continue;
				}

				foreach ( $fields as $field ) {
					\Kirki::add_field( $handle, apply_filters( 'genesis_customizer_field', $field, $panel, $section, $prefix, $counter++ ) );
				}
			}
		}
	}

	return $fields;
}

add_filter( 'genesis_customizer_field', __NAMESPACE__ . '\format_field', 10, 5 );
/**
 * Modify fields before adding them.
 *
 * @since 1.0.0
 *
 * @param array  $field   Field array values.
 * @param string $panel   Field panel string.
 * @param string $section Fields section string.
 * @param string $prefix  Genesis Customizer option prefix.
 * @param int    $counter Counter for unique IDs.
 *
 * @return array
 */
function format_field( $field, $panel, $section, $prefix, $counter ) {
	$field['section']     = $prefix;
	$field['settings']    = $panel . '_' . $section . '_' . $field['settings'];
	$field['option_type'] = 'option';
	$field['option_name'] = _get_handle();

	if ( isset( $field['type'] ) && 'custom' === $field['type'] ) {
		$field['settings'] = $field['settings'] . '-' . $counter;
	}

	// Custom color palette.
	if ( isset( $field['type'] ) && 'multicolor' === $field['type'] ) {
		$colors = array_slice( _get_color( 'all' ), 0, 8 );
		$colors = is_array( $colors ) ? array_values( $colors ) : $colors;

		$field['choices']['irisArgs']['palettes'] = $colors;
	}

	// Allow for element arrays.
	if ( isset( $field['output'] ) && $field['output'] ) {
		foreach ( $field['output'] as $output ) {
			if ( isset( $output['element'] ) && is_array( $output['element'] ) ) {
				$output['element'] = implode( ',', $output['element'] );
			}
		}

		// Set transport for fields with output.
		if ( ! isset( $field['transport'] ) ) {
			$field['transport'] = 'auto';
		}
	}

	return $field;
}
