<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$yolo_template_directory = get_template_directory_uri();

$options = array(
	'photo_options' => array(
		'type'    => 'box',
		'title'   => __( 'Photo Parameters', 'fw' ),
		'options' => array(
			'post_photo_param' => array(
				'type'          => 'multi',
				'label'         => false,
				'inner-options' => array(
					'photo_make'          => array(
						'type'  => 'text',
						'label' => 'MAKE',
						'desc'  => 'ex: Sony ',
					),
					'photo_model'         => array(
						'type'  => 'text',
						'label' => 'MODEL',
						'desc'  => 'ex: NEX-5R ',
					),
					'photo_shutter_speed' => array(
						'type'  => 'text',
						'label' => 'SHUTTER SPEED',
						'desc'  => 'ex: 1/50s',
					),
					'photo_aperture'      => array(
						'type'  => 'text',
						'label' => 'APERTURE',
						'desc'  => 'ex: f/4.5',
					),
					'photo_focal_length'  => array(
						'type'  => 'text',
						'label' => 'FOCAL LENGTH',
						'desc'  => 'ex: 47mm',
					),
					'photo_iso'           => array(
						'type'  => 'text',
						'label' => 'ISO',
						'desc'  => 'ex: 100',
					),
					'photo_dimension'     => array(
						'type'  => 'text',
						'label' => 'DIMENSIONS',
						'desc'  => 'ex: 3568 × 2368',
					),
					'photo_published'     => array(
						'type'            => 'datetime-picker',
						'value'           => '',
						'label'           => __( 'PUBLISHED ON', 'fw' ),
						'desc'            => __( 'Format default: m/d/Y H:i', 'fw' ),
						'datetime-picker' => array(
							'format'     => 'm/d/Y H:i',
							'maxDate'    => false,
							'minDate'    => false,
							'timepicker' => true,
							'datepicker' => true,
						),
					),
				)
			),
		),
	)
);
 