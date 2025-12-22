<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once CGS_DIR . 'includes/class-scroll-animation-style-meta.php';
require_once CGS_DIR . 'includes/class-scroll-animation-carousel-meta.php';

if ( ! class_exists( 'Cinematic_Scroll_Meta' ) ) {
    class Cinematic_Scroll_Meta {
        public function __construct() {
            // These are now instantiated in the main plugin file, so we might not need to do it here
            // to avoid duplication if the main file does it.
            // However, to keep legacy structure working if someone relies on this wrapper:
            if ( ! class_exists('Cinematic_Scroll_Style_Meta') ) {
                 // require handled above
            }
            // Actually, best current practice in this refactor:
            // Since gsap-scroll.php instantiates all of them, this wrapper class might be empty or deprecated.
            // Let's keep it empty or just ensuring logic is loaded.
        }
    }
}
// Removed self-instantiation
