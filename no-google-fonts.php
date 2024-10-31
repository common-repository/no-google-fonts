<?php
/**
 * Plugin Name: No Google Fonts
 * Description: Disables all Google font stylesheets loaded on the frontend.
 * Author: Hassan Derakhshandeh
 * Author URI: hassan.derakhshandeh@gmail.com
 * Version: 0.3
 */

function no_google_fonts_init() {
	global $wp_styles;
	$excludes = array();
	if ( isset( $wp_styles ) && is_array( $wp_styles->registered ) ) {
		foreach ( $wp_styles->registered as $handle => $stylesheet ) {
			if ( is_string( $stylesheet->src ) && preg_match( '/fonts\.googleapis\.com/', $stylesheet->src ) ) {
				$excludes[] = $stylesheet->handle;
				wp_dequeue_style( $stylesheet->handle );
			}

			/* if another stylesheet has added the google fonts as a dependency, remove it from the list so the stylesheet is enqueued anyway */
			$stylesheet->deps = array_diff( $stylesheet->deps, $excludes );
		}
	}
}
add_action( 'wp_head', 'no_google_fonts_init', 7 ); // run just before wp_print_styles (8)
add_action( 'wp_footer', 'no_google_fonts_init', 19 ); // run just before wp_print_footer_scripts (20)
add_action( 'admin_enqueue_scripts', 'no_google_fonts_init', 7 );

/**
 * Remove Google fonts enqueued
 *
 * @return string
 */
function no_google_fonts_style_header_tag( $tag, $handle, $href, $media ) {
	if ( preg_match( '/fonts\.googleapis\.com/', $tag ) ) {
		$tag = '';
	}

	return $tag;
}
add_filter( 'style_loader_tag', 'no_google_fonts_style_header_tag', 100, 4 );

/* disable Google fonts in Themify themes */
add_filter( 'themify_google_fonts', '__return_empty_array', 100 );