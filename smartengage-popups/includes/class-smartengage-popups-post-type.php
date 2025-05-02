<?php
/**
 * Class responsible for registering the custom post type.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Class for registering the custom post type.
 */
class SmartEngage_Popups_Post_Type {

    /**
     * Register the custom post type.
     *
     * @since    1.0.0
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x( 'Popups', 'Post Type General Name', 'smartengage-popups' ),
            'singular_name'         => _x( 'Popup', 'Post Type Singular Name', 'smartengage-popups' ),
            'menu_name'             => __( 'SmartEngage Popups', 'smartengage-popups' ),
            'name_admin_bar'        => __( 'Popup', 'smartengage-popups' ),
            'archives'              => __( 'Popup Archives', 'smartengage-popups' ),
            'attributes'            => __( 'Popup Attributes', 'smartengage-popups' ),
            'parent_item_colon'     => __( 'Parent Popup:', 'smartengage-popups' ),
            'all_items'             => __( 'All Popups', 'smartengage-popups' ),
            'add_new_item'          => __( 'Add New Popup', 'smartengage-popups' ),
            'add_new'               => __( 'Add New', 'smartengage-popups' ),
            'new_item'              => __( 'New Popup', 'smartengage-popups' ),
            'edit_item'             => __( 'Edit Popup', 'smartengage-popups' ),
            'update_item'           => __( 'Update Popup', 'smartengage-popups' ),
            'view_item'             => __( 'View Popup', 'smartengage-popups' ),
            'view_items'            => __( 'View Popups', 'smartengage-popups' ),
            'search_items'          => __( 'Search Popup', 'smartengage-popups' ),
            'not_found'             => __( 'Not found', 'smartengage-popups' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'smartengage-popups' ),
            'featured_image'        => __( 'Popup Image', 'smartengage-popups' ),
            'set_featured_image'    => __( 'Set popup image', 'smartengage-popups' ),
            'remove_featured_image' => __( 'Remove popup image', 'smartengage-popups' ),
            'use_featured_image'    => __( 'Use as popup image', 'smartengage-popups' ),
            'insert_into_item'      => __( 'Insert into popup', 'smartengage-popups' ),
            'uploaded_to_this_item' => __( 'Uploaded to this popup', 'smartengage-popups' ),
            'items_list'            => __( 'Popups list', 'smartengage-popups' ),
            'items_list_navigation' => __( 'Popups list navigation', 'smartengage-popups' ),
            'filter_items_list'     => __( 'Filter popups list', 'smartengage-popups' ),
        );
        
        $args = array(
            'label'                 => __( 'Popup', 'smartengage-popups' ),
            'description'           => __( 'SmartEngage Popup', 'smartengage-popups' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-megaphone',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'page',
            'show_in_rest'          => false,
        );
        
        register_post_type( 'smartengage_popup', $args );
        
        // Register popup status
        register_post_status( 'active', array(
            'label'                     => _x( 'Active', 'popup status', 'smartengage-popups' ),
            'public'                    => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'smartengage-popups' ),
        ) );
        
        register_post_status( 'inactive', array(
            'label'                     => _x( 'Inactive', 'popup status', 'smartengage-popups' ),
            'public'                    => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', 'smartengage-popups' ),
        ) );
    }
}
