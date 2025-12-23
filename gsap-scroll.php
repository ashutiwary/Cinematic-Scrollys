<?php
/*
Plugin Name: Cinematic Scrollys
Description: Create animated, GSAP-powered scrollable carousels with custom content using a simple shortcode.
Version: 1.0.0
Author: Ashu Tiwary
*/

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define constants for the plugin
if (! defined('CGS_URL'))  define('CGS_URL', plugin_dir_url(__FILE__));
if (! defined('CGS_DIR'))  define('CGS_DIR', plugin_dir_path(__FILE__));

// Require the class file
require_once plugin_dir_path(__FILE__) . 'includes/class-scroll-animation-cpt.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-scroll-animation-meta.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-scroll-animation-carousel-meta.php'; // Ensure this is required if not auto-loaded
require_once plugin_dir_path(__FILE__) . 'includes/class-scroll-animation-style-meta.php'; // Ensure this is required
require_once plugin_dir_path(__FILE__) . 'includes/class-scroll-animation-assets.php'; // New Asset Manager
require_once plugin_dir_path(__FILE__) . 'includes/class-scroll-animation-shortcode.php';

// Instantiate the classes
new Cinematic_Scroll_CPT();
new Cinematic_Scroll_Meta(); // If this base class exists
new Cinematic_Scroll_Carousel_Items_Meta(); // Explicitly instantiate
new Cinematic_Scroll_Style_Meta(); // Explicitly instantiate
new Cinematic_Scroll_Assets(); // Centralized Asset Manager (Handles Enqueues)
new Cinematic_Scroll_Shortcode();
