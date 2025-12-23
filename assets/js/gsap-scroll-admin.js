jQuery(function ($) {
  try {
    if (typeof CGS_Admin_Data === "undefined") {
      return;
    }

    // Unified data object provided by Cinematic_Scroll_Assets class
    const postId = CGS_Admin_Data.postId;
    const storageKey = `cgs_collapse_${postId}`;
    let collapseState = JSON.parse(localStorage.getItem(storageKey) || "{}");
    let itemIndex = CGS_Admin_Data.itemIndex;
    const layout = CGS_Admin_Data.layout || "horizontal";
    const $list = $(".cgs-carousel-items-list");

    // Helper: Initialize Pickr on a specific element
    function initPickr($el) {
      if ($el.data('pickr-init')) return;
      $el.data('pickr-init', true);

      const defaultColor = $el.val() || null;
      // Create container
      const $container = $('<div></div>').insertAfter($el);
      $el.hide();

      const pickr = Pickr.create({
        el: $container[0],
        theme: 'classic',
        default: defaultColor,
        swatches: [
          'rgba(244, 67, 54, 1)',
          'rgba(233, 30, 99, 0.95)',
          'rgba(156, 39, 176, 0.9)',
          'rgba(103, 58, 183, 0.85)',
          'rgba(63, 81, 181, 0.8)',
          'rgba(33, 150, 243, 0.75)',
          'rgba(3, 169, 244, 0.7)',
          'rgba(0, 188, 212, 0.7)',
          'rgba(0, 150, 136, 0.75)',
          'rgba(76, 175, 80, 0.8)',
          'rgba(139, 195, 74, 0.85)',
          'rgba(205, 220, 57, 0.9)',
          'rgba(255, 235, 59, 0.95)',
          'rgba(255, 193, 7, 1)'
        ],
        components: {
          preview: true,
          opacity: true,
          hue: true,
          interaction: {
            hex: true,
            rgba: true,
            input: true,
            clear: true,
            save: true
          }
        }
      });

      pickr.on('save', (color, instance) => {
        const val = color ? color.toRGBA().toString(0) : '';
        $el.val(val).trigger('change');
        instance.hide();
      });
      pickr.on('clear', instance => {
        $el.val('').trigger('change');
        instance.hide();
      });
    }

    // Initialize all existing pickers
    if (typeof Pickr !== 'undefined') {
      // 1. Handle potential conflict with WP Color Picker
      // Only target input.cgs-color-picker that might have been auto-initialized by WP
      $('input.cgs-color-picker.wp-color-picker').each(function () {
        const $parent = $(this).closest('.wp-picker-container');
        if ($parent.length) {
          // Unwrap from WP picker container
          $(this).insertBefore($parent);
          $parent.remove();
        }
      });

      // 2. Initialize Pickr on our fields
      $('.cgs-color-picker').each(function () {
        initPickr($(this));
      });
    }

    function initCollapse() {
      $list.find(".cgs-carousel-item").each(function () {
        const $el = $(this);
        const idx = $el.data("index");
        const isCollapsed = !!collapseState[idx];
        $el.find(".cgs-carousel-item-content").toggle(!isCollapsed);
        $el
          .find(".cgs-toggle-item")
          .toggleClass("collapsed", isCollapsed)
          .html(isCollapsed ? "►" : "▼");
      });
    }

    function toggleCollapse(idx, isCollapsed) {
      collapseState[idx] = isCollapsed;
      localStorage.setItem(storageKey, JSON.stringify(collapseState));
    }

    function getItemHtml(idx) {
      const typoSections = [
        { slug: 'title', label: 'Title Typography' },
        { slug: 'body', label: 'Body Typography' },
        { slug: 'button', label: 'Button Typography' }
      ];

      let typoHtml = '';

      // Build Font Options using localized data
      let fontOptions = '';
      if (typeof CGS_Admin_Data !== 'undefined' && CGS_Admin_Data.available_fonts) {
        // It can be an object or array depending on PHP's wp_localize_script (associative array becomes object).
        for (const [val, label] of Object.entries(CGS_Admin_Data.available_fonts)) {
          fontOptions += `<option value="${val}">${label}</option>`;
        }
      } else {
        // Fallback if JS var missing
        fontOptions = `<option value="">Default</option><option value="Arial, sans-serif">Arial</option>`;
      }

      // Build Tag Options
      const availableTags = {
        '': 'Default', 'h1': 'H1', 'h2': 'H2', 'h3': 'H3', 'h4': 'H4', 'h5': 'H5', 'h6': 'H6', 'p': 'P', 'div': 'Result Div', 'span': 'Span'
      };
      let tagOptions = '';
      for (const [tVal, tLabel] of Object.entries(availableTags)) {
        tagOptions += `<option value="${tVal}">${tLabel}</option>`;
      }

      typoSections.forEach(sec => {
        let showTag = (sec.slug !== 'button'); // Hide for button if desired, or show. PHP hides it.
        let tagSelectHtml = '';

        if (showTag) {
          tagSelectHtml = `
                <div style="flex: 1;">
                    <label style="font-size:0.85em; display:block; margin-bottom:2px;">HTML Tag</label>
                    <select name="cgs_carousel_items[${idx}][${sec.slug}_tag]" style="width:100%;">
                        ${tagOptions}
                    </select>
                </div>
              `;
        }

        typoHtml += `
            <div style="width: 100%; margin-top: 15px; padding-top: 10px; border-top: 1px dashed #eee;">
                <strong style="display:block; margin-bottom: 10px;">${sec.label}</strong>
                
                <div class="cgs-form-group" style="margin-bottom: 10px; display:flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label style="font-size:0.85em; display:block; margin-bottom:2px;">Font Family</label>
                        <select name="cgs_carousel_items[${idx}][${sec.slug}_font_family]" style="width:100%; max-width: 100%;">
                            ${fontOptions}
                        </select>
                    </div>
                    ${tagSelectHtml}
                </div>

                <div style="display:flex; flex-wrap:wrap; gap:10px;">
                    <div style="flex:1; min-width: 60px;">
                        <label style="font-size:0.85em; display:block;">Size</label>
                        <input type="text" name="cgs_carousel_items[${idx}][${sec.slug}_font_size]" value="" placeholder="16px" style="width:100%;">
                    </div>
                    <div style="flex:1; min-width: 80px;">
                        <label style="font-size:0.85em; display:block;">Weight</label>
                        <select name="cgs_carousel_items[${idx}][${sec.slug}_font_weight]" style="width:100%;">
                            <option value="">Default</option>
                            <option value="normal">Normal</option>
                            <option value="bold">Bold</option>
                            <option value="100">100</option><option value="200">200</option><option value="300">300</option>
                            <option value="400">400</option><option value="500">500</option><option value="600">600</option>
                            <option value="700">700</option><option value="800">800</option><option value="900">900</option>
                        </select>
                    </div>
                    <div style="flex:1; min-width: 80px;">
                        <label style="font-size:0.85em; display:block;">Style</label>
                        <select name="cgs_carousel_items[${idx}][${sec.slug}_font_style]" style="width:100%;">
                            <option value="">Default</option>
                            <option value="normal">Normal</option>
                            <option value="italic">Italic</option>
                        </select>
                    </div>
                    <div style="flex:1; min-width: 60px;">
                        <label style="font-size:0.85em; display:block;">Line Ht</label>
                        <input type="text" name="cgs_carousel_items[${idx}][${sec.slug}_line_height]" value="" placeholder="1.5" style="width:100%;">
                    </div>
                    <div style="flex:1; min-width: 90px;">
                        <label style="font-size:0.85em; display:block;">Transform</label>
                        <select name="cgs_carousel_items[${idx}][${sec.slug}_text_transform]" style="width:100%;">
                            <option value="">Default</option>
                            <option value="none">None</option>
                            <option value="uppercase">Upper</option>
                            <option value="lowercase">Lower</option>
                            <option value="capitalize">Cap</option>
                        </select>
                    </div>
                    <div style="flex:1; min-width: 60px;">
                        <label style="font-size:0.85em; display:block;">Spacing</label>
                        <input type="text" name="cgs_carousel_items[${idx}][${sec.slug}_letter_spacing]" value="" placeholder="0px" style="width:100%;">
                    </div>
                </div>
            </div>`;
      });

      return `
      <li class="cgs-carousel-item" data-index="${idx}">
        <div class="cgs-card-header">
          <span class="cgs-card-collapsed-title">Item ${idx + 1}</span>
          <button type="button" class="cgs-toggle-item">▼</button>
        </div>
        <div class="cgs-carousel-item-content">
          <div class="cgs-editor-grid">
            
            <!-- Section 1: Content Details -->
            <div class="cgs-editor-section cgs-section-content">
                <span class="cgs-section-title">Content Details</span>
                
                <div class="cgs-form-group">
                    <label>Title</label>
                    <input type="text" name="cgs_carousel_items[${idx}][title]" class="widefat" />
                </div>

                <div class="cgs-form-group">
                    <label>Body Text</label>
                    <textarea name="cgs_carousel_items[${idx}][body]" class="widefat" rows="5"></textarea>
                </div>


            </div>

            <!-- Section 2: Media & Assets -->
            <div class="cgs-editor-section cgs-section-media">
                <span class="cgs-section-title">Media & Assets</span>

                <div class="cgs-form-group">
                    <label>Image</label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width: 80px; height: 80px; background: #f0f0f1; display: flex; align-items: center; justify-content: center; border-radius: 4px; overflow: hidden;">
                            <img class="cgs-carousel-image-preview" style="max-width:100%; max-height:100%; display:none;" />
                            <span class="dashicons dashicons-format-image" style="font-size: 30px; height: 30px; width: 30px; color: #ccc;"></span>
                        </div>
                        <div>
                            <input type="hidden" name="cgs_carousel_items[${idx}][image]" class="cgs-carousel-image-id" />
                            <button type="button" class="button cgs-select-image">Select Image</button>
                            <button type="button" class="button cgs-remove-image" style="color: #b32d2e; border-color: #b32d2e; display:none;">Remove Image</button>
                        </div>
                    </div>
                </div>
                
                <div class="cgs-form-group">
                ${layout === "horizontal"
          ? `<label>Image Position (v1.1.1)</label>
                     <select name="cgs_carousel_items[${idx}][img_position]">
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                     </select>`
          : `<label>Image Position</label>
                     <select name="cgs_carousel_items[${idx}][ver_img_position]">
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                     </select>`
        }
                </div>

                <div class="cgs-form-group">
                    <label>Background Image</label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width: 80px; height: 80px; background: #f0f0f1; display: flex; align-items: center; justify-content: center; border-radius: 4px; overflow: hidden;">
                            <img class="cgs-bg-image-preview" style="max-width:100%; max-height:100%; display:none;" />
                            <span class="dashicons dashicons-format-image" style="font-size: 30px; height: 30px; width: 30px; color: #ccc;"></span>
                        </div>
                        <div>
                            <input type="hidden" name="cgs_carousel_items[${idx}][card_bg_image]" class="cgs-bg-image-id" />
                            <button type="button" class="button cgs-select-bg-image">Select BG Image</button>
                            <button type="button" class="button cgs-remove-bg-image" style="color: #b32d2e; border-color: #b32d2e; display:none;">Remove BG</button>
                        </div>
                    </div>
                </div>
            </div>

                <div class="cgs-editor-section cgs-section-button">
                    <span class="cgs-section-title">Button Settings</span>
                    
                    <!-- Button Settings Grid -->
                    <!-- Row 1: Text, URL, and Link Relation -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="cgs-form-group" style="margin-bottom:0;">
                            <label>Button Text</label>
                            <input type="text" name="cgs_carousel_items[${idx}][button_text]" class="widefat" value="">
                        </div>
                        <div class="cgs-form-group" style="margin-bottom:0;">
                            <label>Button URL</label>
                            <input type="text" name="cgs_carousel_items[${idx}][button_url]" class="widefat" value="">
                        </div>
                        <div class="cgs-form-group" style="margin-bottom:0;">
                            <label>Link Relation</label>
                            <input type="text" name="cgs_carousel_items[${idx}][btn_rel]" class="widefat" value="" placeholder="noopener">
                        </div>
                    </div>

                    <!-- Row 2: Icons and Colors -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        
                        <!-- Column 1: Icon Settings -->
                        <div>
                            <div class="cgs-form-group" style="margin-bottom: 15px;">
                                <label>Icon Class</label>
                                <input type="text" name="cgs_carousel_items[${idx}][btn_icon_class]" value="" class="widefat" placeholder="fas fa-arrow-right" />
                            </div>
                            <div class="cgs-form-group" style="margin-bottom: 0;">
                                <label>Icon Pos</label>
                                <select name="cgs_carousel_items[${idx}][btn_icon_pos]" class="widefat">
                                    <option value="right">Right</option>
                                    <option value="left">Left</option>
                                </select>
                            </div>
                        </div>

                        <!-- Column 2: Color Settings -->
                        <div>
                            <div class="cgs-style-item cgs-form-group" style="margin-bottom: 10px; display:flex; align-items:center; gap:10px;">
                                <label style="width: 140px; margin-bottom:0;">Button Background</label>
                                <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_bg_color]" value="" data-alpha="true" />
                            </div>

                            <div class="cgs-style-item cgs-form-group" style="display:flex; align-items:center; gap:10px;">
                                <label style="width: 140px; margin-bottom:0;">Text Color</label>
                                <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_text_color]" value="" data-alpha="true" />
                            </div>

                            <div class="cgs-form-group" style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                                <label style="width: 140px; margin-bottom: 0;">Button Width</label>
                                <input type="text" name="cgs_carousel_items[${idx}][btn_width]" value="" placeholder="e.g. 200px" style="width: 100px;">
                            </div>
                        </div>
                    </div>

                    <!-- Button Dimensions Row -->
                    <div style="border-top:1px dashed #eee; padding-top:10px; margin-top:10px;">
                        <strong style="display:block; margin-bottom:10px;">Dimensions:</strong>
                        
                        <div style="display:flex; flex-wrap:wrap; gap:20px;">
                            <!-- Padding -->
                            <div>
                                <strong style="font-size:0.9em; display:block; margin-bottom:5px;">Padding (px):</strong>
                                <div style="display:flex; gap:5px;">
                                    <label style="font-size:0.9em;">T <input type="number" name="cgs_carousel_items[${idx}][btn_padding_top]" value="" style="width:45px;"></label>
                                    <label style="font-size:0.9em;">R <input type="number" name="cgs_carousel_items[${idx}][btn_padding_right]" value="" style="width:45px;"></label>
                                    <label style="font-size:0.9em;">B <input type="number" name="cgs_carousel_items[${idx}][btn_padding_bottom]" value="" style="width:45px;"></label>
                                    <label style="font-size:0.9em;">L <input type="number" name="cgs_carousel_items[${idx}][btn_padding_left]" value="" style="width:45px;"></label>
                                </div>
                            </div>

                            <!-- Radius -->
                            <div>
                                <strong style="font-size:0.9em; display:block; margin-bottom:5px;">Radius (px):</strong>
                                <div style="display:flex; gap:5px;">
                                    <label style="font-size:0.9em;">TL <input type="number" name="cgs_carousel_items[${idx}][btn_radius_top_lt]" value="" style="width:40px;"></label>
                                    <label style="font-size:0.9em;">TR <input type="number" name="cgs_carousel_items[${idx}][btn_radius_top_rt]" value="" style="width:40px;"></label>
                                    <label style="font-size:0.9em;">BR <input type="number" name="cgs_carousel_items[${idx}][btn_radius_btm_rt]" value="" style="width:40px;"></label>
                                    <label style="font-size:0.9em;">BL <input type="number" name="cgs_carousel_items[${idx}][btn_radius_btm_lt]" value="" style="width:40px;"></label>
                                </div>
                            </div>
                        </div>

                        <!-- Button Border & Normal Effects -->
                            <!-- Border -->
                            <div style="flex:1; min-width: 200px;">
                                <strong style="font-size:0.9em; display:block; margin-bottom:5px;">Button Normal Border:</strong>
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <label style="font-size:0.85em; margin:0;">Width <input type="number" name="cgs_carousel_items[${idx}][btn_border_width]" value="" style="width:50px;"></label>
                                    <label style="font-size:0.85em; margin:0;">Style 
                                    <select name="cgs_carousel_items[${idx}][btn_border_style]" style="width:80px;">
                                        <option value="">Default</option>
                                        <option value="none">None</option>
                                        <option value="solid">Solid</option>
                                        <option value="dashed">Dashed</option>
                                    </select></label>
                                    <div style="display:flex; align-items:center; gap:5px;">
                                        <label style="font-size:0.85em; margin:0;">Color</label>
                                        <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_border_color]" value="" data-alpha="true">
                                    </div>
                                </div>
                            </div>
                            <!-- Effects -->
                            <div style="flex:1; min-width: 250px;">
                                <strong style="font-size:0.9em; display:block; margin-bottom:5px;">Normal Effects:</strong>
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <label style="font-size:0.85em; display:flex; align-items:center; gap:5px; margin:0;">Shadow <input type="text" name="cgs_carousel_items[${idx}][btn_shadow]" value="" placeholder="e.g. 0 4px 10px rgba(0,0,0,0.2)" style="width:230px;"></label>
                                    <label style="font-size:0.85em; display:flex; align-items:center; gap:5px; margin:0;">Scale <input type="number" step="0.01" name="cgs_carousel_items[${idx}][btn_scale]" value="" placeholder="1" style="width:50px;"></label>
                                    <label style="font-size:0.85em; display:flex; align-items:center; gap:5px; margin:0;">Lift <input type="number" name="cgs_carousel_items[${idx}][btn_lift]" value="" placeholder="0" style="width:50px;"></label>
                                </div>
                            </div>
                        </div>

                        <!-- Button Hover Settings -->
                        <div style="margin-top:15px; padding-top:10px; border-top:1px dashed #eee;">
                            <strong style="display:block; margin-bottom:10px;">Button Hover State:</strong>
                            <div style="display:flex; gap:15px; flex-wrap:wrap; align-items:center;">
                                <div style="display:flex; align-items:center; gap:5px;">
                                    <label style="font-size:0.85em; margin:0;">Background</label>
                                    <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_hover_bg]" value="" data-alpha="true">
                                </div>
                                <div style="display:flex; align-items:center; gap:5px;">
                                    <label style="font-size:0.85em; margin:0;">Text Color</label>
                                    <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_hover_text]" value="" data-alpha="true">
                                </div>
                                <div style="display:flex; align-items:center; gap:5px;">
                                    <label style="font-size:0.85em; margin:0;">Border Color</label>
                                    <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_hover_border_color]" value="" data-alpha="true">
                                </div>
                                <label style="font-size:0.85em; display:flex; align-items:center; gap:5px; margin:0;">
                                    Hover Shadow <input type="text" name="cgs_carousel_items[${idx}][btn_hover_shadow]" value="" placeholder="e.g. 0 8px 25px rgba(0,0,0,0.2)" style="width:230px;">
                                </label>
                                <label style="font-size:0.85em; display:flex; align-items:center; gap:5px; margin:0;">
                                    Hover Scale <input type="number" step="0.01" name="cgs_carousel_items[${idx}][btn_hover_scale]" value="" placeholder="1.05" style="width:60px;">
                                </label>
                                <label style="font-size:0.85em; display:flex; align-items:center; gap:5px; margin:0;">
                                    Hover Lift <input type="number" name="cgs_carousel_items[${idx}][btn_hover_lift]" value="" placeholder="5" style="width:50px;">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Section 4: Styling Options -->
            <div class="cgs-editor-section cgs-section-style">
                <span class="cgs-section-title">Styling Options</span>
                
                <div class="cgs-style-grid">
                    <div class="cgs-style-item cgs-form-group">
                        <label>Card Inside Color</label>
                        <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][card_bg_color]" value="" data-alpha="true" />
                    </div>
                    
                    <div class="cgs-style-item cgs-form-group">
                        <label>Card Outside Color</label>
                        <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][wrapper_bg_color]" value="" data-alpha="true" />
                    </div>

                    <div class="cgs-style-item cgs-form-group">
                        <label>Title Text Color</label>
                        <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][title_text_color]" value="" data-alpha="true" />
                    </div>

                    <div class="cgs-style-item cgs-form-group">
                        <label>Body Text Color</label>
                        <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][text_color]" value="" data-alpha="true" />
                    </div>




                
                <div class="cgs-style-grid" style="margin-top: 15px;">
                    <div class="cgs-style-item cgs-form-group">
                        <label>Card Border Color</label>
                        <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][card_border_color]" value="" data-alpha="true" />
                    </div>
                    <div class="cgs-style-item cgs-form-group">
                        <label>Card Border Width(px)</label>
                        <input type="number" name="cgs_carousel_items[${idx}][card_border_width]" value="" style="width:60px;" />
                    </div>
                    <div class="cgs-style-item cgs-form-group">
                        <label>Card Border Style</label>
                        <select name="cgs_carousel_items[${idx}][card_border_style]" style="width: 100%;">
                            <option value="none">None</option>
                            <option value="solid">Solid</option>
                            <option value="dashed">Dashed</option>
                            <option value="dotted">Dotted</option>
                            <option value="double">Double</option>
                        </select>
                    </div>
                </div>

                ${typoHtml}

            </div>
          </div>
          <p>
            <button type="button" class="button cgs-remove-carousel-item">Remove Item</button>
          </p>
        </div>
      </li>`;
    }

    // Live Title Update (Strip Tags)
    $list.on("input", 'input[name*="[title]"]', function () {
      const val = $(this).val();
      // Strip tags for preview
      const cleanVal = val.replace(/<\/?[^>]+(>|$)/g, "") || `Item ${$(this).closest('.cgs-carousel-item').data('index') + 1}`;
      $(this).closest('.cgs-carousel-item').find('.cgs-card-collapsed-title').text(cleanVal);
    });

    initCollapse();

    // Add New Item — now opens by default
    $("#cgs-add-carousel-item").on("click", function (e) {
      e.preventDefault();
      const html = getItemHtml(itemIndex++);
      $list.prepend(html);

      // immediately expand the new item
      const $new = $list.find(".cgs-carousel-item").first();
      $new.find(".cgs-carousel-item-content").show();
      $new.find(".cgs-toggle-item").removeClass("collapsed").html("▼");

      // Init color picker for new fields
      if (typeof Pickr !== 'undefined') {
        $new.find('.cgs-color-picker').each(function () {
          initPickr($(this));
        });
      }
    });

    // Remove Item
    $list.on("click", ".cgs-remove-carousel-item", function (e) {
      e.preventDefault();
      $(this).closest(".cgs-carousel-item").remove();
    });

    // Toggle Collapse
    $list.on("click", ".cgs-toggle-item", function () {
      const $btn = $(this);
      const $item = $btn.closest(".cgs-carousel-item");
      const idx = $item.data("index");
      const collapsed = !$btn.hasClass("collapsed");

      $item.find(".cgs-carousel-item-content").slideToggle(150);
      $btn.toggleClass("collapsed", collapsed).html(collapsed ? "►" : "▼");
      toggleCollapse(idx, collapsed);
    });

    // Select Image
    $list.on("click", ".cgs-select-image", function (e) {
      e.preventDefault();
      const $container = $(this).closest(".cgs-carousel-item");
      const frame = wp.media({
        title: "Select Image",
        button: { text: "Use Image" },
        multiple: false,
      });
      frame.on("select", function () {
        const attachment = frame.state().get("selection").first().toJSON();
        $container.find(".cgs-carousel-image-id").val(attachment.id);
        $container
          .find(".cgs-carousel-image-preview")
          .attr("src", attachment.sizes.thumbnail.url)
          .show();
        $container.find(".cgs-remove-image").show();
      });
      frame.open();
    });

    // Remove Image
    $list.on("click", ".cgs-remove-image", function (e) {
      e.preventDefault();
      const $container = $(this).closest(".cgs-carousel-item");
      $container.find(".cgs-carousel-image-id").val("");
      $container.find(".cgs-carousel-image-preview").hide();
      $(this).hide();
    });

    // Select BG Image
    $list.on("click", ".cgs-select-bg-image", function (e) {
      e.preventDefault();
      const $container = $(this).closest(".cgs-carousel-item");
      const frame = wp.media({
        title: "Select Background Image",
        button: { text: "Use Image" },
        multiple: false,
      });
      frame.on("select", function () {
        const attachment = frame.state().get("selection").first().toJSON();
        $container.find(".cgs-bg-image-id").val(attachment.id);
        $container
          .find(".cgs-bg-image-preview")
          .attr("src", attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url)
          .show();
        $container.find(".cgs-remove-bg-image").show();
      });
      frame.open();
    });

    // Remove BG Image
    $list.on("click", ".cgs-remove-bg-image", function (e) {
      e.preventDefault();
      const $container = $(this).closest(".cgs-carousel-item");
      $container.find(".cgs-bg-image-id").val("");
      $container.find(".cgs-bg-image-preview").hide();
      $(this).hide();
    });

    // Mark as ready so CSS can show content
    $('body').addClass('js-cgs-ready');

  } catch (err) {
    console.error("CGS Admin JS error:", err);
  }
});
