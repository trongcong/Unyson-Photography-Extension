<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

if ( ! is_admin() ) {
	global $template;
	/**
	 * @var FW_Extension_Photography $photography
	 */
	$photography = fw()->extensions->get( 'photography' );

	if ( is_singular( $photography->get_post_type_name() ) ) {
		
	}
}



