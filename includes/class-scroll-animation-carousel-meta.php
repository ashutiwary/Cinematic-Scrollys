<?php
if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('Cinematic_Scroll_Carousel_Items_Meta')) {

    class Cinematic_Scroll_Carousel_Items_Meta
    {

        public function __construct()
        {
            add_action('add_meta_boxes',        [$this, 'add_carousel_items_meta_box']);
            add_action('save_post',             [$this, 'save_carousel_items_meta_box']);
        }

        public function add_carousel_items_meta_box()
        {
            add_meta_box(
                'cgs_carousel_items',
                __('Carousel Items', 'cinematic-scroll'),
                [$this, 'render_carousel_items_meta_box'],
                'scroll_animation',
                'normal',
                'default'
            );
        }

        public function render_carousel_items_meta_box($post)
        {
            wp_nonce_field('cgs_carousel_items_nonce', 'cgs_carousel_items_nonce_field');
            $items = get_post_meta($post->ID, '_cgs_carousel_items', true);
            $items = is_array($items) ? $items : [];

            echo '<button type="button" class="button" id="cgs-add-carousel-item">'
                . esc_html__('Add New Item', 'cinematic-scroll')
                . '</button>';
            echo '<ul class="cgs-carousel-items-list">';
            foreach ($items as $i => $itm) {
                $itm = wp_parse_args($itm, [
                    'title'       => '',
                    'body'        => '',
                    'image'       => '',
                    'button_text' => '',
                    'button_url'  => '',
                    'card_bg_color' => '',
                    'title_text_color' => '',
                    'text_color' => '',
                    'btn_bg_color' => '',
                    'btn_text_color' => '',
                    'card_border_color' => '',
                    'card_border_width' => '',
                    'card_border_style' => 'none',
                    'img_position' => '',
                    'ver_img_position' => '',
                    'card_bg_image' => '', 
                    'wrapper_bg_color' => '',
                    'btn_target' => '_blank',
                    
                    // Typography Defaults
                    'title_font_family' => '', 'title_font_size' => '', 'title_font_weight' => '', 'title_font_style' => '', 'title_line_height' => '', 'title_text_transform' => '', 'title_letter_spacing' => '',
                    'body_font_family' => '', 'body_font_size' => '', 'body_font_weight' => '', 'body_font_style' => '', 'body_line_height' => '', 'body_text_transform' => '', 'body_letter_spacing' => '',
                    'button_font_family' => '', 'button_font_size' => '', 'button_font_weight' => '', 'button_font_style' => '', 'button_line_height' => '', 'button_text_transform' => '', 'button_letter_spacing' => '',
                ]);
?>
                <li class="cgs-carousel-item" data-index="<?php echo esc_attr($i); ?>">
                    <div class="cgs-card-header">
                        <span class="cgs-drag-handle" title="<?php esc_attr_e('Drag to reorder', 'cinematic-scroll'); ?>">☰</span>
                        <span class="cgs-card-collapsed-title">
                            <?php echo esc_html(wp_strip_all_tags($itm['title']) ?: sprintf(__('Item %d', 'cinematic-scroll'), $i + 1)); ?>
                        </span>
                        <button type="button" class="cgs-duplicate-item" title="<?php esc_attr_e('Duplicate', 'cinematic-scroll'); ?>">⧉</button>
                        <button type="button" class="cgs-toggle-item collapsed">►</button>
                    </div>
                    <div class="cgs-carousel-item-content">
                        <!-- TOP SECTION: Essential Fields (Always Visible) -->
                        <div class="cgs-card-top-section">
                            <!-- Title -->
                            <div class="cgs-form-group">
                                <label><?php esc_html_e('Title', 'cinematic-scroll'); ?></label>
                                <input type="text" name="cgs_carousel_items[<?php echo $i; ?>][title]" class="widefat" value="<?php echo esc_attr($itm['title']); ?>">
                            </div>

                            <!-- Body Text -->
                            <div class="cgs-form-group">
                                <label><?php esc_html_e('Body Text', 'cinematic-scroll'); ?></label>
                                <textarea name="cgs_carousel_items[<?php echo $i; ?>][body]" class="widefat" rows="4"><?php echo esc_textarea($itm['body']); ?></textarea>
                            </div>

                            <!-- Image Row: Card Image | Background Image -->
                            <div class="cgs-card-image-row">
                                <!-- Card Image -->
                                <div class="cgs-image-box">
                                    <label><?php esc_html_e('Card Image', 'cinematic-scroll'); ?></label>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width: 80px; height: 80px; background: #f0f0f1; display: flex; align-items: center; justify-content: center; border-radius: 4px; overflow: hidden;">
                                            <img src="<?php echo $itm['image'] ? esc_url(wp_get_attachment_thumb_url($itm['image'])) : ''; ?>"
                                                class="cgs-carousel-image-preview" style="max-width:100%; max-height:100%; <?php echo $itm['image'] ? '' : 'display:none;'; ?>">
                                            <span class="dashicons dashicons-format-image" style="font-size: 30px; height: 30px; width: 30px; color: #ccc; <?php echo $itm['image'] ? 'display:none;' : ''; ?>"></span>
                                        </div>
                                        <div>
                                            <input type="hidden" name="cgs_carousel_items[<?php echo $i; ?>][image]" class="cgs-carousel-image-id" value="<?php echo esc_attr($itm['image']); ?>">
                                            <button type="button" class="button cgs-select-image"><?php esc_html_e('Select', 'cinematic-scroll'); ?></button>
                                            <button type="button" class="button cgs-remove-image" style="color: #b32d2e; border-color: #b32d2e; <?php echo $itm['image'] ? '' : 'display:none;'; ?>"><?php esc_html_e('Remove', 'cinematic-scroll'); ?></button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Background Image -->
                                <div class="cgs-image-box">
                                    <label><?php esc_html_e('Background Image', 'cinematic-scroll'); ?></label>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width: 80px; height: 80px; background: #f0f0f1; display: flex; align-items: center; justify-content: center; border-radius: 4px; overflow: hidden;">
                                            <img src="<?php echo !empty($itm['card_bg_image']) ? esc_url(wp_get_attachment_thumb_url($itm['card_bg_image'])) : ''; ?>"
                                                class="cgs-bg-image-preview" style="max-width:100%; max-height:100%; <?php echo !empty($itm['card_bg_image']) ? '' : 'display:none;'; ?>">
                                            <span class="dashicons dashicons-format-image" style="font-size: 30px; height: 30px; width: 30px; color: #ccc; <?php echo !empty($itm['card_bg_image']) ? 'display:none;' : ''; ?>"></span>
                                        </div>
                                        <div>
                                            <input type="hidden" name="cgs_carousel_items[<?php echo $i; ?>][card_bg_image]" class="cgs-bg-image-id" value="<?php echo esc_attr($itm['card_bg_image'] ?? ''); ?>">
                                            <button type="button" class="button cgs-select-bg-image"><?php esc_html_e('Select', 'cinematic-scroll'); ?></button>
                                            <button type="button" class="button cgs-remove-bg-image" style="color: #b32d2e; border-color: #b32d2e; <?php echo !empty($itm['card_bg_image']) ? '' : 'display:none;'; ?>"><?php esc_html_e('Remove', 'cinematic-scroll'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Button Basics Row -->
                            <div class="cgs-card-button-row">
                                <div class="cgs-form-group" style="margin-bottom:0;">
                                    <label><?php esc_html_e('Button Text', 'cinematic-scroll'); ?></label>
                                    <input type="text" name="cgs_carousel_items[<?php echo $i; ?>][button_text]" class="widefat" value="<?php echo esc_attr($itm['button_text']); ?>">
                                </div>
                                <div class="cgs-form-group" style="margin-bottom:0;">
                                    <label><?php esc_html_e('Button URL', 'cinematic-scroll'); ?></label>
                                    <input type="text" name="cgs_carousel_items[<?php echo $i; ?>][button_url]" class="widefat" value="<?php echo esc_attr($itm['button_url']); ?>">
                                </div>
                                <div class="cgs-form-group" style="margin-bottom:0;">
                                    <label><?php esc_html_e('Relation', 'cinematic-scroll'); ?></label>
                                    <input type="text" name="cgs_carousel_items[<?php echo $i; ?>][btn_rel]" class="widefat" value="<?php echo esc_attr($itm['btn_rel'] ?? ''); ?>" placeholder="noopener">
                                </div>
                                <div class="cgs-form-group" style="margin-bottom:0;">
                                    <label><?php esc_html_e('Target', 'cinematic-scroll'); ?></label>
                                    <select name="cgs_carousel_items[<?php echo $i; ?>][btn_target]" class="widefat">
                                        <option value="_blank" <?php selected($itm['btn_target'] ?? '_blank', '_blank'); ?>><?php esc_html_e('New Tab', 'cinematic-scroll'); ?></option>
                                        <option value="_self" <?php selected($itm['btn_target'] ?? '_blank', '_self'); ?>><?php esc_html_e('Same Tab', 'cinematic-scroll'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- TAB NAVIGATION -->
                        <div class="cgs-card-tabs-nav">
                            <button type="button" class="cgs-card-tab-link active" data-tab="button"><?php esc_html_e('Button', 'cinematic-scroll'); ?></button>
                            <button type="button" class="cgs-card-tab-link" data-tab="card"><?php esc_html_e('Card', 'cinematic-scroll'); ?></button>
                            <button type="button" class="cgs-card-tab-link" data-tab="media"><?php esc_html_e('Media', 'cinematic-scroll'); ?></button>
                            <button type="button" class="cgs-card-tab-link" data-tab="typography"><?php esc_html_e('Typography', 'cinematic-scroll'); ?></button>
                        </div>

                        <!-- TAB CONTENT PANELS -->
                        <div class="cgs-card-tabs-content">
                            
                            <!-- BUTTON TAB -->
                            <div class="cgs-card-tab-panel active" data-panel="button">
                                <!-- Icon Settings -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                                    <div class="cgs-form-group" style="margin-bottom: 0;">
                                        <label><?php esc_html_e( 'Icon Class', 'cinematic-scroll' ); ?></label>
                                        <input type="text" name="cgs_carousel_items[<?php echo $i; ?>][btn_icon_class]" value="<?php echo esc_attr($itm['btn_icon_class'] ?? ''); ?>" class="widefat" placeholder="fas fa-arrow-right" />
                                    </div>
                                    <div class="cgs-form-group" style="margin-bottom: 0;">
                                        <label><?php esc_html_e( 'Icon Position', 'cinematic-scroll' ); ?></label>
                                        <select name="cgs_carousel_items[<?php echo $i; ?>][btn_icon_pos]" class="widefat">
                                            <option value="right" <?php selected($itm['btn_icon_pos'] ?? 'right', 'right'); ?>><?php esc_html_e('Right', 'cinematic-scroll'); ?></option>
                                            <option value="left" <?php selected($itm['btn_icon_pos'] ?? '', 'left'); ?>><?php esc_html_e('Left', 'cinematic-scroll'); ?></option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Colors Row -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                    <div class="cgs-form-group" style="margin-bottom:0;">
                                        <label><?php esc_html_e('Background', 'cinematic-scroll'); ?></label>
                                        <input type="text" class="cgs-color-picker" name="cgs_carousel_items[<?php echo $i; ?>][btn_bg_color]" value="<?php echo esc_attr($itm['btn_bg_color'] ?: ''); ?>" data-alpha="true">
                                    </div>
                                    <div class="cgs-form-group" style="margin-bottom:0;">
                                        <label><?php esc_html_e('Text Color', 'cinematic-scroll'); ?></label>
                                        <input type="text" class="cgs-color-picker" name="cgs_carousel_items[<?php echo $i; ?>][btn_text_color]" value="<?php echo esc_attr($itm['btn_text_color'] ?: ''); ?>" data-alpha="true">
                                    </div>
                                    <div class="cgs-form-group" style="margin-bottom:0;">
                                        <label><?php esc_html_e('Width', 'cinematic-scroll'); ?></label>
                                        <input type="text" name="cgs_carousel_items[<?php echo $i; ?>][btn_width]" value="<?php echo esc_attr($itm['btn_width'] ?? ''); ?>" placeholder="e.g. 200px" class="widefat">
                                    </div>
                                </div>

                                <!-- Dimensions -->
                                <div style="border-top:1px dashed #eee; padding-top:15px; margin-bottom:15px;">
                                    <strong style="display:block; margin-bottom:10px;"><?php esc_html_e( 'Dimensions', 'cinematic-scroll' ); ?></strong>
                                    <div style="display:flex; flex-wrap:wrap; gap:20px;">
                                        <!-- Padding -->
                                        <div>
                                            <span style="font-size:0.85em; display:block; margin-bottom:5px;"><?php esc_html_e( 'Padding (px)', 'cinematic-scroll' ); ?></span>
                                            <div style="display:flex; gap:5px;">
                                                <label style="font-size:0.85em;">T <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][btn_padding_top]" value="<?php echo esc_attr($itm['btn_padding_top'] ?? ''); ?>" style="width:40px;"></label>
                                                <label style="font-size:0.85em;">R <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][btn_padding_right]" value="<?php echo esc_attr($itm['btn_padding_right'] ?? ''); ?>" style="width:40px;"></label>
                                                <label style="font-size:0.85em;">B <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][btn_padding_bottom]" value="<?php echo esc_attr($itm['btn_padding_bottom'] ?? ''); ?>" style="width:40px;"></label>
                                                <label style="font-size:0.85em;">L <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][btn_padding_left]" value="<?php echo esc_attr($itm['btn_padding_left'] ?? ''); ?>" style="width:40px;"></label>
                                            </div>
                                        </div>
                                        <!-- Radius -->
                                        <div>
                                            <span style="font-size:0.85em; display:block; margin-bottom:5px;"><?php esc_html_e( 'Radius (px)', 'cinematic-scroll' ); ?></span>
                                            <div style="display:flex; gap:5px;">
                                                <label style="font-size:0.85em;">TL <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][btn_radius_top_lt]" value="<?php echo esc_attr($itm['btn_radius_top_lt'] ?? ''); ?>" style="width:40px;"></label>
                                                <label style="font-size:0.85em;">TR <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][btn_radius_top_rt]" value="<?php echo esc_attr($itm['btn_radius_top_rt'] ?? ''); ?>" style="width:40px;"></label>
                                                <label style="font-size:0.85em;">BR <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][btn_radius_btm_rt]" value="<?php echo esc_attr($itm['btn_radius_btm_rt'] ?? ''); ?>" style="width:40px;"></label>
                                                <label style="font-size:0.85em;">BL <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][btn_radius_btm_lt]" value="<?php echo esc_attr($itm['btn_radius_btm_lt'] ?? ''); ?>" style="width:40px;"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Border & Effects -->
                                <div style="border-top:1px dashed #eee; padding-top:15px; margin-bottom:15px;">
                                    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                                        <!-- Border -->
                                        <div style="flex:1; min-width: 200px;">
                                            <strong style="font-size:0.9em; display:block; margin-bottom:5px;"><?php esc_html_e('Border', 'cinematic-scroll'); ?></strong>
                                            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                                <label style="font-size:0.85em; margin:0;">W <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][btn_border_width]" value="<?php echo esc_attr($itm['btn_border_width'] ?? ''); ?>" style="width:45px;"></label>
                                                <select name="cgs_carousel_items[<?php echo $i; ?>][btn_border_style]" style="width:70px;">
                                                    <option value="">-</option>
                                                    <option value="none" <?php selected(($itm['btn_border_style'] ?? ''), 'none'); ?>>None</option>
                                                    <option value="solid" <?php selected(($itm['btn_border_style'] ?? ''), 'solid'); ?>>Solid</option>
                                                    <option value="dashed" <?php selected(($itm['btn_border_style'] ?? ''), 'dashed'); ?>>Dashed</option>
                                                </select>
                                                <input type="text" class="cgs-color-picker" name="cgs_carousel_items[<?php echo $i; ?>][btn_border_color]" value="<?php echo esc_attr($itm['btn_border_color'] ?? ''); ?>" data-alpha="true">
                                            </div>
                                        </div>
                                        <!-- Effects -->
                                        <div style="flex:1; min-width: 250px;">
                                            <strong style="font-size:0.9em; display:block; margin-bottom:5px;"><?php esc_html_e('Effects', 'cinematic-scroll'); ?></strong>
                                            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                                <label style="font-size:0.85em; margin:0;">Shadow <input type="text" name="cgs_carousel_items[<?php echo $i; ?>][btn_shadow]" value="<?php echo esc_attr($itm['btn_shadow'] ?? ''); ?>" placeholder="0 4px 10px rgba(0,0,0,0.2)" style="width:180px;"></label>
                                                <label style="font-size:0.85em; margin:0;">Scale <input type="number" step="0.01" name="cgs_carousel_items[<?php echo $i; ?>][btn_scale]" value="<?php echo esc_attr($itm['btn_scale'] ?? ''); ?>" placeholder="1" style="width:50px;"></label>
                                                <label style="font-size:0.85em; margin:0;">Lift <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][btn_lift]" value="<?php echo esc_attr($itm['btn_lift'] ?? ''); ?>" placeholder="0" style="width:45px;"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hover State -->
                                <div style="border-top:1px dashed #eee; padding-top:15px;">
                                    <strong style="display:block; margin-bottom:10px;"><?php esc_html_e('Hover State', 'cinematic-scroll'); ?></strong>
                                    <div style="display:flex; gap:15px; flex-wrap:wrap; align-items:center;">
                                        <div style="display:flex; align-items:center; gap:5px;">
                                            <label style="font-size:0.85em; margin:0;">BG</label>
                                            <input type="text" class="cgs-color-picker" name="cgs_carousel_items[<?php echo $i; ?>][btn_hover_bg]" value="<?php echo esc_attr($itm['btn_hover_bg'] ?? ''); ?>" data-alpha="true">
                                        </div>
                                        <div style="display:flex; align-items:center; gap:5px;">
                                            <label style="font-size:0.85em; margin:0;">Text</label>
                                            <input type="text" class="cgs-color-picker" name="cgs_carousel_items[<?php echo $i; ?>][btn_hover_text]" value="<?php echo esc_attr($itm['btn_hover_text'] ?? ''); ?>" data-alpha="true">
                                        </div>
                                        <div style="display:flex; align-items:center; gap:5px;">
                                            <label style="font-size:0.85em; margin:0;">Border</label>
                                            <input type="text" class="cgs-color-picker" name="cgs_carousel_items[<?php echo $i; ?>][btn_hover_border_color]" value="<?php echo esc_attr($itm['btn_hover_border_color'] ?? ''); ?>" data-alpha="true">
                                        </div>
                                        <label style="font-size:0.85em; margin:0;">Shadow <input type="text" name="cgs_carousel_items[<?php echo $i; ?>][btn_hover_shadow]" value="<?php echo esc_attr($itm['btn_hover_shadow'] ?? ''); ?>" placeholder="0 8px 25px rgba(0,0,0,0.2)" style="width:180px;"></label>
                                        <label style="font-size:0.85em; margin:0;">Scale <input type="number" step="0.01" name="cgs_carousel_items[<?php echo $i; ?>][btn_hover_scale]" value="<?php echo esc_attr($itm['btn_hover_scale'] ?? ''); ?>" placeholder="1.05" style="width:55px;"></label>
                                        <label style="font-size:0.85em; margin:0;">Lift <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][btn_hover_lift]" value="<?php echo esc_attr($itm['btn_hover_lift'] ?? ''); ?>" placeholder="5" style="width:45px;"></label>
                                    </div>
                                </div>
                            </div>

                            <!-- CARD TAB -->
                            <div class="cgs-card-tab-panel" data-panel="card">
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
                                    <div class="cgs-form-group" style="margin-bottom:0;">
                                        <label><?php esc_html_e('Card Inside Color', 'cinematic-scroll'); ?></label>
                                        <input type="text" class="cgs-color-picker" name="cgs_carousel_items[<?php echo $i; ?>][card_bg_color]" value="<?php echo esc_attr($itm['card_bg_color']) ?: ''; ?>" data-alpha="true">
                                    </div>
                                    <div class="cgs-form-group" style="margin-bottom:0;">
                                        <label><?php esc_html_e('Card Outside Color', 'cinematic-scroll'); ?></label>
                                        <input type="text" class="cgs-color-picker" name="cgs_carousel_items[<?php echo $i; ?>][wrapper_bg_color]" value="<?php echo esc_attr($itm['wrapper_bg_color'] ?? ''); ?>" data-alpha="true">
                                    </div>
                                </div>
                                
                                <div style="border-top:1px dashed #eee; padding-top:15px;">
                                    <strong style="display:block; margin-bottom:10px;"><?php esc_html_e('Card Border', 'cinematic-scroll'); ?></strong>
                                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                                        <div class="cgs-form-group" style="margin-bottom:0;">
                                            <label><?php esc_html_e('Color', 'cinematic-scroll'); ?></label>
                                            <input type="text" class="cgs-color-picker" name="cgs_carousel_items[<?php echo $i; ?>][card_border_color]" value="<?php echo esc_attr($itm['card_border_color'] ?: ''); ?>" data-alpha="true">
                                        </div>
                                        <div class="cgs-form-group" style="margin-bottom:0;">
                                            <label><?php esc_html_e('Width (px)', 'cinematic-scroll'); ?></label>
                                            <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][card_border_width]" value="<?php echo esc_attr($itm['card_border_width'] ?: ''); ?>" class="widefat">
                                        </div>
                                        <div class="cgs-form-group" style="margin-bottom:0;">
                                            <label><?php esc_html_e('Style', 'cinematic-scroll'); ?></label>
                                            <select name="cgs_carousel_items[<?php echo $i; ?>][card_border_style]" class="widefat">
                                                <option value="none" <?php selected($itm['card_border_style'] ?? 'none', 'none'); ?>><?php esc_html_e('None', 'cinematic-scroll'); ?></option>
                                                <option value="solid" <?php selected($itm['card_border_style'] ?? '', 'solid'); ?>><?php esc_html_e('Solid', 'cinematic-scroll'); ?></option>
                                                <option value="dashed" <?php selected($itm['card_border_style'] ?? '', 'dashed'); ?>><?php esc_html_e('Dashed', 'cinematic-scroll'); ?></option>
                                                <option value="dotted" <?php selected($itm['card_border_style'] ?? '', 'dotted'); ?>><?php esc_html_e('Dotted', 'cinematic-scroll'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MEDIA TAB -->
                            <div class="cgs-card-tab-panel" data-panel="media">
                                <?php 
                                $layout = get_post_meta($post->ID, '_cgs_layout', true);
                                $img_position = isset($itm['img_position']) ? $itm['img_position'] : 'left';
                                $ver_img_position = isset($itm['ver_img_position']) ? $itm['ver_img_position'] : 'right';
                                ?>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                    <div class="cgs-form-group" style="margin-bottom:0;">
                                        <label><?php esc_html_e('Image Position', 'cinematic-scroll'); ?></label>
                                        <?php if ('horizontal' === $layout) { ?>
                                            <select name="cgs_carousel_items[<?php echo $i; ?>][img_position]" class="widefat">
                                                <option value="" <?php selected($img_position, ''); ?>><?php esc_html_e('Default (Global)', 'cinematic-scroll'); ?></option>
                                                <option value="left" <?php selected($img_position, 'left'); ?>><?php esc_html_e('Left', 'cinematic-scroll'); ?></option>
                                                <option value="right" <?php selected($img_position, 'right'); ?>><?php esc_html_e('Right', 'cinematic-scroll'); ?></option>
                                            </select>
                                        <?php } else { ?>
                                            <select name="cgs_carousel_items[<?php echo $i; ?>][ver_img_position]" class="widefat">
                                                <option value="" <?php selected($ver_img_position, ''); ?>><?php esc_html_e('Default (Global)', 'cinematic-scroll'); ?></option>
                                                <option value="left" <?php selected($ver_img_position, 'left'); ?>><?php esc_html_e('Left', 'cinematic-scroll'); ?></option>
                                                <option value="right" <?php selected($ver_img_position, 'right'); ?>><?php esc_html_e('Right', 'cinematic-scroll'); ?></option>
                                            </select>
                                        <?php } ?>
                                    </div>
                                    <div class="cgs-form-group" style="margin-bottom:0;">
                                        <label><?php esc_html_e('Image Border Radius (px)', 'cinematic-scroll'); ?></label>
                                        <input type="number" name="cgs_carousel_items[<?php echo $i; ?>][img_border_radius]" value="<?php echo esc_attr($itm['img_border_radius'] ?? ''); ?>" class="widefat" placeholder="0">
                                    </div>
                                </div>
                            </div>

                            <!-- TYPOGRAPHY TAB -->
                            <div class="cgs-card-tab-panel" data-panel="typography">
                                <?php
                                $typo_sections = [
                                    'title'  => __('Title', 'cinematic-scroll'),
                                    'body'   => __('Body', 'cinematic-scroll'),
                                    'button' => __('Button', 'cinematic-scroll'),
                                ];
                                $available_fonts = Cinematic_Scroll_Style_Meta::get_all_fonts();
                                $tags = ['h1'=>'H1','h2'=>'H2','h3'=>'H3','h4'=>'H4','h5'=>'H5','h6'=>'H6','p'=>'P','div'=>'Div','span'=>'Span'];

                                foreach ($typo_sections as $slug => $label) {
                                    $ff = $itm["{$slug}_font_family"] ?? '';
                                    $tag = $itm["{$slug}_tag"] ?? '';
                                    $fs = $itm["{$slug}_font_size"] ?? '';
                                    $fw = $itm["{$slug}_font_weight"] ?? '';
                                    $fst = $itm["{$slug}_font_style"] ?? '';
                                    $lh = $itm["{$slug}_line_height"] ?? '';
                                    $tt = $itm["{$slug}_text_transform"] ?? '';
                                    $ls = $itm["{$slug}_letter_spacing"] ?? '';
                                    $txt_color_field = ($slug === 'title') ? 'title_text_color' : (($slug === 'body') ? 'text_color' : '');
                                    $txt_color = $txt_color_field ? ($itm[$txt_color_field] ?? '') : '';
                                    $show_tag = ($slug !== 'button');
                                    $show_color = ($slug !== 'button');
                                    ?>
                                    <div style="<?php echo ($slug !== 'title') ? 'border-top:1px dashed #eee; padding-top:15px; margin-top:15px;' : ''; ?>">
                                        <strong style="display:block; margin-bottom:10px;"><?php echo esc_html($label); ?></strong>
                                        
                                        <!-- Row 1: Font Family, Tag, Color -->
                                        <div style="display:flex; gap:15px; margin-bottom:10px; flex-wrap:wrap;">
                                            <div style="flex:2; min-width:150px;">
                                                <label style="font-size:0.85em;"><?php esc_html_e('Font', 'cinematic-scroll'); ?></label>
                                                <select name="cgs_carousel_items[<?php echo $i; ?>][<?php echo $slug; ?>_font_family]" style="width:100%;">
                                                    <?php foreach($available_fonts as $val => $name): ?>
                                                        <option value="<?php echo esc_attr($val); ?>" <?php selected($ff, $val); ?>><?php echo esc_html($name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <?php if($show_tag): ?>
                                            <div style="flex:1; min-width:80px;">
                                                <label style="font-size:0.85em;"><?php esc_html_e('Tag', 'cinematic-scroll'); ?></label>
                                                <select name="cgs_carousel_items[<?php echo $i; ?>][<?php echo $slug; ?>_tag]" style="width:100%;">
                                                    <option value="" <?php selected($tag, ''); ?>>Default</option>
                                                    <?php foreach($tags as $t_val => $t_label): ?>
                                                        <option value="<?php echo esc_attr($t_val); ?>" <?php selected($tag, $t_val); ?>><?php echo esc_html($t_label); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <?php endif; ?>
                                            <?php if($show_color): ?>
                                            <div style="flex:1; min-width:60px;">
                                                <label style="font-size:0.85em;"><?php esc_html_e('Color', 'cinematic-scroll'); ?></label>
                                                <input type="text" class="cgs-color-picker" name="cgs_carousel_items[<?php echo $i; ?>][<?php echo $txt_color_field; ?>]" value="<?php echo esc_attr($txt_color); ?>" data-alpha="true">
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Row 2: Size, Weight, Style, Line Height, Transform, Spacing -->
                                        <div style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <div style="flex:1; min-width:60px;">
                                                <label style="font-size:0.85em;"><?php esc_html_e('Size', 'cinematic-scroll'); ?></label>
                                                <input type="text" name="cgs_carousel_items[<?php echo $i; ?>][<?php echo $slug; ?>_font_size]" value="<?php echo esc_attr($fs); ?>" placeholder="16px" style="width:100%;">
                                            </div>
                                            <div style="flex:1; min-width:70px;">
                                                <label style="font-size:0.85em;"><?php esc_html_e('Weight', 'cinematic-scroll'); ?></label>
                                                <select name="cgs_carousel_items[<?php echo $i; ?>][<?php echo $slug; ?>_font_weight]" style="width:100%;">
                                                    <option value="" <?php selected($fw, ''); ?>>-</option>
                                                    <option value="normal" <?php selected($fw, 'normal'); ?>>Normal</option>
                                                    <option value="bold" <?php selected($fw, 'bold'); ?>>Bold</option>
                                                    <?php foreach([100,200,300,400,500,600,700,800,900] as $w): ?>
                                                        <option value="<?php echo $w; ?>" <?php selected($fw, $w); ?>><?php echo $w; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div style="flex:1; min-width:70px;">
                                                <label style="font-size:0.85em;"><?php esc_html_e('Style', 'cinematic-scroll'); ?></label>
                                                <select name="cgs_carousel_items[<?php echo $i; ?>][<?php echo $slug; ?>_font_style]" style="width:100%;">
                                                    <option value="" <?php selected($fst, ''); ?>>-</option>
                                                    <option value="normal" <?php selected($fst, 'normal'); ?>>Normal</option>
                                                    <option value="italic" <?php selected($fst, 'italic'); ?>>Italic</option>
                                                </select>
                                            </div>
                                            <div style="flex:1; min-width:60px;">
                                                <label style="font-size:0.85em;"><?php esc_html_e('Line Ht', 'cinematic-scroll'); ?></label>
                                                <input type="text" name="cgs_carousel_items[<?php echo $i; ?>][<?php echo $slug; ?>_line_height]" value="<?php echo esc_attr($lh); ?>" placeholder="1.5" style="width:100%;">
                                            </div>
                                            <div style="flex:1; min-width:80px;">
                                                <label style="font-size:0.85em;"><?php esc_html_e('Transform', 'cinematic-scroll'); ?></label>
                                                <select name="cgs_carousel_items[<?php echo $i; ?>][<?php echo $slug; ?>_text_transform]" style="width:100%;">
                                                    <option value="" <?php selected($tt, ''); ?>>-</option>
                                                    <option value="none" <?php selected($tt, 'none'); ?>>None</option>
                                                    <option value="uppercase" <?php selected($tt, 'uppercase'); ?>>Upper</option>
                                                    <option value="lowercase" <?php selected($tt, 'lowercase'); ?>>Lower</option>
                                                    <option value="capitalize" <?php selected($tt, 'capitalize'); ?>>Cap</option>
                                                </select>
                                            </div>
                                            <div style="flex:1; min-width:60px;">
                                                <label style="font-size:0.85em;"><?php esc_html_e('Spacing', 'cinematic-scroll'); ?></label>
                                                <input type="text" name="cgs_carousel_items[<?php echo $i; ?>][<?php echo $slug; ?>_letter_spacing]" value="<?php echo esc_attr($ls); ?>" placeholder="0px" style="width:100%;">
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                        </div><!-- .cgs-card-tabs-content -->

                        <p>
                            <button type="button" class="button cgs-remove-carousel-item">
                                <?php esc_html_e('Remove Item', 'cinematic-scroll'); ?>
                            </button>
                        </p>
                    </div>
                </li>
<?php
            }
            echo '</ul>';
        }

        public function save_carousel_items_meta_box($post_id)
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            if (
                ! isset($_POST['cgs_carousel_items_nonce_field'])
                || ! wp_verify_nonce($_POST['cgs_carousel_items_nonce_field'], 'cgs_carousel_items_nonce')
            ) {
                return;
            }
            if (
                isset($_POST['post_type']) && 'scroll_animation' === $_POST['post_type']
                && ! current_user_can('edit_post', $post_id)
            ) {
                return;
            }

            $raw   = isset($_POST['cgs_carousel_items'])
                ? array_values($_POST['cgs_carousel_items'])
                : [];
            $clean = [];

            // Save global image position selection for layout
            $layout = get_post_meta($post_id, '_cgs_layout', true);
            if ('horizontal' === $layout && isset($_POST['cgs_img_position'])) {
                update_post_meta($post_id, '_cgs_img_position', sanitize_text_field($_POST['cgs_img_position']));
            } elseif ('vertical' === $layout && isset($_POST['cgs_vert_img_position'])) {
                update_post_meta($post_id, '_cgs_vert_img_position', sanitize_text_field($_POST['cgs_vert_img_position']));
            }

            foreach ($raw as $itm) {
                $clean[] = [
                    'title'       => wp_kses_post($itm['title'] ?? ''),
                    'body'        => wp_kses_post($itm['body'] ?? ''),
                    'image'       => isset($itm['image']) ? absint($itm['image']) : '',
                    'button_text' => sanitize_text_field($itm['button_text'] ?? ''),
                    'button_url'  => esc_url_raw($itm['button_url'] ?? ''),
                    'btn_rel'     => sanitize_text_field($itm['btn_rel'] ?? ''),
                    'btn_target'  => sanitize_text_field($itm['btn_target'] ?? '_blank'),
                    'card_bg_color' => sanitize_text_field($itm['card_bg_color'] ?? ''),
                    'title_text_color' => sanitize_text_field($itm['title_text_color'] ?? ''),
                    'text_color' => sanitize_text_field($itm['text_color'] ?? ''),
                    'btn_bg_color' => sanitize_text_field($itm['btn_bg_color'] ?? ''),
                    'btn_text_color' => sanitize_text_field($itm['btn_text_color'] ?? ''),
                    'btn_width'      => sanitize_text_field($itm['btn_width'] ?? ''),
                    // Button Icon
                    'btn_icon_class' => sanitize_text_field($itm['btn_icon_class'] ?? ''),
                    'btn_icon_pos'   => sanitize_text_field($itm['btn_icon_pos'] ?? 'right'),
                    // Button Padding - Allow empty for inheritance
                    'btn_padding_top'    => (isset($itm['btn_padding_top']) && $itm['btn_padding_top']!=='') ? intval($itm['btn_padding_top']) : '',
                    'btn_padding_right'  => (isset($itm['btn_padding_right']) && $itm['btn_padding_right']!=='') ? intval($itm['btn_padding_right']) : '',
                    'btn_padding_bottom' => (isset($itm['btn_padding_bottom']) && $itm['btn_padding_bottom']!=='') ? intval($itm['btn_padding_bottom']) : '',
                    'btn_padding_left'   => (isset($itm['btn_padding_left']) && $itm['btn_padding_left']!=='') ? intval($itm['btn_padding_left']) : '',
                    // Button Radius - Allow empty
                    'btn_radius_top_lt' => (isset($itm['btn_radius_top_lt']) && $itm['btn_radius_top_lt']!=='') ? intval($itm['btn_radius_top_lt']) : '',
                    'btn_radius_top_rt' => (isset($itm['btn_radius_top_rt']) && $itm['btn_radius_top_rt']!=='') ? intval($itm['btn_radius_top_rt']) : '',
                    'btn_radius_btm_rt' => (isset($itm['btn_radius_btm_rt']) && $itm['btn_radius_btm_rt']!=='') ? intval($itm['btn_radius_btm_rt']) : '',
                    'btn_radius_btm_lt' => (isset($itm['btn_radius_btm_lt']) && $itm['btn_radius_btm_lt']!=='') ? intval($itm['btn_radius_btm_lt']) : '',
                    
                    // New Border Fields
                    'btn_border_width' => sanitize_text_field($itm['btn_border_width'] ?? ''),
                    'btn_border_style' => sanitize_text_field($itm['btn_border_style'] ?? ''),
                    'btn_border_color' => sanitize_text_field($itm['btn_border_color'] ?? ''),
                    // New Normal Effects
                    'btn_shadow' => sanitize_text_field($itm['btn_shadow'] ?? ''),
                    'btn_scale'  => sanitize_text_field($itm['btn_scale'] ?? ''),
                    'btn_lift'   => sanitize_text_field($itm['btn_lift'] ?? ''),
                    // New Hover Fields
                    'btn_hover_bg' => sanitize_text_field($itm['btn_hover_bg'] ?? ''),
                    'btn_hover_text' => sanitize_text_field($itm['btn_hover_text'] ?? ''),
                    'btn_hover_border_color' => sanitize_text_field($itm['btn_hover_border_color'] ?? ''),
                    'btn_hover_shadow' => sanitize_text_field($itm['btn_hover_shadow'] ?? ''),
                    'btn_hover_scale' => sanitize_text_field($itm['btn_hover_scale'] ?? ''),
                    'btn_hover_lift' => sanitize_text_field($itm['btn_hover_lift'] ?? ''),
                    
                    'card_border_color' => sanitize_text_field($itm['card_border_color'] ?? ''),
                    'card_border_width' => isset($itm['card_border_width']) ? intval($itm['card_border_width']) : '',
                    'card_border_style' => sanitize_text_field($itm['card_border_style'] ?? ''),
                    'img_position' => isset($itm['img_position']) ? sanitize_text_field($itm['img_position']) : '',
                    'ver_img_position' => isset($itm['ver_img_position']) ? sanitize_text_field($itm['ver_img_position']) : '',
                    'card_bg_image' => isset($itm['card_bg_image']) ? absint($itm['card_bg_image']) : '',
                    'wrapper_bg_color' => sanitize_text_field($itm['wrapper_bg_color'] ?? ''),
                ];

                // Typography Fields
                $typo_props = ['tag', 'font_family', 'font_size', 'font_weight', 'font_style', 'line_height', 'text_transform', 'letter_spacing'];
                foreach (['title', 'body', 'button'] as $slug) {
                    foreach ($typo_props as $prop) {
                        $key = "{$slug}_{$prop}"; // matches HTML name suffix and array key
                        // $itm contains sanitized $_POST values? No, $raw contains $_POST values. $itm is an element of $raw.
                        // $itm['title_font_family'] check.
                        if (isset($itm[$key])) {
                            $clean[count($clean)-1][$key] = sanitize_text_field($itm[$key]);
                        } else {
                            $clean[count($clean)-1][$key] = ''; // Ensure key exists
                        }
                    }
                }
            }

            update_post_meta($post_id, '_cgs_carousel_items', $clean);
        }

    }

    new Cinematic_Scroll_Carousel_Items_Meta();
}
