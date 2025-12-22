<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (! class_exists('Cinematic_Scroll_CPT')) {
    class Cinematic_Scroll_CPT
    {
        public function __construct()
        {
            add_action('init', array($this, 'register_scroll_animation_cpt'));
            add_filter('use_block_editor_for_post_type', array($this, 'disable_gutenberg_for_scroll_animation'), 10, 2);
            add_action('add_meta_boxes', array($this, 'remove_default_meta_boxes'), 99);
            add_filter('manage_edit-scroll_animation_columns', array($this, 'add_shortcode_column'));
            add_action('manage_scroll_animation_posts_custom_column', array($this, 'render_shortcode_column'), 10, 2);
            add_filter('manage_edit-scroll_animation_columns', array($this, 'remove_comments_column'), 20);
        }

        public function register_scroll_animation_cpt()
        {
            $labels = array(
                'name'                  => _x('Cinematic Scrollys', 'Post Type General Name', 'cinematic-scroll'),
                'singular_name'         => _x('Cinematic Scroll', 'Post Type Singular Name', 'cinematic-scroll'),
                'menu_name'             => __('Cinematic Scrollys', 'cinematic-scroll'),
                'name_admin_bar'        => __('Cinematic Scroll', 'cinematic-scroll'),
                'add_new'               => __('Add New', 'cinematic-scroll'),
                'add_new_item'          => __('Add New Cinematic Scroll', 'cinematic-scroll'),
                'edit_item'             => __('Edit Cinematic Scroll', 'cinematic-scroll'),
                'new_item'              => __('New Cinematic Scroll', 'cinematic-scroll'),
                'view_item'             => __('View Cinematic Scroll', 'cinematic-scroll'),
                'search_items'          => __('Search Cinematic Scrolls', 'cinematic-scroll'),
                'not_found'             => __('No cinematic scrolls found', 'cinematic-scroll'),
                'not_found_in_trash'    => __('No cinematic scrolls found in Trash', 'cinematic-scroll'),
                'all_items'             => __('All Cinematic Scrolls', 'cinematic-scroll'),
                'archives'              => __('Cinematic Scroll Archives', 'cinematic-scroll'),
                'insert_into_item'      => __('Insert into cinematic scroll', 'cinematic-scroll'),
                'uploaded_to_this_item' => __('Uploaded to this cinematic scroll', 'cinematic-scroll'),
            );

            $args = array(
                'label'                 => __('Cinematic Scroll', 'cinematic-scroll'),
                'description'           => __('A custom post type for cinematic scroll animations.', 'cinematic-scroll'),
                'labels'                => $labels,
                'supports'              => array('title', 'comments'),
                'public'                => false,
                'publicly_queryable'    => false,
                'exclude_from_search'   => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 5,
                'menu_icon'             => 'dashicons-schedule',
                'has_archive'           => false,
                'rewrite'               => false,
                'show_in_rest'          => false, // Disable Gutenberg
            );

            register_post_type('scroll_animation', $args);
        }

        public function disable_gutenberg_for_scroll_animation($use_block_editor, $post_type)
        {
            if ($post_type === 'scroll_animation') {
                return false;
            }
            return $use_block_editor;
        }

        public function remove_default_meta_boxes()
        {
            remove_meta_box('commentstatusdiv', 'scroll_animation', 'normal'); // Comments status
            remove_meta_box('commentsdiv', 'scroll_animation', 'normal');      // Comments
            remove_meta_box('slugdiv', 'scroll_animation', 'normal');          // Slug
            remove_meta_box('authordiv', 'scroll_animation', 'normal');        // Author
            remove_meta_box('trackbacksdiv', 'scroll_animation', 'normal');    // Trackbacks
            remove_meta_box('postcustom', 'scroll_animation', 'normal');       // Custom fields
            remove_meta_box('revisionsdiv', 'scroll_animation', 'normal');     // Revisions
            remove_meta_box('postexcerpt', 'scroll_animation', 'normal');      // Excerpt
            remove_meta_box('discussiondiv', 'scroll_animation', 'normal');    // Discussion
            remove_meta_box('postimagediv', 'scroll_animation', 'side');       // Featured image
        }

        public function add_shortcode_column($columns)
        {
            $new_columns = array();
            foreach ($columns as $key => $value) {
                $new_columns[$key] = $value;
                if ($key === 'title') {
                    $new_columns['gsap_shortcode'] = __('Shortcode', 'cinematic-scroll');
                }
            }
            return $new_columns;
        }

        public function render_shortcode_column($column, $post_id)
        {
            if ($column === 'gsap_shortcode') {
                echo '<code>[cinematic_scroll id="' . esc_attr($post_id) . '"]</code>';
            }
        }

        public function remove_comments_column($columns)
        {
            if (isset($columns['comments'])) {
                unset($columns['comments']);
            }
            return $columns;
        }
    }
}
