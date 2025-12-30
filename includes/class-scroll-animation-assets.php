<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Cinematic_Scroll_Assets' ) ) {
    /**
     * Handles registration and enqueueing of all plugin scripts and styles.
     * Centralizes asset management to ensure dependencies (GSAP, Pickr) are loaded correctly.
     */
    class Cinematic_Scroll_Assets {

        public function __construct() {
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
            add_action( 'wp_enqueue_scripts',    [ $this, 'enqueue_frontend_assets' ] );
        }

        public function enqueue_admin_assets( $hook ) {
            global $post;

            // Only load on our CPT post edit screens
            if ( in_array( $hook, [ 'post.php', 'post-new.php' ], true ) &&
                 isset( $post ) && $post->post_type === 'scroll_animation' ) {
                
                // 1. WordPress Media Uploader
                wp_enqueue_media();

                // 2. Local Libraries (Pickr)
                wp_enqueue_style( 'pickr-css', CGS_URL . 'assets/css/lib/pickr.min.css', [], '1.9.1' );
                wp_enqueue_script( 'pickr-js', CGS_URL . 'assets/js/lib/pickr.min.js', [], '1.9.1', true );

                // 3. Admin CSS/JS
                wp_enqueue_style( 'cgs-admin-css', CGS_URL . 'assets/css/gsap-scroll-admin.css', [], '1.3.1' ); // Bumped version
                wp_enqueue_script( 'cgs-admin-js', CGS_URL . 'assets/js/gsap-scroll-admin.js', ['jquery', 'jquery-ui-sortable', 'pickr-js'], '1.3.2', true );

                // 4. Centralized Data Localization
                // Combine data from Carousel Meta and Style Meta
                $items = get_post_meta($post->ID, '_cgs_carousel_items', true);
                $count = is_array($items) ? count($items) : 0;
                $layout = get_post_meta($post->ID, '_cgs_layout', true) ?: 'horizontal';
                
                // Get available fonts from Style Meta class (static method)
                // We ensure the class is loaded or check method existence.
                // Since this asset class handles enqueue, we assume other classes are loaded or we can replicate the logic?
                // Ideally, we shouldn't duplicate the font list.
                // We can check if class exists.
                $available_fonts = [];
                if ( class_exists( 'Cinematic_Scroll_Style_Meta' ) && method_exists( 'Cinematic_Scroll_Style_Meta', 'get_all_fonts' ) ) {
                    $available_fonts = Cinematic_Scroll_Style_Meta::get_all_fonts();
                } else {
                    // Fallback if class not loaded yet (though it should be)
                     $available_fonts = [ 'inherit' => 'Default' ]; 
                }

                // UNIFIED Data Object
                wp_localize_script( 'cgs-admin-js', 'CGS_Admin_Data', [
                    'postId' => $post->ID,
                    'itemIndex' => $count,
                    'layout' => $layout,
                    'available_fonts' => $available_fonts
                ]);
            }
        }

        public function enqueue_frontend_assets() {
            // Register Scripts
            wp_register_script( 'gsap', CGS_URL . 'assets/js/lib/gsap.min.js', [], '3.12.5', true );
            wp_register_script( 'gsap-scrolltrigger', CGS_URL . 'assets/js/lib/ScrollTrigger.min.js', ['gsap'], '3.12.5', true );
            
            // Register Styles
            wp_register_style( 'cgs-carousel', CGS_URL . 'assets/css/gsap-scroll-carousel.css', [], '1.3.1' );
            wp_register_style( 'cgs-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], '6.5.1' ); // Kept CDN as per plan

            // We do NOT enqueue them globally here unless we want them on every page.
            // Best practice: The shortcode handles the enqueue when it runs.
            // However, shortcode runs locally in content.
            // If we want to enqueue ONLY when shortcode is present, we usually do it inside the shortcode render function.
            // But since we want to CENTRALIZE registration, we register here.
            
            // Wait, the previous implementation had 'wp_enqueue_scripts' hook in the shortcode class.
            // We can keep the registration here, and let the shortcode call wp_enqueue_script().
        }
    }
}
