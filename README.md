# Cinematic GSAP Scroll

**Description:**

The GSAP Scroll plugin is a custom WordPress plugin that integrates the GreenSock Animation Platform (GSAP) with scroll-based animations for your WordPress site. This plugin allows you to create engaging, animated carousels with customizable content cards that smoothly animate as users scroll through your pages. With modular architecture, separate meta box management, and dynamic layout options, it offers a powerful yet user-friendly solution for creating animated content presentations.

## Features

### Architecture & Organization
* **Modular code structure** with separated components
* **Independent meta box management**
* **Organized style and card controls**
* **Clean, maintainable codebase**

### Content Management
* **Custom post type** for carousel creation
* **Intuitive card-based interface**
* **Individual card content management**
* **Per-card styling customization**
* **Media library integration**
* **Drag-and-drop card ordering**
* **Expand/collapse card interface**
* **Layout selection** (Horizontal/Vertical)

### Button Features
* **Dedicated "Button Settings" section**
* **FontAwesome Icon support** (Class & Position)
* **Comprehensive Button Dimension controls** (Padding, Radius, Width)
* **Button typography and color customization**

### Style Controls
* **Comprehensive color settings**
* **Custom border radius controls** (Global & Per-card)
* **Box shadow customization**
* **Typography options** (Google Fonts integration)
* **Real-time style preview**
* **Per-card border customization** (color, width, style)
* **Image position control** for both layouts
* **Individual card styling options**

### Animation System
* **GSAP and ScrollTrigger integration**
* **Layout-specific animations**
* **Smooth scroll transitions**
* **Performance optimized effects**

### Development Features
* **Separated class responsibilities**
* **Enhanced security measures**
* **Clean uninstall process**
* **WordPress coding standards**
* **Proper data sanitization**

## Installation

1. Upload the `gsap-scroll` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to 'Cinematic Scrolls' in your admin menu.
4. Click 'Add New' to create your first carousel.
5. Add cards with your content and customize the appearance.
6. Use the provided shortcode to display the carousel in your posts or pages.

## Usage

1. **Create a new carousel animation** from the 'Cinematic Scrolls' menu.
2. **Select your layout** (Horizontal Scroll or Vertical Stack) at the top of the editor.
3. **Use the card management interface:**
   * Click 'Add New Item' to create new cards.
   * Fill in card content (title, text).
   * Configure **Button Settings** (Text, URL, Icon, Dimensions, Colors).
   * Customize per-card **Media & Assets** (Image, Background Image).
   * Configure per-card **Styling** (Colors, Borders, Typography).
   * Use expand/collapse arrows to manage space.
4. **Configure global carousel styles** (in the sidebar):
   * Set default colors for cards and buttons.
   * Customize border radius for cards and images.
   * Add box shadows.
   * Configure padding.
   * Preview changes in real-time.
5. **Copy the shortcode** `[gsap_scroll_animation id="X"]` where X is your carousel ID.
6. **Paste the shortcode** in your posts or pages.

## Card Management

### 1. Expanding/Collapsing
* Use arrow toggles to show/hide card content.
* Makes managing multiple cards easier.
* Saves space in admin interface.

### 2. Content Organization
* Drag-and-drop reordering.
* Individual card preview.
* Media library integration.
* Real-time updates.

## Changelog


### 1.3.7
* **Improved Admin UI**: Redesigned Button Settings layout for better usability.
* **Organized Grid Layout**: 2-column structure for cleaner setting management.
* **Enhanced Button Controls**: Grouped color, icon, and dimension settings logically.

### 1.0.0
* **Initial release**
* **Dedicated "Button Settings" section**
* **FontAwesome Icon support**
* **Comprehensive Button Dimension controls**
* **Google Fonts integration**
* **Advanced styling system**
* **Media library integration**
* **Responsive design implementation**

## Version
1.0.0

## Author
Ashu Tiwary

## License
GPLv2 or later
