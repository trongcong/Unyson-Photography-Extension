<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Replace the content of the current template with the content of photography view
 *
 * @param string $the_content
 *
 * @return string
 */
function _filter_fw_ext_photography_the_content( $the_content ) {
	/**
	 * @var FW_Extension_Photography $photography
	 */
	$photography = fw()->extensions->get( 'photography' );

	return fw_render_view( $photography->locate_view_path( 'content' ), array( 'the_content' => $the_content ) );
}

/**
 * Check if the there are defined views for the photography templates, otherwise are used theme templates
 *
 * @param string $template
 *
 * @return string
 */
function _filter_fw_ext_photography_template_include( $template ) {

	/**
	 * @var FW_Extension_Photography $photography
	 */
	$photography = fw()->extensions->get( 'photography' );

	if ( is_singular( $photography->get_post_type_name() ) ) {

		if ( preg_match( '/single-' . '.*\.php/i', basename( $template ) ) === 1 ) {
			return $template;
		}

		if ( $photography->locate_view_path( 'single' ) ) {
			return $photography->locate_view_path( 'single' );
		} else {
			add_filter( 'the_content', '_filter_fw_ext_photography_the_content' );
		}
	} else if ( is_tax( $photography->get_taxonomy_name() ) && $photography->locate_view_path( 'taxonomy' ) ) {

		if ( preg_match( '/taxonomy-' . '.*\.php/i', basename( $template ) ) === 1 ) {
			return $template;
		}

		return $photography->locate_view_path( 'taxonomy' );
	} else if ( is_post_type_archive( $photography->get_post_type_name() ) && $photography->locate_view_path( 'archive' ) ) {
		if ( preg_match( '/archive-' . '.*\.php/i', basename( $template ) ) === 1 ) {
			return $template;
		}

		return $photography->locate_view_path( 'archive' );
	}

	return $template;
}

add_filter( 'template_include', '_filter_fw_ext_photography_template_include' );

function registerOptions( $options, $post_type ) {
	$photography = fw()->extensions->get( 'photography' );
	if ( $post_type == 'yolo-photography' ) {
		return $photography->get_post_options( 'photography' );
	}

	return $options;
}

add_filter( 'fw_post_options', 'registerOptions', 10, 2 );