<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Cinematic_Scroll_Style_Meta' ) ) {
    class Cinematic_Scroll_Style_Meta {
        public function __construct() {
            // Add our style meta box on the side
            add_action( 'add_meta_boxes',        [ $this, 'add_style_meta_box' ] );
            // Save all meta when the post is saved
            add_action( 'save_post',             [ $this, 'save_style_meta_box' ] );
            // Enqueue any admin CSS
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
            // Render the layout selector under the title field
            add_action( 'edit_form_after_title', [ $this, 'select_layout_option' ], 10, 1 );
        }

        /**
         * Output a layout dropdown (Carousel/Grid) under the Title field.
         * Runs once per screen load.
         */
        public function select_layout_option( WP_Post $post ) {
            // Only target our CPT
            if ( $post->post_type !== 'scroll_animation' ) {
                return;
            }

            // Guard to prevent duplicate rendering
            static $ran = false;
            if ( $ran ) {
                return;
            }
            $ran = true;

            // Security nonce
            wp_nonce_field( 'cgs_layout_nonce', 'cgs_layout_nonce_field' );

            // Retrieve saved layout or default to 'carousel'
            $layout = get_post_meta( $post->ID, '_cgs_layout', true ) ?: 'horizontal';
            ?>
            <div class="cgs-layout-option" style="display:flex;justify-content: space-between;align-items:center;margin:1em 0;padding:20px;border:1px solid #ddd;border-radius:10px;background:#f9f9f9;box-shadow: 0px 3px 15px 0px #d6e6ff;">
                <p style="width:50%;">
                <label for="cgs_layout" style="font-weight:bold;">
                    <?php esc_html_e( 'Select Layout:', 'cinematic-scroll' ); ?>
                </label>
                <select id="cgs_layout" name="cgs_layout" style="width:100%;max-width:200px;">
                    <option value="horizontal" <?php selected( $layout, 'horizontal' ); ?>><?php esc_html_e( 'Horizontal Scroll', 'cinematic-scroll' ); ?></option>
                    <option value="vertical"   <?php selected( $layout, 'vertical' );   ?>><?php esc_html_e( 'Vertical Stack',     'cinematic-scroll' ); ?></option>
                </select>
                <?php 
                    // Use WordPress native submit button to match default appearance
                submit_button(
                    esc_html__( 'Apply', 'cinematic-scroll' ), // Button text
                    'primary',                            // Button type/style
                    'cgs_layout_apply',           // Button name attribute
                    false,                                // Don't wrap in <p>
                    array(
                        'id'    => 'cgs_layout_apply',
                        'style' => 'margin:0px 10px;'
                    )
                );
                ?>
                </p>
                <p style="font-weight:bold;">Shortcode:
                    <?php echo '<code>[cinematic_scroll id="' . esc_attr($post->ID) . '"]</code>'; ?>
                </p>
            </div>
            <?php
        }

        public function add_style_meta_box() {
            add_meta_box(
                'cgs_style',
                __( 'Carousel Style Settings', 'cinematic-scroll' ),
                [ $this, 'render_style_meta_box' ],
                'scroll_animation',
                'normal',
                'default'
            );
        }

        /**
         * Get list of available fonts (System + Theme + Google).
         */
        public static function get_all_fonts() {
            // 1. Standard Web Safe Fonts
            $fonts = [
                'inherit' => 'Default (Inherit from Theme)',
                'Arial, Helvetica, sans-serif' => 'Arial',
                'Verdana, Geneva, sans-serif' => 'Verdana',
                'Tahoma, Geneva, sans-serif' => 'Tahoma',
                'Trebuchet MS, Helvetica, sans-serif' => 'Trebuchet MS',
                'Times New Roman, Times, serif' => 'Times New Roman',
                'Georgia, serif' => 'Georgia',
                'Courier New, Courier, monospace' => 'Courier New',
                'Impact, Charcoal, sans-serif' => 'Impact',
                '"Comic Sans MS", cursive, sans-serif' => 'Comic Sans MS',
            ];

            // 2. Try to get Block Theme Fonts (WordPress 5.8+)
            if ( class_exists( 'WP_Theme_JSON_Resolver' ) ) {
                $theme_data = WP_Theme_JSON_Resolver::get_merged_data();
                $settings   = $theme_data->get_settings();
                
                if ( isset( $settings['typography']['fontFamilies'] ) && is_array( $settings['typography']['fontFamilies'] ) ) {
                    foreach ( $settings['typography']['fontFamilies'] as $font ) {
                        if ( isset( $font['fontFamily'] ) && isset( $font['name'] ) ) {
                            $fonts[ $font['fontFamily'] ] = $font['name'] . ' (Theme)';
                        }
                    }
                }
            }
            
            // 3. Google Fonts (Top Popular)
            $google_fonts = self::get_google_fonts_list();
            foreach($google_fonts as $gfont) {
                // Key is family name, Value is Label
                // We assume Google Fonts are loaded via their name. 
                // We add ', sans-serif' or ', serif' generically? 
                // For simplicity, we just use the name as the family stack for now, or append fallback.
                // Most Google Fonts fall into sans-serif or serif. We'll leave it as just the family name string for the CSS value 
                // which works if the font is loaded.
                
                // Note: To make this "not hardcoded", ideally this comes from an API. 
                // Since we can't easily fetch, we provide a robust list.
                 $fonts[ "'$gfont', sans-serif" ] = $gfont; // Defaulting to sans-serif fallback for safety
            }

            return $fonts;
        }

        /**
         * Returns a comprehensive list of Google Fonts.
         */
        private static function get_google_fonts_list() {
            return [
                'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Oswald', 'Source Sans Pro', 'Slabo 27px', 'Raleway', 'PT Sans', 'Merriweather',
                'Noto Sans', 'Nunito Sans', 'Concert One', 'Prompt', 'Work Sans', 'Poppins', 'Playfair Display', 'Rubik', 'Lora', 'Fira Sans',
                'Mulish', 'Titillium Web', 'Ubuntu', 'Condensed', 'Karla', 'Arimo', 'Mukta', 'Nunito', 'Josefin Sans', 'Inconsolata',
                'Libre Baskerville', 'Quicksand', 'Dosis', 'Anton', 'Cabin', 'Oxygen', 'Crimson Text', 'Hind', 'Bitter', 'Domine',
                'Barlow', 'Bebas Neue', 'Varela Round', 'Exo 2', 'Pacifico', 'Dancing Script', 'Shadows Into Light', 'Indie Flower',
                'Abel', 'Bree Serif', 'Fjalla One', 'Lobster', 'Satisfy', 'Righteous', 'Fredoka One', 'Comfortaa', 'Cinzel',
                'Play', 'Rokkitt', 'Vollkorn', 'Cuprum', 'Maven Pro', 'Signika', 'Hammersmith One', 'Voltaire', 'Didact Gothic',
                'Muli', 'Space Mono', 'Cardo', 'Ruda', 'Poly', 'Jura', 'Coda', 'Syncopate', 'Audiowide', 'Michroma', 'Orbitron',
                'Khand', 'Teko', 'Rajdhani', 'Eczar', 'Yantramanav', 'Hind Siliguri', 'Hind Vadodara', 'Hind Madurai', 
                'Noto Serif', 'Zilla Slab', 'IBM Plex Sans', 'IBM Plex Serif', 'Inter', 'Manrope', 'DM Sans', 'Syne', 'Outfit'
            ];
        }

        public function render_style_meta_box( $post ) {
            // Nonce for saving
            wp_nonce_field( 'cgs_style_nonce', 'cgs_style_nonce_field' );

            $layout = get_post_meta( $post->ID, '_cgs_layout', true ) ?: 'horizontal';
            
            // Load saved style values
            $card_bg      = get_post_meta( $post->ID, '_cgs_card_bg',     true );
            $title_txt_colr = get_post_meta( $post->ID, '_cgs_title_colr', true );
            $card_txt     = get_post_meta( $post->ID, '_cgs_txt_colr',    true );
            $button_bg    = get_post_meta( $post->ID, '_cgs_button_bg',   true );
            $button_text  = get_post_meta( $post->ID, '_cgs_button_text', true );
            $content_width = get_post_meta( $post->ID, '_cgs_content_width', true ) ?: '1200px';
            // Border and radius values
            $card_border_clr = get_post_meta( $post->ID, '_cgs_card_border_clr', true );
            $card_border_width = get_post_meta( $post->ID, '_cgs_card_border_width', true ) ?: '0';
            $card_border_style = get_post_meta( $post->ID, '_cgs_card_border_style', true ) ?: 'none';
            // Radius values
            $r_tl         = get_post_meta( $post->ID, '_cgs_card_radius_top_lt', true );
            $r_tr         = get_post_meta( $post->ID, '_cgs_card_radius_top_rt', true );
            $r_br         = get_post_meta( $post->ID, '_cgs_card_radius_btm_rt', true );
            $r_bl         = get_post_meta( $post->ID, '_cgs_card_radius_btm_lt', true );
            // Image radius and padding
            $img_tl       = get_post_meta( $post->ID, '_cgs_img_radius_top_lt', true );
            $img_tr       = get_post_meta( $post->ID, '_cgs_img_radius_top_rt', true );
            $img_br       = get_post_meta( $post->ID, '_cgs_img_radius_btm_rt', true );
            $img_bl       = get_post_meta( $post->ID, '_cgs_img_radius_btm_lt', true );
            // Padding values
            $padding_top  = get_post_meta( $post->ID, '_cgs_padding_top', true ) ?: '0';
            $padding_right = get_post_meta( $post->ID, '_cgs_padding_right', true ) ?: '0';
            $padding_bottom = get_post_meta( $post->ID, '_cgs_padding_bottom', true ) ?: '0';
            $padding_left = get_post_meta( $post->ID, '_cgs_padding_left', true ) ?: '0';
            // Card shadow and image position
            $shadow       = get_post_meta( $post->ID, '_cgs_card_shadow', true );
            $img_position = get_post_meta( $post->ID, '_cgs_img_position', true ) ?: 'bottom';
            $ver_img_position = get_post_meta( $post->ID, '_cgs_vert_img_position', true ) ?: 'right';
            ?>
            <!-- Card Color and button color and Text color -->
            <p class="cgs-meta-style-desc">
                <label for="cgs_card_bg"><?php esc_html_e( 'Global Card Inside Color:', 'cinematic-scroll' ); ?></label><br>
                <input type="text" class="cgs-color-picker" id="cgs_card_bg" name="cgs_card_bg" value="<?php echo esc_attr( $card_bg ); ?>" data-alpha="true"><br><br>
                
                <label for="cgs_wrapper_bg_color"><?php esc_html_e( 'Global Card Outside Color:', 'cinematic-scroll' ); ?></label><br>
                <input type="text" class="cgs-color-picker" id="cgs_wrapper_bg_color" name="cgs_wrapper_bg_color" value="<?php echo esc_attr( get_post_meta( $post->ID, '_cgs_wrapper_bg_color', true ) ); ?>" data-alpha="true"><br><br>

                <label for="cgs_title_colr"><?php esc_html_e( 'Title Text Color:', 'cinematic-scroll' ); ?></label><br>
                <input type="text" class="cgs-color-picker" id="cgs_title_colr" name="cgs_title_colr" value="<?php echo esc_attr( $title_txt_colr ); ?>" data-alpha="true"><br><br>
                <label for="cgs_txt_colr"><?php esc_html_e( 'Body Text Color:', 'cinematic-scroll' ); ?></label><br>
                <input type="text" class="cgs-color-picker" id="cgs_txt_colr" name="cgs_txt_colr" value="<?php echo esc_attr( $card_txt ); ?>" data-alpha="true"><br><br>
                <label for="cgs_button_bg"><?php esc_html_e( 'Button Background Color:', 'cinematic-scroll' ); ?></label><br>
                <input type="text" class="cgs-color-picker" id="cgs_button_bg" name="cgs_button_bg" value="<?php echo esc_attr( $button_bg ); ?>" data-alpha="true"><br><br>
                <label for="cgs_button_text"><?php esc_html_e( 'Button Text Color:', 'cinematic-scroll' ); ?></label><br>
                <input type="text" class="cgs-color-picker" id="cgs_button_text" name="cgs_button_text" value="<?php echo esc_attr( $button_text ); ?>" data-alpha="true"><br><br>
            </p>

            <!-- Global Width Configuration (New Row) -->
            <!-- Global Width Configuration (New Row) -->
            <p class="cgs-meta-style-desc" style="border-top:1px dashed #ddd; padding-top:10px; margin-top:10px; display:flex; align-items:center; gap:15px; width: 100%;">
                <strong style="flex: 0 0 auto;"><?php esc_html_e( 'Global Width Configuration:', 'cinematic-scroll' ); ?></strong>
                
                <span style="flex: 1; display:flex; align-items:center; gap:5px;">
                    <label for="cgs_content_width" style="margin-bottom:0; font-size: 0.9em; white-space:nowrap;"><?php esc_html_e( 'Inside Card Width (Default: 1200px):', 'cinematic-scroll' ); ?></label>
                    <input type="text" id="cgs_content_width" name="cgs_content_width" value="<?php echo esc_attr( $content_width ); ?>" placeholder="1200px" style="flex:1;">
                </span>

                <span style="flex: 1; display:flex; align-items:center; gap:5px;">
                    <label for="cgs_btn_width" style="margin-bottom:0; font-size: 0.9em; white-space:nowrap;"><?php esc_html_e( 'Button Width:', 'cinematic-scroll' ); ?></label>
                    <input type="text" id="cgs_btn_width" name="cgs_btn_width" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_width', true) ); ?>" placeholder="e.g. 200px" style="flex:1;">
                </span>
            </p>

            <!-- Button Dimension Settings (New Row) -->
            <p class="cgs-meta-style-desc" style="border-top:1px dashed #ddd; padding-top:10px; margin-top:10px;">
                <strong><?php esc_html_e( 'Button Dimensions:', 'cinematic-scroll' ); ?></strong><br>
                
                <span style="display:inline-block; margin-right:20px; margin-top:5px;">
                    <strong><?php esc_html_e( 'Padding (px):', 'cinematic-scroll' ); ?></strong><br>
                    <?php $bp_t = get_post_meta($post->ID, '_cgs_btn_padding_top', true); ?>
                    <?php $bp_r = get_post_meta($post->ID, '_cgs_btn_padding_right', true); ?>
                    <?php $bp_b = get_post_meta($post->ID, '_cgs_btn_padding_bottom', true); ?>
                    <?php $bp_l = get_post_meta($post->ID, '_cgs_btn_padding_left', true); ?>
                    <label style="font-size:0.9em;"><?php esc_html_e( 'Top', 'cinematic-scroll' ); ?> <input type="number" name="cgs_btn_padding_top" value="<?php echo esc_attr( $bp_t ); ?>" style="width:50px;"></label>
                    <label style="font-size:0.9em;"><?php esc_html_e( 'Right', 'cinematic-scroll' ); ?> <input type="number" name="cgs_btn_padding_right" value="<?php echo esc_attr( $bp_r ); ?>" style="width:50px;"></label>
                    <label style="font-size:0.9em;"><?php esc_html_e( 'Bottom', 'cinematic-scroll' ); ?> <input type="number" name="cgs_btn_padding_bottom" value="<?php echo esc_attr( $bp_b ); ?>" style="width:50px;"></label>
                    <label style="font-size:0.9em;"><?php esc_html_e( 'Left', 'cinematic-scroll' ); ?> <input type="number" name="cgs_btn_padding_left" value="<?php echo esc_attr( $bp_l ); ?>" style="width:50px;"></label>
                </span>

                <span style="display:inline-block; margin-right:20px; margin-top:5px;">
                    <strong><?php esc_html_e( 'Radius (px):', 'cinematic-scroll' ); ?></strong><br>
                    <?php $br_tl = get_post_meta($post->ID, '_cgs_btn_radius_top_lt', true); ?>
                    <?php $br_tr = get_post_meta($post->ID, '_cgs_btn_radius_top_rt', true); ?>
                    <?php $br_br = get_post_meta($post->ID, '_cgs_btn_radius_btm_rt', true); ?>
                    <?php $br_bl = get_post_meta($post->ID, '_cgs_btn_radius_btm_lt', true); ?>
                    <label style="font-size:0.9em;"><?php esc_html_e( 'TL', 'cinematic-scroll' ); ?> <input type="number" name="cgs_btn_radius_top_lt" value="<?php echo esc_attr( $br_tl ); ?>" style="width:50px;"></label>
                    <label style="font-size:0.9em;"><?php esc_html_e( 'TR', 'cinematic-scroll' ); ?> <input type="number" name="cgs_btn_radius_top_rt" value="<?php echo esc_attr( $br_tr ); ?>" style="width:50px;"></label>
                    <label style="font-size:0.9em;"><?php esc_html_e( 'BR', 'cinematic-scroll' ); ?> <input type="number" name="cgs_btn_radius_btm_rt" value="<?php echo esc_attr( $br_br ); ?>" style="width:50px;"></label>
                    <label style="font-size:0.9em;"><?php esc_html_e( 'BL', 'cinematic-scroll' ); ?> <input type="number" name="cgs_btn_radius_btm_lt" value="<?php echo esc_attr( $br_bl ); ?>" style="width:50px;"></label>
                </span>

            </p>

            </p>

            <!-- Button Border (Normal) -->
            <p class="cgs-meta-style-desc">
                <strong><?php esc_html_e( 'Button Border (Normal):', 'cinematic-scroll' ); ?></strong>
                <label><?php esc_html_e('Width', 'cinematic-scroll'); ?> <input type="number" name="cgs_btn_border_width" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_border_width', true) ); ?>" style="width:60px;"></label>
                <label><?php esc_html_e('Style', 'cinematic-scroll'); ?>
                <select name="cgs_btn_border_style" style="width:100px;">
                    <option value="none" <?php selected( get_post_meta($post->ID, '_cgs_btn_border_style', true), 'none' ); ?>>None</option>
                    <option value="solid" <?php selected( get_post_meta($post->ID, '_cgs_btn_border_style', true), 'solid' ); ?>>Solid</option>
                    <option value="dashed" <?php selected( get_post_meta($post->ID, '_cgs_btn_border_style', true), 'dashed' ); ?>>Dashed</option>
                    <option value="dotted" <?php selected( get_post_meta($post->ID, '_cgs_btn_border_style', true), 'dotted' ); ?>>Dotted</option>
                </select></label>
                <label><?php esc_html_e('Color', 'cinematic-scroll'); ?> <input type="text" class="cgs-color-picker" name="cgs_btn_border_color" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_border_color', true) ); ?>" data-alpha="true"></label>
            </p>

            <!-- Button Effects (Normal) -->
            <p class="cgs-meta-style-desc">
                <strong><?php esc_html_e( 'Button Effects (Normal):', 'cinematic-scroll' ); ?></strong>
                <label><?php esc_html_e('Shadow', 'cinematic-scroll'); ?> <input type="text" name="cgs_btn_shadow" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_shadow', true) ); ?>" placeholder="e.g. 0 5px 15px rgba(0,0,0,0.1)" style="width:250px;"></label>
                <label><?php esc_html_e('Scale', 'cinematic-scroll'); ?> <input type="number" step="0.01" name="cgs_btn_scale" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_scale', true) ); ?>" placeholder="1" style="width:60px;"></label>
                <label><?php esc_html_e('Lift (px)', 'cinematic-scroll'); ?> <input type="number" name="cgs_btn_lift" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_lift', true) ); ?>" placeholder="0" style="width:60px;"></label>
            </p>

            <!-- Button Hover Settings -->
            <!-- Button Hover Settings -->
            <div style="margin-top:20px; padding-top:10px; border-top:1px dashed #ccc;">
                <strong style="display:block; margin-bottom:10px;"><?php esc_html_e( 'Button Hover State:', 'cinematic-scroll' ); ?></strong>
                
                <div style="display:flex; flex-wrap:wrap; gap:20px; align-items:center;">
                     <!-- Hover Background -->
                     <div style="display:flex; align-items:center; gap:8px;">
                        <label style="margin:0;"><?php esc_html_e('Background', 'cinematic-scroll'); ?></label>
                        <input type="text" class="cgs-color-picker" name="cgs_btn_hover_bg" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_hover_bg', true) ); ?>" data-alpha="true">
                     </div>
                     <!-- Hover Text -->
                     <div style="display:flex; align-items:center; gap:8px;">
                        <label style="margin:0;"><?php esc_html_e('Text Color', 'cinematic-scroll'); ?></label>
                        <input type="text" class="cgs-color-picker" name="cgs_btn_hover_text" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_hover_text', true) ); ?>" data-alpha="true">
                     </div>
                     <!-- Hover Border -->
                     <div style="display:flex; align-items:center; gap:8px;">
                        <label style="margin:0;"><?php esc_html_e('Border Color', 'cinematic-scroll'); ?></label>
                        <input type="text" class="cgs-color-picker" name="cgs_btn_hover_border_color" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_hover_border_color', true) ); ?>" data-alpha="true">
                     </div>
                    <!-- Hover Effects -->
                    <label style="display:flex; align-items:center; gap:5px; margin:0;">
                        <?php esc_html_e('Hover Shadow', 'cinematic-scroll'); ?> 
                        <input type="text" name="cgs_btn_hover_shadow" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_hover_shadow', true) ); ?>" placeholder="e.g. 0 8px 25px rgba(0,0,0,0.2)" style="width:230px;">
                    </label>
                    <label style="display:flex; align-items:center; gap:5px; margin:0;">
                        <?php esc_html_e('Hover Scale', 'cinematic-scroll'); ?> 
                        <input type="number" step="0.01" name="cgs_btn_hover_scale" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_hover_scale', true) ); ?>" placeholder="1.05" style="width:70px;">
                    </label>
                    <label style="display:flex; align-items:center; gap:5px; margin:0;">
                        <?php esc_html_e('Hover Lift', 'cinematic-scroll'); ?> 
                        <input type="number" name="cgs_btn_hover_lift" value="<?php echo esc_attr( get_post_meta($post->ID, '_cgs_btn_hover_lift', true) ); ?>" placeholder="5" style="width:60px;">
                    </label>
                </div>
            </div>
            <!-- Card Border and Radius Settings -->
            <p class="cgs-meta-style-desc">
                <strong><?php esc_html_e( 'Card Border:', 'cinematic-scroll' ); ?></strong>
                <label for="cgs_card_border_clr"><?php esc_html_e( 'Color:', 'cinematic-scroll' ); ?></label>
                <input type="text" class="cgs-color-picker" id="cgs_card_border_clr" name="cgs_card_border_clr" value="<?php echo esc_attr( $card_border_clr ); ?>" data-alpha="true">
                <label for="cgs_card_border_width"><?php esc_html_e( 'Width (px):', 'cinematic-scroll' ); ?></label>
                <input type="number" id="cgs_card_border_width" name="cgs_card_border_width" value="<?php echo esc_attr( get_post_meta( $post->ID, '_cgs_card_border_width', true ) ?: '0' ); ?>" min="0" style="width:60px;">
                <strong><?php esc_html_e( 'Card Border Style:', 'cinematic-scroll' ); ?></strong>
                <select id="cgs_card_border_style" name="cgs_card_border_style">
                    <option value="none" <?php selected( get_post_meta( $post->ID, '_cgs_card_border_style', true ) ?: 'none', 'none' ); ?>><?php esc_html_e( 'None', 'cinematic-scroll' ); ?></option>
                    <option value="solid" <?php selected( get_post_meta( $post->ID, '_cgs_card_border_style', true ), 'solid' ); ?>><?php esc_html_e( 'Solid', 'cinematic-scroll' ); ?></option>
                    <option value="dashed" <?php selected( get_post_meta( $post->ID, '_cgs_card_border_style', true ), 'dashed' ); ?>><?php esc_html_e( 'Dashed', 'cinematic-scroll' ); ?></option>
                    <option value="dotted" <?php selected( get_post_meta( $post->ID, '_cgs_card_border_style', true ), 'dotted' ); ?>><?php esc_html_e( 'Dotted', 'cinematic-scroll' ); ?></option>
                    <option value="double" <?php selected( get_post_meta( $post->ID, '_cgs_card_border_style', true ), 'double' ); ?>><?php esc_html_e( 'Double', 'cinematic-scroll' ); ?></option>
                </select>
            </p>
            <!-- Card Radius and Padding Settings -->
            <p class="cgs-meta-style-desc">
                <strong><?php esc_html_e( 'Card Border Radius (px):', 'cinematic-scroll' ); ?></strong>
                <?php esc_html_e( 'Top-Left',   'cinematic-scroll' ); ?> <input type="number" name="cgs_card_radius_top_lt" value="<?php echo esc_attr( $r_tl ); ?>" min="0" style="width:60px;">
                <?php esc_html_e( 'Top-Right',  'cinematic-scroll' ); ?> <input type="number" name="cgs_card_radius_top_rt" value="<?php echo esc_attr( $r_tr ); ?>" min="0" style="width:60px;">
                <?php esc_html_e( 'Bottom-Right','cinematic-scroll'); ?> <input type="number" name="cgs_card_radius_btm_rt" value="<?php echo esc_attr( $r_br ); ?>" min="0" style="width:60px;">
                <?php esc_html_e( 'Bottom-Left','cinematic-scroll'); ?> <input type="number" name="cgs_card_radius_btm_lt" value="<?php echo esc_attr( $r_bl ); ?>" min="0" style="width:60px;">
            </p>
            <!-- Image Border Radius -->
            <p class="cgs-meta-style-desc">
                <strong><?php esc_html_e( 'Image Border Radius (px):', 'cinematic-scroll' ); ?></strong>
                <?php esc_html_e( 'Top-Left',   'cinematic-scroll' ); ?> <input type="number" name="cgs_img_radius_top_lt" value="<?php echo esc_attr( $img_tl ); ?>" min="0" style="width:60px;">
                <?php esc_html_e( 'Top-Right',  'cinematic-scroll' ); ?> <input type="number" name="cgs_img_radius_top_rt" value="<?php echo esc_attr( $img_tr ); ?>" min="0" style="width:60px;">
                <?php esc_html_e( 'Bottom-Right','cinematic-scroll'); ?> <input type="number" name="cgs_img_radius_btm_rt" value="<?php echo esc_attr( $img_br ); ?>" min="0" style="width:60px;">
                <?php esc_html_e( 'Bottom-Left','cinematic-scroll'); ?> <input type="number" name="cgs_img_radius_btm_lt" value="<?php echo esc_attr( $img_bl ); ?>" min="0" style="width:60px;">
            </p>
            <!-- Padding Settings -->   
            <p class="cgs-meta-style-desc">
                <strong><?php esc_html_e( 'Card Padding (px):', 'cinematic-scroll' ); ?></strong>
                <?php esc_html_e( 'Top',   'cinematic-scroll' ); ?> <input type="number" name="cgs_padding_top" value="<?php echo esc_attr( $padding_top ); ?>" min="0" style="width:60px;">
                <?php esc_html_e( 'Right',  'cinematic-scroll' ); ?> <input type="number" name="cgs_padding_right" value="<?php echo esc_attr( $padding_right ); ?>" min="0" style="width:60px;">
                <?php esc_html_e( 'Bottom','cinematic-scroll'); ?> <input type="number" name="cgs_padding_bottom" value="<?php echo esc_attr( $padding_bottom ); ?>" min="0" style="width:60px;">
                <?php esc_html_e( 'Left','cinematic-scroll'); ?> <input type="number" name="cgs_padding_left" value="<?php echo esc_attr( $padding_left ); ?>" min="0" style="width:60px;">
            </p>
            <!-- Card Shadow and Image Position -->
            <p class="cgs-meta-style-desc">
                <label for="cgs_card_shadow"><?php esc_html_e( 'Card Box Shadow (CSS):', 'cinematic-scroll' ); ?></label><br>
                <input type="text" id="cgs_card_shadow" name="cgs_card_shadow"
                    value="<?php echo esc_attr( $shadow ); ?>" placeholder="e.g. 0 4px 10px rgba(0,0,0,0.2)" style="width:50%;">
                <br>
                <?php if ( 'horizontal' === $layout ) { ?>
                <label for="cgs_img_position"><?php esc_html_e( 'Image Position:', 'cinematic-scroll' ); ?></label><br>
                <select id="cgs_img_position" name="cgs_img_position">
                    <option value="left"    <?php selected( $img_position, 'left' ); ?>><?php esc_html_e( 'Left',    'cinematic-scroll' ); ?></option>
                    <option value="right" <?php selected( $img_position,'right'); ?>><?php esc_html_e( 'Right','cinematic-scroll'); ?></option>
                </select>
                <?php } else if('vertical' === $layout){?>
                <label for="cgs_vert_img_position"><?php esc_html_e( 'Image Position:', 'cinematic-scroll' ); ?></label><br>
                <select id="cgs_vert_img_position" name="cgs_vert_img_position">
                    <option value="left"    <?php selected( $ver_img_position, 'left' ); ?>><?php esc_html_e( 'Left',    'cinematic-scroll' ); ?></option>
                    <option value="right" <?php selected( $ver_img_position,'right'); ?>><?php esc_html_e( 'Right','cinematic-scroll'); ?></option>
                </select>
                <?php } ?>
            </p>

            <!-- Typography Settings -->
            <?php
            $typo_sections = [
                'title'  => __('Title Typography', 'cinematic-scroll'),
                'body'   => __('Body Typography', 'cinematic-scroll'),
                'button' => __('Button Typography', 'cinematic-scroll'),
            ];

            // Get available fonts
            $available_fonts = self::get_all_fonts();

            foreach ($typo_sections as $slug => $label) {
                // Get saved values
                $ff = get_post_meta($post->ID, "_cgs_{$slug}_font_family", true);
                $tag = get_post_meta($post->ID, "_cgs_{$slug}_tag", true); // New Attribute
                $fs = get_post_meta($post->ID, "_cgs_{$slug}_font_size", true);
                $fw = get_post_meta($post->ID, "_cgs_{$slug}_font_weight", true);
                $fst = get_post_meta($post->ID, "_cgs_{$slug}_font_style", true);
                $lh = get_post_meta($post->ID, "_cgs_{$slug}_line_height", true);
                $tt = get_post_meta($post->ID, "_cgs_{$slug}_text_transform", true);
                $ls = get_post_meta($post->ID, "_cgs_{$slug}_letter_spacing", true);
                
                // Define available tags
                $tags = ['h1'=>'H1','h2'=>'H2','h3'=>'H3','h4'=>'H4','h5'=>'H5','h6'=>'H6','p'=>'P','div'=>'Result Div','span'=>'Span'];
                // Defaults: Title -> h3, Body -> p, Button -> span (though button usually stays 'a' or 'button', but user asked for attributes. 
                // Actually user specifically said "Title,body typography attribute". I will exclude Button from the Tag selector to avoid breaking semantics of the link unless requested, 
                // OR I can just allow it but it might break the <a> structure if I replace <a> with <h1>?
                // The current code wraps the button text in `<a>`. Changing that tag might break the link functionality. 
                // I will add it for Title and Body ONLY as requested.
                
                $show_tag_option = ($slug !== 'button');
                ?>
                <div style="width: 100%; margin-top: 15px; padding-top: 10px; border-top: 1px dashed #eee;">
                    <strong style="display:block; margin-bottom: 10px;"><?php echo esc_html($label); ?></strong>
                    
                    <div class="cgs-form-group" style="margin-bottom: 10px; display:flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label style="font-size:0.85em; display:block; margin-bottom:2px;"><?php esc_html_e('Font Family', 'cinematic-scroll'); ?></label>
                            <select name="cgs_<?php echo $slug; ?>_font_family" style="width:100%; max-width: 100%;">
                                 <?php foreach($available_fonts as $val => $name): ?>
                                    <option value="<?php echo esc_attr($val); ?>" <?php selected($ff, $val); ?>><?php echo esc_html($name); ?></option>
                                 <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if($show_tag_option): ?>
                        <div style="flex: 1;">
                            <label style="font-size:0.85em; display:block; margin-bottom:2px;"><?php esc_html_e('HTML Tag', 'cinematic-scroll'); ?></label>
                            <select name="cgs_<?php echo $slug; ?>_tag" style="width:100%;">
                                <option value=""><?php esc_html_e('Default', 'cinematic-scroll'); ?></option>
                                <?php foreach($tags as $t_val => $t_label): ?>
                                    <option value="<?php echo esc_attr($t_val); ?>" <?php selected($tag, $t_val); ?>><?php echo esc_html($t_label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display:flex; flex-wrap:wrap; gap:10px;">
                        <div style="flex:1; min-width: 60px;">
                            <label style="font-size:0.85em; display:block;"><?php esc_html_e('Size', 'cinematic-scroll'); ?></label>
                            <input type="text" name="cgs_<?php echo $slug; ?>_font_size" value="<?php echo esc_attr($fs); ?>" placeholder="16px" style="width:100%;">
                        </div>
                        <div style="flex:1; min-width: 80px;">
                            <label style="font-size:0.85em; display:block;"><?php esc_html_e('Weight', 'cinematic-scroll'); ?></label>
                            <select name="cgs_<?php echo $slug; ?>_font_weight" style="width:100%;">
                                <option value="" <?php selected($fw, ''); ?>><?php esc_html_e('Default', 'cinematic-scroll'); ?></option>
                                <option value="normal" <?php selected($fw, 'normal'); ?>>Normal</option>
                                <option value="bold" <?php selected($fw, 'bold'); ?>>Bold</option>
                                <?php foreach([100,200,300,400,500,600,700,800,900] as $w): ?>
                                    <option value="<?php echo $w; ?>" <?php selected($fw, $w); ?>><?php echo $w; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="flex:1; min-width: 80px;">
                            <label style="font-size:0.85em; display:block;"><?php esc_html_e('Style', 'cinematic-scroll'); ?></label>
                            <select name="cgs_<?php echo $slug; ?>_font_style" style="width:100%;">
                                <option value="" <?php selected($fst, ''); ?>><?php esc_html_e('Default', 'cinematic-scroll'); ?></option>
                                <option value="normal" <?php selected($fst, 'normal'); ?>>Normal</option>
                                <option value="italic" <?php selected($fst, 'italic'); ?>>Italic</option>
                            </select>
                        </div>
                        <div style="flex:1; min-width: 60px;">
                            <label style="font-size:0.85em; display:block;"><?php esc_html_e('Line Ht', 'cinematic-scroll'); ?></label>
                            <input type="text" name="cgs_<?php echo $slug; ?>_line_height" value="<?php echo esc_attr($lh); ?>" placeholder="1.5" style="width:100%;">
                        </div>
                        <div style="flex:1; min-width: 90px;">
                            <label style="font-size:0.85em; display:block;"><?php esc_html_e('Transform', 'cinematic-scroll'); ?></label>
                            <select name="cgs_<?php echo $slug; ?>_text_transform" style="width:100%;">
                                <option value="" <?php selected($tt, ''); ?>><?php esc_html_e('Default', 'cinematic-scroll'); ?></option>
                                <option value="none" <?php selected($tt, 'none'); ?>>None</option>
                                <option value="uppercase" <?php selected($tt, 'uppercase'); ?>>Upper</option>
                                <option value="lowercase" <?php selected($tt, 'lowercase'); ?>>Lower</option>
                                <option value="capitalize" <?php selected($tt, 'capitalize'); ?>>Cap</option>
                            </select>
                        </div>
                        <div style="flex:1; min-width: 60px;">
                            <label style="font-size:0.85em; display:block;"><?php esc_html_e('Spacing', 'cinematic-scroll'); ?></label>
                            <input type="text" name="cgs_<?php echo $slug; ?>_letter_spacing" value="<?php echo esc_attr($ls); ?>" placeholder="0px" style="width:100%;">
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php
        }

        public function save_style_meta_box( $post_id ) {
            // Bail early on autosave
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            if ( isset( $_POST['cgs_layout_nonce_field'] ) && wp_verify_nonce( $_POST['cgs_layout_nonce_field'], 'cgs_layout_nonce' ) && isset( $_POST['cgs_layout'] ) ) {
                update_post_meta( $post_id, '_cgs_layout', sanitize_key( $_POST['cgs_layout'] ) );
            }


            // Verify our nonces
            if ( isset( $_POST['cgs_style_nonce_field'] ) &&
                 wp_verify_nonce( $_POST['cgs_style_nonce_field'], 'cgs_style_nonce' ) ) {

                // Save style settings
                if ( isset( $_POST['cgs_card_bg'] ) )     {
                    update_post_meta( $post_id, '_cgs_card_bg', sanitize_text_field( $_POST['cgs_card_bg'] ) );
                }
                if ( isset( $_POST['cgs_wrapper_bg_color'] ) ) {
                    update_post_meta( $post_id, '_cgs_wrapper_bg_color', sanitize_text_field( $_POST['cgs_wrapper_bg_color'] ) );
                }
                if ( isset( $_POST['cgs_txt_colr'] ) )    {
                    update_post_meta( $post_id, '_cgs_txt_colr', sanitize_text_field( $_POST['cgs_txt_colr'] ) );
                }
                if ( isset( $_POST['cgs_title_colr'] ) ) {
                    update_post_meta( $post_id, '_cgs_title_colr', sanitize_text_field( $_POST['cgs_title_colr'] ) );
                }
                if ( isset( $_POST['cgs_button_bg'] ) )   {
                    update_post_meta( $post_id, '_cgs_button_bg', sanitize_text_field( $_POST['cgs_button_bg'] ) );
                }
                if ( isset( $_POST['cgs_button_text'] ) ) {
                    update_post_meta( $post_id, '_cgs_button_text', sanitize_text_field( $_POST['cgs_button_text'] ) );
                }
                if ( isset( $_POST['cgs_content_width'] ) ) {
                    update_post_meta( $post_id, '_cgs_content_width', sanitize_text_field( $_POST['cgs_content_width'] ) );
                }
                
                // Save Button Padding
                foreach ( [ 'top', 'right', 'bottom', 'left' ] as $f ) {
                    $key = "cgs_btn_padding_{$f}";
                    if ( isset( $_POST[ $key ] ) ) {
                        $val = $_POST[ $key ];
                        if ( $val === '' ) {
                            update_post_meta( $post_id, "_cgs_btn_padding_{$f}", '' );
                        } else {
                            update_post_meta( $post_id, "_cgs_btn_padding_{$f}", intval( $val ) );
                        }
                    }
                }
                // Save Button Radius
                foreach ( [ 'top_lt','top_rt','btm_rt','btm_lt' ] as $f ) {
                    $key = "cgs_btn_radius_{$f}";
                    if ( isset( $_POST[ $key ] ) ) {
                        $val = $_POST[ $key ];
                        if ( $val === '' ) {
                            update_post_meta( $post_id, "_cgs_btn_radius_{$f}", '' );
                        } else {
                            update_post_meta( $post_id, "_cgs_btn_radius_{$f}", intval( $val ) );
                        }
                    }
                }
                // Save Button Width
                if ( isset( $_POST['cgs_btn_width'] ) ) {
                    update_post_meta( $post_id, '_cgs_btn_width', sanitize_text_field( $_POST['cgs_btn_width'] ) );
                }

                // Button Border
                if ( isset( $_POST['cgs_btn_border_width'] ) ) update_post_meta( $post_id, '_cgs_btn_border_width', sanitize_text_field( $_POST['cgs_btn_border_width'] ) );
                if ( isset( $_POST['cgs_btn_border_style'] ) ) update_post_meta( $post_id, '_cgs_btn_border_style', sanitize_text_field( $_POST['cgs_btn_border_style'] ) );
                if ( isset( $_POST['cgs_btn_border_color'] ) ) update_post_meta( $post_id, '_cgs_btn_border_color', sanitize_text_field( $_POST['cgs_btn_border_color'] ) );

                // Button Normal Effects
                if ( isset( $_POST['cgs_btn_shadow'] ) ) update_post_meta( $post_id, '_cgs_btn_shadow', sanitize_text_field( $_POST['cgs_btn_shadow'] ) );
                if ( isset( $_POST['cgs_btn_scale'] ) ) update_post_meta( $post_id, '_cgs_btn_scale', sanitize_text_field( $_POST['cgs_btn_scale'] ) );
                if ( isset( $_POST['cgs_btn_lift'] ) ) update_post_meta( $post_id, '_cgs_btn_lift', sanitize_text_field( $_POST['cgs_btn_lift'] ) );

                // Button Hover
                if ( isset( $_POST['cgs_btn_hover_bg'] ) ) update_post_meta( $post_id, '_cgs_btn_hover_bg', sanitize_text_field( $_POST['cgs_btn_hover_bg'] ) );
                if ( isset( $_POST['cgs_btn_hover_text'] ) ) update_post_meta( $post_id, '_cgs_btn_hover_text', sanitize_text_field( $_POST['cgs_btn_hover_text'] ) );
                if ( isset( $_POST['cgs_btn_hover_border_color'] ) ) update_post_meta( $post_id, '_cgs_btn_hover_border_color', sanitize_text_field( $_POST['cgs_btn_hover_border_color'] ) );
                if ( isset( $_POST['cgs_btn_hover_shadow'] ) ) update_post_meta( $post_id, '_cgs_btn_hover_shadow', sanitize_text_field( $_POST['cgs_btn_hover_shadow'] ) );
                if ( isset( $_POST['cgs_btn_hover_scale'] ) ) update_post_meta( $post_id, '_cgs_btn_hover_scale', sanitize_text_field( $_POST['cgs_btn_hover_scale'] ) );
                if ( isset( $_POST['cgs_btn_hover_lift'] ) ) update_post_meta( $post_id, '_cgs_btn_hover_lift', sanitize_text_field( $_POST['cgs_btn_hover_lift'] ) );

                // Save card border color
                if ( isset( $_POST['cgs_card_border_clr'] ) ) {
                    update_post_meta( $post_id, '_cgs_card_border_clr', sanitize_text_field( $_POST['cgs_card_border_clr'] ) );
                }
                // Save card border width
                if ( isset( $_POST['cgs_card_border_width'] ) ) {
                    $width = intval( $_POST['cgs_card_border_width'] );
                    update_post_meta( $post_id, '_cgs_card_border_width', $width >= 0 ? $width : 0 );
                }
                // Save card border style
                if ( isset( $_POST['cgs_card_border_style'] ) ) {
                    $style = sanitize_text_field( $_POST['cgs_card_border_style'] );
                    $valid_styles = [ 'none', 'solid', 'dashed', 'dotted', 'double' ];
                    if ( in_array( $style, $valid_styles, true ) ) {
                        update_post_meta( $post_id, '_cgs_card_border_style', $style );
                    }
                }
                // Save card radius values
                foreach ( [ 'top_lt','top_rt','btm_rt','btm_lt' ] as $f ) {
                    $key = "cgs_card_radius_{$f}";
                    if ( isset( $_POST[ $key ] ) ) {
                        update_post_meta( $post_id, "_cgs_card_radius_{$f}", intval( $_POST[ $key ] ) );
                    }
                }
                // Save image radius values
                foreach ( [ 'top_lt','top_rt','btm_rt','btm_lt' ] as $f ) {
                    $key = "cgs_img_radius_{$f}";
                    if ( isset( $_POST[ $key ] ) ) {
                        update_post_meta( $post_id, "_cgs_img_radius_{$f}", intval( $_POST[ $key ] ) );
                    }
                }
                // Save padding values
                foreach ( [ 'top', 'right', 'bottom', 'left' ] as $f ) {
                    $key = "cgs_padding_{$f}";
                    if ( isset( $_POST[ $key ] ) ) {
                        update_post_meta( $post_id, "_cgs_padding_{$f}", intval( $_POST[ $key ] ) );
                    }
                }

                if ( isset( $_POST['cgs_card_shadow'] ) ) {
                    update_post_meta( $post_id, '_cgs_card_shadow', sanitize_text_field( $_POST['cgs_card_shadow'] ) );
                }
                if ( isset( $_POST['cgs_img_position'] ) ) {
                    update_post_meta( $post_id, '_cgs_img_position', sanitize_text_field( $_POST['cgs_img_position'] ) );
                }
                if ( isset( $_POST['cgs_vert_img_position'] ) ) {
                    update_post_meta( $post_id, '_cgs_vert_img_position', sanitize_text_field( $_POST['cgs_vert_img_position'] ) );
                }

                // Save Typography Settings
                $typo_props = ['tag', 'font_family', 'font_size', 'font_weight', 'font_style', 'line_height', 'text_transform', 'letter_spacing'];
                foreach (['title', 'body', 'button'] as $slug) {
                    foreach ($typo_props as $prop) {
                        $key = "cgs_{$slug}_{$prop}";
                        if ( isset( $_POST[$key] ) ) {
                            update_post_meta( $post_id, "_cgs_{$slug}_{$prop}", sanitize_text_field( $_POST[$key] ) );
                        }
                    }
                }
            }
            
            // Save layout selection
            if ( isset( $_POST['cgs_layout_nonce_field'] ) &&
                 wp_verify_nonce( $_POST['cgs_layout_nonce_field'], 'cgs_layout_nonce' ) &&
                 isset( $_POST['cgs_layout'] ) ) {
                update_post_meta(
                    $post_id,
                    '_cgs_layout',
                    sanitize_key( $_POST['cgs_layout'] )
                );
            }
        }

        public function enqueue_admin_styles( $hook ) {
            global $post;
            if ( in_array( $hook, [ 'post.php', 'post-new.php' ], true ) &&
                 isset( $post ) && $post->post_type === 'scroll_animation' ) {
                // Pickr Library (CDN)
                wp_enqueue_style('pickr-css', 'https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css', [], '1.9.1');
                wp_enqueue_script('pickr-js', 'https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js', [], '1.9.1', true);

                wp_enqueue_style( 'cgs-admin-css', CGS_URL . 'assets/css/gsap-scroll-admin.css', [], '1.3.0' );
                
                // Pass available fonts to JS
                $available_fonts = self::get_all_fonts();
                // Map to array of objects for easier JS handling if needed, or just regular object
                // JS expects array of {value, label} or object
                wp_localize_script( 'pickr-js', 'cgs_admin_vars', [
                    'available_fonts' => $available_fonts
                ]);
            }
        }
    }
}
