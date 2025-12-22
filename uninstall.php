<?php
// Prevent direct access to the file
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit; // Exit if accessed directly
}

// Define any plugin-specific options and database tables
$options = [
    'gsap_scroll_button_bg',
    'gsap_scroll_button_text',
    'gsap_scroll_card_bg',
    'gsap_scroll_txt_colr',
    'gsap_scroll_card_radius_top_lt',
    'gsap_scroll_card_radius_top_rt',
    'gsap_scroll_card_radius_btm_rt',
    'gsap_scroll_card_radius_btm_lt',
    'gsap_scroll_card_shadow',
    // Add more options here that you wish to delete
];

// Delete plugin options from the database
foreach ( $options as $option ) {
    delete_option( $option );
}

// Optionally, delete any custom database tables related to the plugin (if applicable)
global $wpdb;

// Optionally, remove custom post types and their metadata
$args = array(
    'post_type'      => 'scroll_animation', // Replace with your custom post type slug
    'posts_per_page' => -1,
);
$query = new WP_Query( $args );
if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
        $query->the_post();
        wp_delete_post( get_the_ID(), true ); // Delete post permanently
    }
    wp_reset_postdata();
}

// Optionally, delete any plugin-specific transients (if applicable)
delete_transient( 'gsap_scroll_transient' );
