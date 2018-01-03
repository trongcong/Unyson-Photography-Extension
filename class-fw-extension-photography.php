<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Extension_Photography extends FW_Extension {

	private $post_type = 'yolo-photography';
	private $slug = 'photo';
	private $taxonomy_slug = 'photography';
	private $taxonomy_name = 'yolo-photography-category';
	private $taxonomy_tag_name = 'yolo-photography-tag';
	private $taxonomy_tag_slug = 'photography-tag';

	/**
	 * @internal
	 */
	public function _init() {
		$this->define_slugs();

		add_action( 'init', array( $this, '_action_register_post_type' ) );
		add_action( 'init', array( $this, '_action_register_taxonomy' ) );

		if ( is_admin() ) {
			$this->save_permalink_structure();
			$this->add_admin_actions();
			$this->add_admin_filters();
		}
	}

	private function define_slugs() {
		$this->slug = apply_filters( 'fw_ext_photography_post_slug', $this->get_db_data( 'permalinks/post', $this->slug ) );

		$this->taxonomy_slug = apply_filters( 'fw_ext_photography_taxonomy_slug', $this->get_db_data( 'permalinks/taxonomy', $this->taxonomy_slug ) );
	}

	private function save_permalink_structure() {

		if ( ! isset( $_POST['permalink_structure'] ) && ! isset( $_POST['category_base'] ) ) {
			return;
		}

		$post = FW_Request::POST( 'fw_ext_photography_photo_slug', apply_filters( 'fw_ext_photography_post_slug', $this->slug ) );

		$taxonomy = FW_Request::POST( 'fw_ext_photography_photography_slug', apply_filters( 'fw_ext_photography_taxonomy_slug', $this->taxonomy_slug ) );


		$this->set_db_data( 'permalinks/post', $post );
		$this->set_db_data( 'permalinks/taxonomy', $taxonomy );
	}

	private function add_admin_actions() {
		add_action( 'admin_init', array( $this, '_action_add_permalink_in_settings' ) );
		add_action( 'admin_menu', array( $this, '_action_admin_rename_photos' ) );
		add_action( 'restrict_manage_posts', array( $this, '_action_admin_add_photography_edit_page_filter' ) );
		// listing screen
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array(
			$this,
			'_action_admin_manage_custom_column'
		), 10, 2 );

		// add / edit screen
		add_action( 'do_meta_boxes', array( $this, '_action_admin_featured_image_label' ) );

		add_action( 'admin_enqueue_scripts', array( $this, '_action_admin_add_static' ) );

		add_action( 'admin_head', array( $this, '_action_admin_initial_nav_menu_meta_boxes' ), 999 );
	}

	public function add_admin_filters() {
		add_filter( 'parse_query', array( $this, '_filter_admin_filter_photographys_by_photography_category' ), 10, 2 );
		add_filter( 'months_dropdown_results', array( $this, '_filter_admin_remove_select_by_date_filter' ) );
		add_filter( 'manage_edit-' . $this->post_type . '_columns', array(
			$this,
			'_filter_admin_manage_edit_columns'
		), 10, 1 );

		if ( $this->get_config( 'has-gallery' ) === true ) {
			add_filter( 'fw_post_options', array( $this, '_filter_admin_add_post_options' ), 10, 2 );
		}
	}

	/**
	 * @internal
	 **/
	public function _action_add_permalink_in_settings() {
		add_settings_field( 'fw_ext_photography_photo_slug', __( 'Photo base', 'yolo' ), array(
			$this,
			'_photo_slug_input'
		), 'permalink', 'optional' );

		add_settings_field( 'fw_ext_photography_photography_slug', __( 'Photography category base', 'yolo' ), array(
			$this,
			'_photography_slug_input'
		), 'permalink', 'optional' );
	}

	/**
	 * @internal
	 */
	public function _photo_slug_input() {
		?>
        <input type="text" name="fw_ext_photography_photo_slug" value="<?php echo $this->slug; ?>">
        <code>/my-photo</code>
		<?php
	}

	/**
	 * @internal
	 */
	public function _photography_slug_input() {
		?>
        <input type="text" name="fw_ext_photography_photography_slug" value="<?php echo $this->taxonomy_slug; ?>">
        <code>/my-photography</code>
		<?php
	}

	/**
	 * @internal
	 */
	public function _action_admin_add_static() {
		$photos_listing_screen  = array(
			'only' => array(
				array(
					'post_type' => $this->post_type,
					'base'      => array( 'edit' )
				)
			)
		);
		$photos_add_edit_screen = array(
			'only' => array(
				array(
					'post_type' => $this->post_type,
					'base'      => 'post'
				)
			)
		);

		if ( fw_current_screen_match( $photos_listing_screen ) ) {
			wp_enqueue_style( 'fw-extension-' . $this->get_name() . '-listing', $this->get_declared_URI( '/static/css/admin-listing.css' ), array(), fw()->manifest->get_version() );
		}

		if ( fw_current_screen_match( $photos_add_edit_screen ) ) {
			wp_enqueue_style( 'fw-extension-' . $this->get_name() . '-add-edit', $this->get_declared_URI( '/static/css/admin-add-edit.css' ), array(), fw()->manifest->get_version() );
			wp_enqueue_script( 'fw-extension-' . $this->get_name() . '-add-edit', $this->get_declared_URI( '/static/js/admin-add-edit.js' ), array( 'jquery' ), fw()->manifest->get_version(), true );
		}
	}

	/**
	 * @internal
	 */
	public function _action_register_post_type() {

		$post_names = apply_filters( 'fw_ext_photos_post_type_name', array(
			'singular' => __( 'Photo', 'yolo' ),
			'plural'   => __( 'Photos', 'yolo' )
		) );

		register_post_type( $this->post_type, array(
			'labels'             => array(
				'name'               => $post_names['plural'], //__( 'Photography', 'yolo' ),
				'singular_name'      => $post_names['singular'], //__( 'Photography photo', 'yolo' ),
				'add_new'            => __( 'Add New', 'yolo' ),
				'add_new_item'       => sprintf( __( 'Add New %s', 'yolo' ), $post_names['singular'] ),
				'edit'               => __( 'Edit', 'yolo' ),
				'edit_item'          => sprintf( __( 'Edit %s', 'yolo' ), $post_names['singular'] ),
				'new_item'           => sprintf( __( 'New %s', 'yolo' ), $post_names['singular'] ),
				'all_items'          => sprintf( __( 'All %s', 'yolo' ), $post_names['plural'] ),
				'view'               => sprintf( __( 'View %s', 'yolo' ), $post_names['singular'] ),
				'view_item'          => sprintf( __( 'View %s', 'yolo' ), $post_names['singular'] ),
				'search_items'       => sprintf( __( 'Search %s', 'yolo' ), $post_names['plural'] ),
				'not_found'          => sprintf( __( 'No %s Found', 'yolo' ), $post_names['plural'] ),
				'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', 'yolo' ), $post_names['plural'] ),
				'parent_item_colon'  => '' /* text for parent types */
			),
			'description'        => __( 'Create a photography item', 'yolo' ),
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'publicly_queryable' => true,
			/* queries can be performed on the front end */
			'has_archive'        => true,
			'rewrite'            => array(
				'slug' => $this->slug
			),
			'menu_position'      => 4,
			'show_in_nav_menus'  => true,
			'menu_icon'          => 'dashicons-format-gallery',
			'hierarchical'       => false,
			'query_var'          => true,
			/* Sets the query_var key for this post type. Default: true - set to $post_type */
			'supports'           => array(
				'title', /* Text input field to create a post title. */
				'editor',
				'thumbnail', /* Displays a box for featured image. */
			),
			'capabilities'       => array(
				'edit_post'              => 'edit_pages',
				'read_post'              => 'edit_pages',
				'delete_post'            => 'edit_pages',
				'edit_posts'             => 'edit_pages',
				'edit_others_posts'      => 'edit_pages',
				'publish_posts'          => 'edit_pages',
				'read_private_posts'     => 'edit_pages',
				'read'                   => 'edit_pages',
				'delete_posts'           => 'edit_pages',
				'delete_private_posts'   => 'edit_pages',
				'delete_published_posts' => 'edit_pages',
				'delete_others_posts'    => 'edit_pages',
				'edit_private_posts'     => 'edit_pages',
				'edit_published_posts'   => 'edit_pages',
			),
		) );

	}

	/**
	 * @internal
	 */
	public function _action_register_taxonomy() {

		$category_names = apply_filters( 'fw_ext_photography_category_name', array(
			'singular' => __( 'Category', 'yolo' ),
			'plural'   => __( 'Categories', 'yolo' )
		) );

		register_taxonomy( $this->taxonomy_name, $this->post_type, array(
			'labels'            => array(
				'name'              => sprintf( _x( 'Photography %s', 'taxonomy general name', 'yolo' ), $category_names['plural'] ),
				'singular_name'     => sprintf( _x( 'Photography %s', 'taxonomy singular name', 'yolo' ), $category_names['singular'] ),
				'search_items'      => sprintf( __( 'Search %s', 'yolo' ), $category_names['plural'] ),
				'all_items'         => sprintf( __( 'All %s', 'yolo' ), $category_names['plural'] ),
				'parent_item'       => sprintf( __( 'Parent %s', 'yolo' ), $category_names['singular'] ),
				'parent_item_colon' => sprintf( __( 'Parent %s:', 'yolo' ), $category_names['singular'] ),
				'edit_item'         => sprintf( __( 'Edit %s', 'yolo' ), $category_names['singular'] ),
				'update_item'       => sprintf( __( 'Update %s', 'yolo' ), $category_names['singular'] ),
				'add_new_item'      => sprintf( __( 'Add New %s', 'yolo' ), $category_names['singular'] ),
				'new_item_name'     => sprintf( __( 'New %s Name', 'yolo' ), $category_names['singular'] ),
				'menu_name'         => $category_names['plural'],
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'rewrite'           => array(
				'slug' => $this->taxonomy_slug
			),
		) );

		if ( apply_filters( 'fw:ext:photography:enable-tags', false ) ) {
			$tag_names = apply_filters( 'fw_ext_photography_tag_name', array(
				'singular' => __( 'Tag', 'yolo' ),
				'plural'   => __( 'Tags', 'yolo' )
			) );

			register_taxonomy( $this->taxonomy_tag_name, $this->post_type, array(
				'hierarchical' => false,
				'labels'       => array(
					'name'                       => $tag_names['plural'],
					'singular_name'              => $tag_names['singular'],
					'search_items'               => sprintf( __( 'Search %s', 'yolo' ), $tag_names['plural'] ),
					'popular_items'              => sprintf( __( 'Popular %s', 'yolo' ), $tag_names['plural'] ),
					'all_items'                  => sprintf( __( 'All %s', 'yolo' ), $tag_names['plural'] ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
					'edit_item'                  => sprintf( __( 'Edit %s', 'yolo' ), $tag_names['singular'] ),
					'update_item'                => sprintf( __( 'Update %s', 'yolo' ), $tag_names['singular'] ),
					'add_new_item'               => sprintf( __( 'Add New %s', 'yolo' ), $tag_names['singular'] ),
					'new_item_name'              => sprintf( __( 'New %s Name', 'yolo' ), $tag_names['singular'] ),
					'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'yolo' ), strtolower( $tag_names['plural'] ) ),
					'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'yolo' ), strtolower( $tag_names['plural'] ) ),
					'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'yolo' ), strtolower( $tag_names['plural'] ) ),
				),
				'public'       => true,
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array(
					'slug' => $this->taxonomy_tag_slug
				),
			) );
		}
	}

	/**
	 * @internal
	 *
	 * @param array $options
	 * @param string $post_type
	 *
	 * @return array
	 */
	public function _filter_admin_add_post_options( $options, $post_type ) {
		if ( $post_type === $this->post_type ) {
			$options[] = array(
				'general' => array(
					'context' => 'side',
					'title'   => __( 'Photo', 'yolo' ) . ' ' . __( 'Gallery', 'yolo' ),
					'type'    => 'box',
					'options' => array(
						'photography-gallery' => array(
							'label' => false,
							'type'  => 'multi-upload',
							'desc'  => false,
							'texts' => array(
								'button_add'  => __( 'Set photo gallery', 'yolo' ),
								'button_edit' => __( 'Edit photo gallery', 'yolo' )
							)
						)
					)
				)
			);
		}

		return $options;
	}

	/**
	 * internal
	 */
	public function _action_admin_rename_photos() {
		global $menu;

		foreach ( $menu as $key => $menu_item ) {
			if ( $menu_item[2] == 'edit.php?post_type=' . $this->post_type ) {
				$menu[ $key ][0] = __( 'Photography', 'yolo' );
			}
		}
	}

	/**
	 * Change the title of Featured Image Meta box
	 * @internal
	 */
	public function _action_admin_featured_image_label() {
		remove_meta_box( 'postimagediv', $this->post_type, 'side' );
		add_meta_box( 'postimagediv', __( 'Cover Image', 'yolo' ), 'post_thumbnail_meta_box', $this->post_type, 'side' );
	}

	/**
	 * @internal
	 *
	 * @param string $column_name
	 * @param int $id
	 */
	public function _action_admin_manage_custom_column( $column_name, $id ) {

		switch ( $column_name ) {
			case 'image':
				if ( get_the_post_thumbnail( intval( $id ) ) ) {
					$value = '<a href="' . get_edit_post_link( $id, true ) . '" title="' . esc_attr( __( 'Edit this item', 'yolo' ) ) . '">' . wp_get_attachment_image( get_post_thumbnail_id( intval( $id ) ), array(
							150,
							100
						), true ) . '</a>';
				} else {
					$value = '<img src="' . $this->get_declared_URI( '/static/images/no-image.png' ) . '"/>';
				}
				echo $value;
				break;

			default:
				break;
		}
	}

	/**
	 * @internal
	 */
	public function _action_admin_initial_nav_menu_meta_boxes() {
		$screen = array(
			'only' => array(
				'base' => 'nav-menus'
			)
		);
		if ( ! fw_current_screen_match( $screen ) ) {
			return;
		}

		if ( get_user_option( 'fw-metaboxhidden_nav-menus' ) !== false ) {
			return;
		}

		$user              = wp_get_current_user();
		$hidden_meta_boxes = get_user_meta( $user->ID, 'metaboxhidden_nav-menus' );

		if ( $key = array_search( 'add-' . $this->taxonomy_name, $hidden_meta_boxes[0] ) ) {
			unset( $hidden_meta_boxes[0][ $key ] );
		}

		update_user_option( $user->ID, 'metaboxhidden_nav-menus', $hidden_meta_boxes[0], true );
		update_user_option( $user->ID, 'fw-metaboxhidden_nav-menus', 'updated', true );
	}

	/**
	 * @internal
	 */
	public function _action_admin_add_photography_edit_page_filter() {
		$screen = fw_current_screen_match( array(
			'only' => array(
				'base'      => 'edit',
				'id'        => 'edit-' . $this->post_type,
				'post_type' => $this->post_type,
			)
		) );

		if ( ! $screen ) {
			return;
		}

		$terms = get_terms( $this->taxonomy_name );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			echo '<select name="' . $this->get_name() . '-filter-by-photography-category"><option value="0">' . __( 'View all categories', 'yolo' ) . '</option></select>';

			return;
		}

		$get = FW_Request::GET( $this->get_name() . '-filter-by-photography-category' );
		$id  = ( ! empty( $get ) ) ? (int)$get : 0;

		$dropdown_options = array(
			'selected'        => $id,
			'name'            => $this->get_name() . '-filter-by-photography-category">',
			'taxonomy'        => $this->taxonomy_name,
			'show_option_all' => __( 'View all categories', 'yolo' ),
			'hide_empty'      => true,
			'hierarchical'    => 1,
			'show_count'      => 0,
			'orderby'         => 'name',
		);

		wp_dropdown_categories( $dropdown_options );
	}

	/**
	 * @internal
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function _filter_admin_manage_edit_columns( $columns ) {
		$new_columns          = array();
		$new_columns['cb']    = $columns['cb']; // checkboxes for all photos page
		$new_columns['image'] = __( 'Cover Image', 'yolo' );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * @internal
	 *
	 * @param WP_Query $query
	 *
	 * @return WP_Query
	 */
	public function _filter_admin_filter_photographys_by_photography_category( $query ) {
		$screen = fw_current_screen_match( array(
			'only' => array(
				'base'      => 'edit',
				'id'        => 'edit-' . $this->post_type,
				'post_type' => $this->post_type,
			)
		) );

		if ( ! $screen || ! $query->is_main_query() ) {
			return $query;
		}

		$filter_value = FW_Request::GET( $this->get_name() . '-filter-by-photography-category' );

		if ( empty( $filter_value ) ) {
			return $query;
		}

		$filter_value = (int)$filter_value;

		$query->set( 'tax_query', array(
			array(
				'taxonomy' => $this->taxonomy_name,
				'field'    => 'id',
				'terms'    => $filter_value,
			)
		) );

		return $query;
	}

	/**
	 * @internal
	 *
	 * @param array $filters
	 *
	 * @return array
	 */
	public function _filter_admin_remove_select_by_date_filter( $filters ) {
		$screen = array(
			'only' => array(
				'base' => 'edit',
				'id'   => 'edit-' . $this->post_type,
			)
		);

		if ( ! fw_current_screen_match( $screen ) ) {
			return $filters;
		}

		return array();
	}

	/**
	 * @internal
	 * @return string
	 */
	public function _get_link() {
		return self_admin_url( 'edit.php?post_type=' . $this->post_type );
	}

	public function get_settings() {

		$response = array(
			'post_type'     => $this->post_type,
			'slug'          => $this->slug,
			'taxonomy_slug' => $this->taxonomy_slug,
			'taxonomy_name' => $this->taxonomy_name
		);

		return $response;
	}

	public function get_image_sizes() {
		return $this->get_config( 'image_sizes' );
	}

	public function get_post_type_name() {
		return $this->post_type;
	}

	public function get_taxonomy_name() {
		return $this->taxonomy_name;
	}
}