<?php defined( 'FW' ) or die();

// is executed when the extension is first installed
$extensions = array(
	'extension_name' => array(
		'display'     => true,
		'parent'      => null,
		'name'        => __( 'Photography', 'yolo' ),
		'description' => __( 'Photography post type.', 'yolo' ),
		'thumbnail'   => 'pathToThumbnail/thumbnail.jpg',
		'download'    => array(
			'source' => 'github',
			'opts'   => array(// 'user_repo' => 'https://github.com/yourUsername/yourRepositoryExtension',
			),
		),
	),
);