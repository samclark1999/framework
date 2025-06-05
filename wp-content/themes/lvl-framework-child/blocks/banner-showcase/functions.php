<?php

add_action( 'init', function () {

	if ( class_exists( 'Level\PostTypes' ) ) {
		(new Level\PostTypes)->registerCPT('banner', 'Banner', 'Banners',
			[],
				'private',
			[
				'capability_type'     => 'post',
				'public'              => false,
				'has_archive'         => false,
				'show_in_nav_menus'   => false,
				'exclude_from_search' => true,
						//            'show_in_rest'        => false,
						//'template'            => array(
						//					array( 'lvl/banner', array() ),
						//				),
						'template'            =>
								array(
										array(
												'lvl/banner',
												array(
														'name' => 'lvl/banner',
														'data' => array(
																'field_65808a34b1252' => array(
																		'field_65808a6bb1254' => '',
																		'field_65b945d12d27d' => 'center',
																		'field_65b946022d27e' => 'cover',
																		'field_65b94c4f5123a' => '1',
																		'field_65b94caa5123b' => '1'
																),
																'field_hero_657c9d3f053d6' => '1',
																'field_65aff5ef887d0' => '400',
																'field_65808bc2b1259' => '100',
																'field_6580b4e2a1968' => 'end',
																'field_6580b4e2a1968_padding' => '0',
																'field_65ef3e1418dc8' => array(
																		'title' => '',
																		'url' => '',
																		'target' => ''
																)
														),
														'mode' => 'preview',
														'backgroundColor' => 'bs-dark',
														'style' => array(),
														'bs' => array(
																'theme' => 'dark'
														)
												),
												array()
										),
				),
				'template_lock'       => 'insert',
				'rewrite'             => array( 'slug' => 'banners', 'with_front' => false ),
				'menu_icon'           => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-images" viewBox="0 0 16 16"><path d="M4.502 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/><path d="M14.002 13a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2V5A2 2 0 0 1 2 3a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v8a2 2 0 0 1-1.998 2M14 2H4a1 1 0 0 0-1 1h9.002a2 2 0 0 1 2 2v7A1 1 0 0 0 15 11V3a1 1 0 0 0-1-1M2.002 4a1 1 0 0 0-1 1v8l2.646-2.354a.5.5 0 0 1 .63-.062l2.66 1.773 3.71-3.71a.5.5 0 0 1 .577-.094l1.777 1.947V5a1 1 0 0 0-1-1h-10"/></svg>' ),
				'supports'            => array( 'editor', 'author', 'title', 'custom-fields' ),
						//            'menu_position'       => 5,
			]
		);

		if ( function_exists( 'acf_add_local_field_group' ) ) {
			acf_add_local_field_group( array(
				'key'                   => 'group_banner_schedule_key',
				'title'                 => 'Schedule',
				'fields'                => array(
					array(
						'key'            => 'field_banner_schedule_start_key',
						'label'          => 'Start',
						'name'           => 'start',
						'type'           => 'date_time_picker',
						'display_format' => 'F j, Y g:i a',
						'return_format'  => 'Y-m-d H:i:s',
						'first_day'      => 1,
					),
					array(
						'key'            => 'field_banner_schedule_end_key',
						'label'          => 'End',
						'name'           => 'end',
						'type'           => 'date_time_picker',
						'display_format' => 'F j, Y g:i a',
						'return_format'  => 'Y-m-d H:i:s',
						'first_day'      => 1,
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'banner',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'side',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			) );
		}
	}

} );