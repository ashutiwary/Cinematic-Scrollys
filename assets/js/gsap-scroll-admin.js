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

    // Global Settings Tabs
    $('.cgs-settings-tab-link').on('click', function () {
      const tabId = $(this).data('tab');

      // Update active link
      $('.cgs-settings-tab-link').removeClass('active');
      $(this).addClass('active');

      // Update active content
      $('.cgs-settings-tab-content').removeClass('active');
      $('#' + tabId).addClass('active');
    });

    function toggleCollapse(idx, isCollapsed) {
      collapseState[idx] = isCollapsed;
      localStorage.setItem(storageKey, JSON.stringify(collapseState));
    }

function getItemHtml(idx) {
    const typoSections = [
        { slug: 'title', label: 'Title', showTag: true, showColor: true, colorField: 'title_text_color' },
        { slug: 'body', label: 'Body', showTag: true, showColor: true, colorField: 'text_color' },
        { slug: 'button', label: 'Button', showTag: false, showColor: false }
    ];

    // Build Font Options
    let fontOptions = '';
    if (typeof CGS_Admin_Data !== 'undefined' && CGS_Admin_Data.available_fonts) {
        for (const [val, label] of Object.entries(CGS_Admin_Data.available_fonts)) {
            fontOptions += `<option value="${val}">${label}</option>`;
        }
    } else {
        fontOptions = `<option value="">Default</option><option value="Arial, sans-serif">Arial</option>`;
    }

    let typoHtml = '';
    typoSections.forEach((sec, secIdx) => {
        const borderTop = secIdx > 0 ? 'border-top:1px dashed #eee; padding-top:15px; margin-top:15px;' : '';
        typoHtml += `
            <div style="${borderTop}">
                <strong style="display:block; margin-bottom:10px;">${sec.label}</strong>
                <div style="display:flex; gap:15px; margin-bottom:10px; flex-wrap:wrap;">
                    <div style="flex:2; min-width:150px;">
                        <label style="font-size:0.85em;">Font</label>
                        <select name="cgs_carousel_items[${idx}][${sec.slug}_font_family]" style="width:100%;">
                            ${fontOptions}
                        </select>
                    </div>
                    ${sec.showTag ? `
                    <div style="flex:1; min-width:80px;">
                        <label style="font-size:0.85em;">Tag</label>
                        <select name="cgs_carousel_items[${idx}][${sec.slug}_tag]" style="width:100%;">
                            <option value="">Default</option>
                            <option value="h1">H1</option><option value="h2">H2</option><option value="h3">H3</option>
                            <option value="h4">H4</option><option value="h5">H5</option><option value="h6">H6</option>
                            <option value="p">P</option><option value="div">Div</option><option value="span">Span</option>
                        </select>
                    </div>` : ''}
                    ${sec.showColor ? `
                    <div style="flex:1; min-width:60px;">
                        <label style="font-size:0.85em;">Color</label>
                        <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][${sec.colorField}]" value="" data-alpha="true">
                    </div>` : ''}
                </div>
                <div style="display:flex; flex-wrap:wrap; gap:10px;">
                    <div style="flex:1; min-width:60px;">
                        <label style="font-size:0.85em;">Size</label>
                        <input type="text" name="cgs_carousel_items[${idx}][${sec.slug}_font_size]" value="" placeholder="16px" style="width:100%;">
                    </div>
                    <div style="flex:1; min-width:70px;">
                        <label style="font-size:0.85em;">Weight</label>
                        <select name="cgs_carousel_items[${idx}][${sec.slug}_font_weight]" style="width:100%;">
                            <option value="">-</option><option value="normal">Normal</option><option value="bold">Bold</option>
                            <option value="100">100</option><option value="200">200</option><option value="300">300</option>
                            <option value="400">400</option><option value="500">500</option><option value="600">600</option>
                            <option value="700">700</option><option value="800">800</option><option value="900">900</option>
                        </select>
                    </div>
                    <div style="flex:1; min-width:70px;">
                        <label style="font-size:0.85em;">Style</label>
                        <select name="cgs_carousel_items[${idx}][${sec.slug}_font_style]" style="width:100%;">
                            <option value="">-</option><option value="normal">Normal</option><option value="italic">Italic</option>
                        </select>
                    </div>
                    <div style="flex:1; min-width:60px;">
                        <label style="font-size:0.85em;">Line Ht</label>
                        <input type="text" name="cgs_carousel_items[${idx}][${sec.slug}_line_height]" value="" placeholder="1.5" style="width:100%;">
                    </div>
                    <div style="flex:1; min-width:80px;">
                        <label style="font-size:0.85em;">Transform</label>
                        <select name="cgs_carousel_items[${idx}][${sec.slug}_text_transform]" style="width:100%;">
                            <option value="">-</option><option value="none">None</option><option value="uppercase">Upper</option>
                            <option value="lowercase">Lower</option><option value="capitalize">Cap</option>
                        </select>
                    </div>
                    <div style="flex:1; min-width:60px;">
                        <label style="font-size:0.85em;">Spacing</label>
                        <input type="text" name="cgs_carousel_items[${idx}][${sec.slug}_letter_spacing]" value="" placeholder="0px" style="width:100%;">
                    </div>
                </div>
            </div>`;
    });

    const imgPosHtml = layout === "horizontal"
        ? `<select name="cgs_carousel_items[${idx}][img_position]" class="widefat">
             <option value="" selected>Default (Global)</option>
             <option value="left">Left</option>
             <option value="right">Right</option>
           </select>`
        : `<select name="cgs_carousel_items[${idx}][ver_img_position]" class="widefat">
             <option value="" selected>Default (Global)</option>
             <option value="left">Left</option>
             <option value="right">Right</option>
           </select>`;

    return `
      <li class="cgs-carousel-item" data-index="${idx}">
        <div class="cgs-card-header">
          <span class="cgs-drag-handle" title="Drag to reorder">☰</span>
          <span class="cgs-card-collapsed-title">Item ${idx + 1}</span>
          <button type="button" class="cgs-duplicate-item" title="Duplicate">⧉</button>
          <button type="button" class="cgs-toggle-item">▼</button>
        </div>
        <div class="cgs-carousel-item-content">
          <!-- TOP SECTION -->
          <div class="cgs-card-top-section">
            <div class="cgs-form-group">
              <label>Title</label>
              <input type="text" name="cgs_carousel_items[${idx}][title]" class="widefat">
            </div>
            <div class="cgs-form-group">
              <label>Body Text</label>
              <textarea name="cgs_carousel_items[${idx}][body]" class="widefat" rows="4"></textarea>
            </div>
            <div class="cgs-card-image-row">
              <div class="cgs-image-box">
                <label>Card Image</label>
                <div style="display:flex; align-items:center; gap:10px;">
                  <div style="width:80px; height:80px; background:#f0f0f1; display:flex; align-items:center; justify-content:center; border-radius:4px; overflow:hidden;">
                    <img class="cgs-carousel-image-preview" style="max-width:100%; max-height:100%; display:none;">
                    <span class="dashicons dashicons-format-image" style="font-size:30px; height:30px; width:30px; color:#ccc;"></span>
                  </div>
                  <div>
                    <input type="hidden" name="cgs_carousel_items[${idx}][image]" class="cgs-carousel-image-id">
                    <button type="button" class="button cgs-select-image">Select</button>
                    <button type="button" class="button cgs-remove-image" style="color:#b32d2e; border-color:#b32d2e; display:none;">Remove</button>
                  </div>
                </div>
              </div>
              <div class="cgs-image-box">
                <label>Background Image</label>
                <div style="display:flex; align-items:center; gap:10px;">
                  <div style="width:80px; height:80px; background:#f0f0f1; display:flex; align-items:center; justify-content:center; border-radius:4px; overflow:hidden;">
                    <img class="cgs-bg-image-preview" style="max-width:100%; max-height:100%; display:none;">
                    <span class="dashicons dashicons-format-image" style="font-size:30px; height:30px; width:30px; color:#ccc;"></span>
                  </div>
                  <div>
                    <input type="hidden" name="cgs_carousel_items[${idx}][card_bg_image]" class="cgs-bg-image-id">
                    <button type="button" class="button cgs-select-bg-image">Select</button>
                    <button type="button" class="button cgs-remove-bg-image" style="color:#b32d2e; border-color:#b32d2e; display:none;">Remove</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="cgs-card-button-row">
              <div class="cgs-form-group" style="margin-bottom:0;">
                <label>Button Text</label>
                <input type="text" name="cgs_carousel_items[${idx}][button_text]" class="widefat">
              </div>
              <div class="cgs-form-group" style="margin-bottom:0;">
                <label>Button URL</label>
                <input type="text" name="cgs_carousel_items[${idx}][button_url]" class="widefat">
              </div>
              <div class="cgs-form-group" style="margin-bottom:0;">
                <label>Relation</label>
                <input type="text" name="cgs_carousel_items[${idx}][btn_rel]" class="widefat" placeholder="noopener">
              </div>
              <div class="cgs-form-group" style="margin-bottom:0;">
                <label>Target</label>
                <select name="cgs_carousel_items[${idx}][btn_target]" class="widefat">
                  <option value="_blank">New Tab</option>
                  <option value="_self">Same Tab</option>
                </select>
              </div>
            </div>
          </div>

          <!-- TAB NAV -->
          <div class="cgs-card-tabs-nav">
            <button type="button" class="cgs-card-tab-link active" data-tab="button">Button</button>
            <button type="button" class="cgs-card-tab-link" data-tab="card">Card</button>
            <button type="button" class="cgs-card-tab-link" data-tab="media">Media</button>
            <button type="button" class="cgs-card-tab-link" data-tab="typography">Typography</button>
          </div>

          <!-- TAB PANELS -->
          <div class="cgs-card-tabs-content">
            <!-- BUTTON TAB -->
            <div class="cgs-card-tab-panel active" data-panel="button">
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:15px;">
                <div class="cgs-form-group" style="margin-bottom:0;">
                  <label>Icon Class</label>
                  <input type="text" name="cgs_carousel_items[${idx}][btn_icon_class]" class="widefat" placeholder="fas fa-arrow-right">
                </div>
                <div class="cgs-form-group" style="margin-bottom:0;">
                  <label>Icon Position</label>
                  <select name="cgs_carousel_items[${idx}][btn_icon_pos]" class="widefat">
                    <option value="right">Right</option>
                    <option value="left">Left</option>
                  </select>
                </div>
              </div>
              <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px; margin-bottom:15px;">
                <div class="cgs-form-group" style="margin-bottom:0;">
                  <label>Background</label>
                  <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_bg_color]" data-alpha="true">
                </div>
                <div class="cgs-form-group" style="margin-bottom:0;">
                  <label>Text Color</label>
                  <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_text_color]" data-alpha="true">
                </div>
                <div class="cgs-form-group" style="margin-bottom:0;">
                  <label>Width</label>
                  <input type="text" name="cgs_carousel_items[${idx}][btn_width]" placeholder="e.g. 200px" class="widefat">
                </div>
              </div>
              <div style="border-top:1px dashed #eee; padding-top:15px; margin-bottom:15px;">
                <strong style="display:block; margin-bottom:10px;">Dimensions</strong>
                <div style="display:flex; flex-wrap:wrap; gap:20px;">
                  <div>
                    <span style="font-size:0.85em; display:block; margin-bottom:5px;">Padding (px)</span>
                    <div style="display:flex; gap:5px;">
                      <label style="font-size:0.85em;">T <input type="number" name="cgs_carousel_items[${idx}][btn_padding_top]" style="width:40px;"></label>
                      <label style="font-size:0.85em;">R <input type="number" name="cgs_carousel_items[${idx}][btn_padding_right]" style="width:40px;"></label>
                      <label style="font-size:0.85em;">B <input type="number" name="cgs_carousel_items[${idx}][btn_padding_bottom]" style="width:40px;"></label>
                      <label style="font-size:0.85em;">L <input type="number" name="cgs_carousel_items[${idx}][btn_padding_left]" style="width:40px;"></label>
                    </div>
                  </div>
                  <div>
                    <span style="font-size:0.85em; display:block; margin-bottom:5px;">Radius (px)</span>
                    <div style="display:flex; gap:5px;">
                      <label style="font-size:0.85em;">TL <input type="number" name="cgs_carousel_items[${idx}][btn_radius_top_lt]" style="width:40px;"></label>
                      <label style="font-size:0.85em;">TR <input type="number" name="cgs_carousel_items[${idx}][btn_radius_top_rt]" style="width:40px;"></label>
                      <label style="font-size:0.85em;">BR <input type="number" name="cgs_carousel_items[${idx}][btn_radius_btm_rt]" style="width:40px;"></label>
                      <label style="font-size:0.85em;">BL <input type="number" name="cgs_carousel_items[${idx}][btn_radius_btm_lt]" style="width:40px;"></label>
                    </div>
                  </div>
                </div>
              </div>
              <div style="border-top:1px dashed #eee; padding-top:15px; margin-bottom:15px;">
                <div style="display:flex; flex-wrap:wrap; gap:20px;">
                  <div style="flex:1; min-width:200px;">
                    <strong style="font-size:0.9em; display:block; margin-bottom:5px;">Border</strong>
                    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                      <label style="font-size:0.85em; margin:0;">W <input type="number" name="cgs_carousel_items[${idx}][btn_border_width]" style="width:45px;"></label>
                      <select name="cgs_carousel_items[${idx}][btn_border_style]" style="width:70px;">
                        <option value="">-</option><option value="none">None</option><option value="solid">Solid</option><option value="dashed">Dashed</option>
                      </select>
                      <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_border_color]" data-alpha="true">
                    </div>
                  </div>
                  <div style="flex:1; min-width:250px;">
                    <strong style="font-size:0.9em; display:block; margin-bottom:5px;">Effects</strong>
                    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                      <label style="font-size:0.85em; margin:0;">Shadow <input type="text" name="cgs_carousel_items[${idx}][btn_shadow]" placeholder="0 4px 10px rgba(0,0,0,0.2)" style="width:180px;"></label>
                      <label style="font-size:0.85em; margin:0;">Scale <input type="number" step="0.01" name="cgs_carousel_items[${idx}][btn_scale]" placeholder="1" style="width:50px;"></label>
                      <label style="font-size:0.85em; margin:0;">Lift <input type="number" name="cgs_carousel_items[${idx}][btn_lift]" placeholder="0" style="width:45px;"></label>
                    </div>
                  </div>
                </div>
              </div>
              <div style="border-top:1px dashed #eee; padding-top:15px;">
                <strong style="display:block; margin-bottom:10px;">Hover State</strong>
                <div style="display:flex; gap:15px; flex-wrap:wrap; align-items:center;">
                  <div style="display:flex; align-items:center; gap:5px;">
                    <label style="font-size:0.85em; margin:0;">BG</label>
                    <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_hover_bg]" data-alpha="true">
                  </div>
                  <div style="display:flex; align-items:center; gap:5px;">
                    <label style="font-size:0.85em; margin:0;">Text</label>
                    <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_hover_text]" data-alpha="true">
                  </div>
                  <div style="display:flex; align-items:center; gap:5px;">
                    <label style="font-size:0.85em; margin:0;">Border</label>
                    <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][btn_hover_border_color]" data-alpha="true">
                  </div>
                  <label style="font-size:0.85em; margin:0;">Shadow <input type="text" name="cgs_carousel_items[${idx}][btn_hover_shadow]" placeholder="0 8px 25px rgba(0,0,0,0.2)" style="width:180px;"></label>
                  <label style="font-size:0.85em; margin:0;">Scale <input type="number" step="0.01" name="cgs_carousel_items[${idx}][btn_hover_scale]" placeholder="1.05" style="width:55px;"></label>
                  <label style="font-size:0.85em; margin:0;">Lift <input type="number" name="cgs_carousel_items[${idx}][btn_hover_lift]" placeholder="5" style="width:45px;"></label>
                </div>
              </div>
            </div>

            <!-- CARD TAB -->
            <div class="cgs-card-tab-panel" data-panel="card">
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <div class="cgs-form-group" style="margin-bottom:0;">
                  <label>Card Inside Color</label>
                  <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][card_bg_color]" data-alpha="true">
                </div>
                <div class="cgs-form-group" style="margin-bottom:0;">
                  <label>Card Outside Color</label>
                  <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][wrapper_bg_color]" data-alpha="true">
                </div>
              </div>
              <div style="border-top:1px dashed #eee; padding-top:15px;">
                <strong style="display:block; margin-bottom:10px;">Card Border</strong>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px;">
                  <div class="cgs-form-group" style="margin-bottom:0;">
                    <label>Color</label>
                    <input type="text" class="cgs-color-picker" name="cgs_carousel_items[${idx}][card_border_color]" data-alpha="true">
                  </div>
                  <div class="cgs-form-group" style="margin-bottom:0;">
                    <label>Width (px)</label>
                    <input type="number" name="cgs_carousel_items[${idx}][card_border_width]" class="widefat">
                  </div>
                  <div class="cgs-form-group" style="margin-bottom:0;">
                    <label>Style</label>
                    <select name="cgs_carousel_items[${idx}][card_border_style]" class="widefat">
                      <option value="none">None</option>
                      <option value="solid">Solid</option>
                      <option value="dashed">Dashed</option>
                      <option value="dotted">Dotted</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- MEDIA TAB -->
            <div class="cgs-card-tab-panel" data-panel="media">
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="cgs-form-group" style="margin-bottom:0;">
                  <label>Image Position</label>
                  ${imgPosHtml}
                </div>
                <div class="cgs-form-group" style="margin-bottom:0;">
                  <label>Image Border Radius (px)</label>
                  <input type="number" name="cgs_carousel_items[${idx}][img_border_radius]" class="widefat" placeholder="0">
                </div>
              </div>
            </div>

            <!-- TYPOGRAPHY TAB -->
            <div class="cgs-card-tab-panel" data-panel="typography">
              ${typoHtml}
            </div>

          </div><!-- .cgs-card-tabs-content -->

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

    // Initialize drag-and-drop sortable for card reordering
    $list.sortable({
      handle: ".cgs-drag-handle",
      placeholder: "cgs-sortable-placeholder",
      cursor: "grabbing",
      opacity: 0.7,
      tolerance: "pointer",
      start: function (e, ui) {
        ui.placeholder.height(ui.item.outerHeight());
      }
    });

    // Tab switching for card editor tabs
    $list.on("click", ".cgs-card-tab-link", function (e) {
      e.preventDefault();
      const $this = $(this);
      const tabName = $this.data("tab");
      const $card = $this.closest(".cgs-carousel-item");

      // Update tab nav active state
      $card.find(".cgs-card-tab-link").removeClass("active");
      $this.addClass("active");

      // Show/hide tab panels
      $card.find(".cgs-card-tab-panel").removeClass("active");
      $card.find('.cgs-card-tab-panel[data-panel="' + tabName + '"]').addClass("active");
    });

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

    // Duplicate Item
    $list.on("click", ".cgs-duplicate-item", function (e) {
      e.preventDefault();
      const $original = $(this).closest(".cgs-carousel-item");
      const newIdx = itemIndex++;

      // Clone the item
      const $clone = $original.clone(true);

      // Update the data-index attribute
      $clone.attr("data-index", newIdx);

      // Update all input/select/textarea names with new index
      $clone.find("input, select, textarea").each(function () {
        const name = $(this).attr("name");
        if (name) {
          const newName = name.replace(/\[\d+\]/, `[${newIdx}]`);
          $(this).attr("name", newName);
        }
        // Clear pickr initialization flag for color pickers
        if ($(this).hasClass("cgs-color-picker")) {
          $(this).removeData("pickr-init");
          $(this).show();
          // Remove the cloned pickr container (will be re-initialized)
          $(this).next("div").remove();
        }
      });

      // Update the title display
      const originalTitle = $original.find('input[name*="[title]"]').val();
      $clone.find(".cgs-card-collapsed-title").text(
        originalTitle ? originalTitle + " (Copy)" : `Item ${newIdx + 1}`
      );

      // Expand the cloned item
      $clone.find(".cgs-carousel-item-content").show();
      $clone.find(".cgs-toggle-item").removeClass("collapsed").html("▼");

      // Insert the clone BEFORE the original (above it)
      $clone.insertBefore($original);

      // Re-initialize color pickers on the cloned item
      if (typeof Pickr !== "undefined") {
        $clone.find(".cgs-color-picker").each(function () {
          initPickr($(this));
        });
      }
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
