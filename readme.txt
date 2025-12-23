=== GSAP Scroll Plugin ===
Contributors: ashutiwary
Tags: gsap, scrolltrigger, animation, carousel, cinematic
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.3.1
Requires PHP: 7.4
License: GPLv2 or later

Create animated, GSAP-powered scrollable carousels with custom content using a simple shortcode.

== Description ==

The GSAP Scroll plugin is a premium WordPress plugin that integrates the GreenSock Animation Platform (GSAP) with scroll-based animations. It allows you to create engaging, animated carousels where content cards animate smoothly as users scroll.

With a modular architecture and detailed customization controls, you can build horizontal or vertical scroll experiences without writing code.

**Key Features:**

*   **Dual Layout Modes:** Choose between Horizontal Scroll (side-scrolling) or Vertical Stack layouts.
*   **Privacy & Performance:** Core libraries (GSAP, ScrollTrigger, Pickr) are now **bundled locally**, eliminating external CDN dependencies for better privacy and reliability.
*   **Advanced Typography:** deeply customizable typography settings (Font Family, Weight, Style, Transform, etc.) with Google Fonts integration.
*   **Button Styling:** Dedicated controls for button dimensions, padding, border radius, hover effects, and FontAwesome icons.
*   **Visual Editor:** Intuitive admin interface with real-time color pickers (Pickr) and drag-and-drop reordering.
*   **Responsive:** Built-in responsiveness to ensure animations look great on all devices.
*   **Security:** Audited codebase with strict input sanitization, output escaping, and nonce verification.

== Installation ==

1. Upload the `gsap-scroll` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to 'Cinematic Scrolls' in your admin dashboard.

== Usage ==

1.  **Create:** Go to 'Cinematic Scrolls' > 'Add New'.
2.  **Configure:**
    *   Select Layout: Horizontal or Vertical.
    *   Add Items: Title, Body, Images, and Buttons.
    *   Style: Use the meta boxes to set colors, fonts, borders, and shadows.
3.  **Embed:** Copy the shortcode `[cinematic_scroll id="123"]` and paste it into any Post or Page.

== Changelog ==

= 1.3.1 =
*   **Security:** Completed comprehensive security audit. Refactored asset management for better security compliance.
*   **Performance:** Bundled GSAP, ScrollTrigger, and Pickr libraries locally to reduce external HTTP requests and improve privacy.
*   **Feature:** Added centralized Asset Manager class.
*   **Update:** Unified Admin JavaScript data handling.

= 1.3.0 =
*   **Feature:** Added comprehensive Typography controls (Google Fonts support).
*   **Feature:** Added Vertical Stack layout option.
*   **UI:** Refined Admin UI with new color picker integration.

= 1.2.0 =
*   **Feature:** Button Hover Effects (Lift, Scale, Shadow).
*   **Feature:** Detailed Button Border and Padding controls.

= 1.0.0 =
*   Initial release.