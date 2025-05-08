<?php
/**
 * Custom Post Types
 *
 * @package SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SmartEngage Post Types
 */
class SmartEngage_Post_Types {

    /**
     * Initialize the class
     */
    public function init() {
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_filter( 'manage_smartengage_popup_posts_columns', array( $this, 'set_custom_columns' ) );
        add_action( 'manage_smartengage_popup_posts_custom_column', array( $this, 'custom_column_content' ), 10, 2 );
    }

    /**
     * Register post types
     */
    public function register_post_types() {
        // Register Popup post type
        $labels = array(
            'name'                  => _x( 'Popups', 'Post type general name', 'smartengage-popups' ),
            'singular_name'         => _x( 'Popup', 'Post type singular name', 'smartengage-popups' ),
            'menu_name'             => _x( 'SmartEngage', 'Admin Menu text', 'smartengage-popups' ),
            'name_admin_bar'        => _x( 'Popup', 'Add New on Toolbar', 'smartengage-popups' ),
            'add_new'               => __( 'Add New', 'smartengage-popups' ),
            'add_new_item'          => __( 'Add New Popup', 'smartengage-popups' ),
            'new_item'              => __( 'New Popup', 'smartengage-popups' ),
            'edit_item'             => __( 'Edit Popup', 'smartengage-popups' ),
            'view_item'             => __( 'View Popup', 'smartengage-popups' ),
            'all_items'             => __( 'All Popups', 'smartengage-popups' ),
            'search_items'          => __( 'Search Popups', 'smartengage-popups' ),
            'parent_item_colon'     => __( 'Parent Popups:', 'smartengage-popups' ),
            'not_found'             => __( 'No popups found.', 'smartengage-popups' ),
            'not_found_in_trash'    => __( 'No popups found in Trash.', 'smartengage-popups' ),
            'featured_image'        => _x( 'Popup Image', 'Overrides the "Featured Image" phrase', 'smartengage-popups' ),
            'set_featured_image'    => _x( 'Set popup image', 'Overrides the "Set featured image" phrase', 'smartengage-popups' ),
            'remove_featured_image' => _x( 'Remove popup image', 'Overrides the "Remove featured image" phrase', 'smartengage-popups' ),
            'use_featured_image'    => _x( 'Use as popup image', 'Overrides the "Use as featured image" phrase', 'smartengage-popups' ),
            'archives'              => _x( 'Popup archives', 'The post type archive label used in nav menus', 'smartengage-popups' ),
            'attributes'            => _x( 'Popup attributes', 'The post type attributes label', 'smartengage-popups' ),
            'insert_into_item'      => _x( 'Insert into popup', 'Overrides the "Insert into post" phrase', 'smartengage-popups' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this popup', 'Overrides the "Uploaded to this post" phrase', 'smartengage-popups' ),
            'filter_items_list'     => _x( 'Filter popups list', 'Screen reader text for the filter links', 'smartengage-popups' ),
            'items_list_navigation' => _x( 'Popups list navigation', 'Screen reader text for the pagination', 'smartengage-popups' ),
            'items_list'            => _x( 'Popups list', 'Screen reader text for the items list', 'smartengage-popups' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'smartengage-popup' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 25,
            'menu_icon'          => 'dashicons-megaphone',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
            'show_in_rest'       => true, // Enable Gutenberg editor
        );

        register_post_type( 'smartengage_popup', $args );
    }

    /**
     * Register custom taxonomies for popups
     */
    public function register_taxonomies() {
        // Register Category taxonomy for popups
        $labels = array(
            'name'                       => _x( 'Popup Categories', 'Taxonomy general name', 'smartengage-popups' ),
            'singular_name'              => _x( 'Popup Category', 'Taxonomy singular name', 'smartengage-popups' ),
            'search_items'               => __( 'Search Popup Categories', 'smartengage-popups' ),
            'popular_items'              => __( 'Popular Popup Categories', 'smartengage-popups' ),
            'all_items'                  => __( 'All Popup Categories', 'smartengage-popups' ),
            'parent_item'                => __( 'Parent Popup Category', 'smartengage-popups' ),
            'parent_item_colon'          => __( 'Parent Popup Category:', 'smartengage-popups' ),
            'edit_item'                  => __( 'Edit Popup Category', 'smartengage-popups' ),
            'update_item'                => __( 'Update Popup Category', 'smartengage-popups' ),
            'add_new_item'               => __( 'Add New Popup Category', 'smartengage-popups' ),
            'new_item_name'              => __( 'New Popup Category Name', 'smartengage-popups' ),
            'separate_items_with_commas' => __( 'Separate popup categories with commas', 'smartengage-popups' ),
            'add_or_remove_items'        => __( 'Add or remove popup categories', 'smartengage-popups' ),
            'choose_from_most_used'      => __( 'Choose from the most used popup categories', 'smartengage-popups' ),
            'not_found'                  => __( 'No popup categories found.', 'smartengage-popups' ),
            'menu_name'                  => __( 'Categories', 'smartengage-popups' ),
        );

        $args = array(
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'popup-category' ),
            'show_in_rest'          => true,
        );

        register_taxonomy( 'popup_category', array( 'smartengage_popup' ), $args );
    }

    /**
     * Set custom columns for popup post type
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public function set_custom_columns( $columns ) {
        $new_columns = array();
        
        // Insert columns after title
        foreach ( $columns as $key => $value ) {
            $new_columns[ $key ] = $value;
            
            if ( 'title' === $key ) {
                $new_columns['popup_type']   = __( 'Type', 'smartengage-popups' );
                $new_columns['popup_status'] = __( 'Status', 'smartengage-popups' );
                $new_columns['impressions']  = __( 'Impressions', 'smartengage-popups' );
                $new_columns['conversions']  = __( 'Conversions', 'smartengage-popups' );
                $new_columns['conversion_rate'] = __( 'Conv. Rate', 'smartengage-popups' );
            }
        }
        
        return $new_columns;
    }

    /**
     * Custom column content for popup post type
     *
     * @param string $column  Column ID.
     * @param int    $post_id Post ID.
     */
    public function custom_column_content( $column, $post_id ) {
        switch ( $column ) {
            case 'popup_type':
                $popup_type = get_post_meta( $post_id, '_smartengage_popup_type', true );
                echo esc_html( ucfirst( str_replace( '-', ' ', $popup_type ) ) );
                break;
                
            case 'popup_status':
                $status = get_post_meta( $post_id, '_smartengage_popup_status', true );
                if ( 'enabled' === $status ) {
                    echo '<span class="smartengage-status enabled">' . esc_html__( 'Active', 'smartengage-popups' ) . '</span>';
                } else {
                    echo '<span class="smartengage-status disabled">' . esc_html__( 'Disabled', 'smartengage-popups' ) . '</span>';
                }
                break;
                
            case 'impressions':
                $impressions = get_post_meta( $post_id, '_smartengage_impressions', true );
                echo esc_html( !empty( $impressions ) ? number_format( $impressions ) : '0' );
                break;
                
            case 'conversions':
                $conversions = get_post_meta( $post_id, '_smartengage_conversions', true );
                echo esc_html( !empty( $conversions ) ? number_format( $conversions ) : '0' );
                break;
                
            case 'conversion_rate':
                $impressions = (int) get_post_meta( $post_id, '_smartengage_impressions', true );
                $conversions = (int) get_post_meta( $post_id, '_smartengage_conversions', true );
                
                if ( $impressions > 0 ) {
                    $rate = ( $conversions / $impressions ) * 100;
                    echo esc_html( number_format( $rate, 1 ) . '%' );
                } else {
                    echo esc_html( '0%' );
                }
                break;
        }
    }
}
