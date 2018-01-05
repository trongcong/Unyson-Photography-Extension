<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Photography', 'fw' );
$manifest['description'] = __( 'This extension adds a fully fledged Photography module to your theme.  ', 'fw' );
$manifest['version']     = '1.0.1';
$manifest['display']     = true;
$manifest['standalone']  = true;
$manifest['github_repo'] = 'https://github.com/trongcong/Unyson-Photography-Extension';
$manifest['uri']        = 'http://bearsthemes.com';
$manifest['author']     = 'Trong Cong';
$manifest['author_uri'] = 'http://2dev4u.com/';

$manifest['github_update'] = 'trongcong/Unyson-Photography-Extension';
$manifest['requirements'] = array(
    'wordpress' => array(
        'min_version' => '4.8', 
    ),
    'framework' => array(
        'min_version' => '2.7', 
    ) 
);