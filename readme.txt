GSAP Scroll Plugin
==================

Description:
-------------
The GSAP Scroll plugin is a custom WordPress plugin that integrates the GreenSock Animation Platform (GSAP) with scroll-based animations for your WordPress site. This plugin allows you to create engaging, animated carousels with customizable content cards that smoothly animate as users scroll through your pages. With modular architecture, separate meta box management, and dynamic layout options, it offers a powerful yet user-friendly solution for creating animated content presentations.

Features:
----------
- Architecture & Organization:
  * Modular code structure with separated components
  * Independent meta box management
  * Organized style and card controls
  * Clean, maintainable codebase

- Content Management:
  * Custom post type for carousel creation
  * Intuitive card-based interface
  * Individual card content management
  * Per-card styling customization
  * Media library integration
  * Drag-and-drop card ordering
  * Expand/collapse card interface
  * Layout selection (Horizontal/Vertical)


- Button Features:
  * Dedicated "Button Settings" section
  * FontAwesome Icon support (Class & Position)
  * Comprehensive Button Dimension controls (Padding, Radius, Width)
  * Button typography and color customization

- Style Controls:
  * Comprehensive color settings
  * Custom border radius controls (Global & Per-card)
  * Box shadow customization
  * Typography options (Google Fonts integration)
  * Real-time style preview
  * Per-card border customization (color, width, style)
  * Image position control for both layouts
  * Individual card styling options

- Animation System:
  * GSAP and ScrollTrigger integration
  * Layout-specific animations
  * Smooth scroll transitions
  * Performance optimized effects

- Development Features:
  * Separated class responsibilities
  * Enhanced security measures
  * Clean uninstall process
  * WordPress coding standards
  * Proper data sanitization

Installation:
--------------
1. Upload the `gsap-scroll` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Cinematic Scrolls' in your admin menu
4. Click 'Add New' to create your first carousel
5. Add cards with your content and customize the appearance
6. Use the provided shortcode to display the carousel in your posts or pages

Usage:
-------
1. Create a new carousel animation from the 'Cinematic Scrolls' menu
2. Select your layout (Horizontal Scroll or Vertical Stack) at the top of the editor
3. Use the card management interface:
   * Click 'Add New Item' to create new cards
   * Fill in card content (title, text)
   * Configure Button Settings (Text, URL, Icon, Dimensions, Colors)
   * Customize per-card Media & Assets (Image, Background Image)
   * Configure per-card Styling (Colors, Borders, Typography)
   * Use expand/collapse arrows to manage space
4. Configure global carousel styles (in the sidebar):
   * Set default colors for cards and buttons
   * Customize border radius for cards and images
   * Add box shadows
   * Configure padding
   * Preview changes in real-time
5. Copy the shortcode [gsap_scroll_animation id="X"] where X is your carousel ID
6. Paste the shortcode in your posts or pages

Card Management:
---------------
1. Expanding/Collapsing:
   * Use arrow toggles to show/hide card content
   * Makes managing multiple cards easier
   * Saves space in admin interface

2. Content Organization:
   * Drag-and-drop reordering
   * Individual card preview
   * Media library integration
   * Real-time updates

Changelog:
-----------
= 1.3.6 =
* Refined "Button Dimensions" layout
  - Cleaned up Radius control (removed redundant "All" input)
  - Aligned 4-corner inputs for Padding and Radius
* Fixed Admin UI consistency between PHP and JS

= 1.3.5 =
* Restored dedicated "Button Settings" section
  - Moved button fields (Text, URL, Icon, Colors, Dimensions) to their own section
  - Cleaned up "Content Details" and "Styling Options" to remove duplicates
* Updated CSS Grid layout for better organization

= 1.3.0 - 1.3.4 =
* Added FontAwesome Icon support for buttons
* Added Button Dimension controls (Padding, Radius, Width)
* Added Google Fonts integration with typography controls
* Enhanced Admin UI with better sectioning (Content, Media, Button, Style)
* Fixed various layout and saving bugs

= 1.0.1 =
* Added per-card border customization
  - Individual card border color, width, and style controls
  - Solid, dashed, dotted, and double border styles
* Enhanced per-card styling options
  - Title text color customization per card
  - Body text color customization per card
* Improved image position controls
  - Layout-specific image positioning
  - Horizontal layout: Top, After Title, Bottom
  - Vertical layout: Left, Right
* JavaScript improvements for dynamic card creation
  - All new cards include complete settings
  - Proper layout detection and conditional options
* Updated admin interface for better card management

= 1.0.0 =
* Initial release with GSAP and ScrollTrigger integration
* Modular architecture implementation:
  - Separated meta box functionality
  - Independent card management
  - Organized style controls
* Enhanced card interface features:
  - Expand/collapse functionality
  - Drag-and-drop reordering
  - Real-time content preview
* Advanced styling system:
  - Comprehensive color controls
  - Border radius customization
  - Box shadow settings
* Development improvements:
  - Clean code organization
  - Security enhancements
  - Proper data handling
* Media library integration with preview
* Responsive design implementation
* Shortcode system for easy placement

Version:
---------
1.3.6

Author:
--------
Ashu Tiwary

License:
---------
GPLv2 or later