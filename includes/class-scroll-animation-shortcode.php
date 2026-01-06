<?php
if (! defined('ABSPATH')) exit;

/**
 * Cinematic Scroll Shortcode Class
 */
if (! class_exists('Cinematic_Scroll_Shortcode')) {
  class Cinematic_Scroll_Shortcode
  {

    public function __construct()
    {
      add_shortcode('cinematic_scroll', [$this, 'render_shortcode']);
    }


    public function render_shortcode($atts)
    {

      $atts    = shortcode_atts(['id' => 0], $atts, 'cinematic_scroll');
      $post_id = absint($atts['id']);
      if (! $post_id) return '';

      // fetch & reverse items
      $items = get_post_meta($post_id, '_cgs_carousel_items', true);
      if (! is_array($items) || empty($items)) return '';
      $items = array_reverse(array_values($items));

      // Enqueue Scripts & Styles (Loaded from local assets/lib for privacy/performance)
      wp_enqueue_style('cgs-carousel');
      wp_enqueue_style('cgs-font-awesome');
      wp_enqueue_script('gsap');
      wp_enqueue_script('gsap-scrolltrigger');
      wp_enqueue_script('cgs-carousel-js', CGS_URL . 'assets/js/gsap-scroll-carousel.js', ['jquery', 'gsap', 'gsap-scrolltrigger'], '1.3.1', true);

      $layout = get_post_meta($post_id, '_cgs_layout', true);
      // pull style meta option for horizontal (with defaults)
      $card_bg = get_post_meta($post_id, '_cgs_card_bg',     true) ?: 'transparent';
      $title_txt_colr = get_post_meta($post_id, '_cgs_title_colr', true) ?: '#000000';
      $card_txt = get_post_meta($post_id, '_cgs_txt_colr',    true) ?: '#333333';
      $btn_bg  = get_post_meta($post_id, '_cgs_button_bg',   true) ?: '#0073aa';
      $btn_txt = get_post_meta($post_id, '_cgs_button_text', true) ?: '#ffffff';
      // Border values
      $card_border_clr = get_post_meta($post_id, '_cgs_card_border_clr', true) ?: '';
      $card_border_width = get_post_meta($post_id, '_cgs_card_border_width', true) ?: '0';
      $card_border_style = get_post_meta($post_id, '_cgs_card_border_style', true) ?: 'none';
      // Radius values
      $r_tl    = get_post_meta($post_id, '_cgs_card_radius_top_lt', true) ?: '0';
      $r_tr    = get_post_meta($post_id, '_cgs_card_radius_top_rt', true) ?: '0';
      $r_br    = get_post_meta($post_id, '_cgs_card_radius_btm_rt', true) ?: '0';
      $r_bl    = get_post_meta($post_id, '_cgs_card_radius_btm_lt', true) ?: '0';
      // Image radius and padding
      $img_tl  = get_post_meta($post_id, '_cgs_img_radius_top_lt', true) ?: '0';
      $img_tr  = get_post_meta($post_id, '_cgs_img_radius_top_rt', true) ?: '0';
      $img_br  = get_post_meta($post_id, '_cgs_img_radius_btm_rt', true) ?: '0';
      $img_bl  = get_post_meta($post_id, '_cgs_img_radius_btm_lt', true) ?: '0';
      // Padding values
      $padding_top  = get_post_meta($post_id, '_cgs_padding_top', true) ?: '0';
      $padding_right = get_post_meta($post_id, '_cgs_padding_right', true) ?: '0';
      $padding_bottom = get_post_meta($post_id, '_cgs_padding_bottom', true) ?: '0';
      $padding_left = get_post_meta($post_id, '_cgs_padding_left', true) ?: '0';
      // Card shadow and image position
      $shd     = get_post_meta($post_id, '_cgs_card_shadow', true) ?: '';
      $img_position = get_post_meta($post_id, '_cgs_img_position', true) ?: 'left';
      $ver_img_position = get_post_meta($post_id, '_cgs_vert_img_position', true) ?: 'right';
      $content_width = get_post_meta($post_id, '_cgs_content_width', true) ?: '1200px';

      // --- GOOGLE FONTS LOADING ---
      // We need to collect all fonts usage to build the request
      $fonts_to_load = [];
      
      // Helper to check if Google Font
      $all_google_fonts = [
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
      // Flip for fast lookup
      $google_fonts_lookup = array_flip($all_google_fonts);

      // Check Global Settings
      foreach(['title', 'body', 'button'] as $slug) {
          $f_val = get_post_meta($post_id, "_cgs_{$slug}_font_family", true);
          if ($f_val) {
             // Extract Name: "'Roboto', sans-serif" -> "Roboto"
             $parts = explode(',', $f_val);
             $fname = trim(str_replace(["'", '"'], '', $parts[0]));
             if(isset($google_fonts_lookup[$fname])) {
                 $fonts_to_load[$fname] = true;
             }
          }
      }

      // Enqueue FontAwesome

      // Check Card Settings
      foreach($items as $itm) {
          foreach(['title', 'body', 'button'] as $slug) {
              if ( !empty($itm["{$slug}_font_family"]) ) {
                  $f_val = $itm["{$slug}_font_family"];
                   $parts = explode(',', $f_val);
                   $fname = trim(str_replace(["'", '"'], '', $parts[0]));
                   if(isset($google_fonts_lookup[$fname])) {
                       $fonts_to_load[$fname] = true;
                   }
              }
          }
      }

      if ( !empty($fonts_to_load) ) {
          // Build URL: family=Roboto:wght@100;400;700&family=Open+Sans...
          // We load weights 300,400,500,600,700 to be safe
          $gfont_url = "https://fonts.googleapis.com/css2?display=swap";
          foreach(array_keys($fonts_to_load) as $fname) {
              $safe_name = urlencode($fname);
              $gfont_url .= "&family={$safe_name}:wght@300;400;500;600;700";
          }
          // Enqueue or output style. Since this is shortcode, outputting link is easier
          echo "<link rel='stylesheet' href='" . esc_url($gfont_url) . "'>";
      }

      // --- PER-CARD CSS GENERATION (Hover & Specifics) ---
      $per_card_css = "";
      foreach($items as $idx => $itm) {
          $h_bg = isset($itm['btn_hover_bg']) ? $itm['btn_hover_bg'] : '';
          $h_tx = isset($itm['btn_hover_text']) ? $itm['btn_hover_text'] : '';
          $h_bd = isset($itm['btn_hover_border_color']) ? $itm['btn_hover_border_color'] : '';
          $h_sh = isset($itm['btn_hover_shadow']) ? $itm['btn_hover_shadow'] : '';
          $h_sc = isset($itm['btn_hover_scale']) ? $itm['btn_hover_scale'] : '';
          $h_li = isset($itm['btn_hover_lift']) ? $itm['btn_hover_lift'] : '';
          
          if($h_bg || $h_tx || $h_bd || $h_sh || $h_sc || $h_li) {
              $rule = "";
              if($h_bg) $rule .= "background-color:{$h_bg} !important;";
              if($h_tx) $rule .= "color:{$h_tx} !important;";
              if($h_bd) $rule .= "border-color:{$h_bd} !important;";
              if($h_sh) $rule .= "box-shadow:{$h_sh} !important;";
              
              $b_sc = isset($itm['btn_scale']) && $itm['btn_scale']!=='' ? $itm['btn_scale'] : $g_btn_scale;
              $b_li = isset($itm['btn_lift']) && $itm['btn_lift']!=='' ? $itm['btn_lift'] : $g_btn_lift;
              
              $f_sc = $h_sc !== '' ? $h_sc : $b_sc;
              $f_li = $h_li !== '' ? $h_li : $b_li;
              
              $trans = [];
              if($f_sc !== '') $trans[] = "scale({$f_sc})";
              if($f_li !== '') $trans[] = "translateY(-{$f_li}px)";
              
              if(!empty($trans)) {
                  $rule .= "transform: " . implode(' ', $trans) . " !important;";
              }
              
              if($rule) {
                  $per_card_css .= "#{$cid} .cgs-btn-{$idx}:hover { {$rule} } ";
              }
          }
      }

      // --- GLOBAL TYPOGRAPHY CSS CONSTRUCTION ---
      // Use classes instead of tags for styling validity across dynamic tags
      $typo_elements = [
          'title'  => ['selector' => ".cgs-card-title", 'props' => []],
          'body'   => ['selector' => ".cgs-card-body",  'props' => []],
          'button' => ['selector' => ".button", 'props' => []]
      ];
      // CSS Property Map
      $prop_map = [
          'font_family' => 'font-family', 'font_size' => 'font-size', 'font_weight' => 'font-weight',
          'font_style' => 'font-style', 'line_height' => 'line-height', 'text_transform' => 'text-transform', 'letter_spacing' => 'letter-spacing'
      ];
      
      $global_typo_css = "";
      
      foreach ($typo_elements as $slug => $data) {
          $inner_css = "";
          foreach ($prop_map as $meta_suffix => $css_prop) {
             $val = get_post_meta($post_id, "_cgs_{$slug}_{$meta_suffix}", true);
             if (!empty($val)) {
                 $inner_css .= "{$css_prop}: {$val}; ";
             }
          }
           // Store for later use if needed, but mainly for generating CSS string
          $global_typo_css .= "#cgs-carousel-" . esc_attr($post_id) . " .cgs-card-inner {$data['selector']}, .cgs-stack-card {$data['selector']} { {$inner_css} } ";
      }

      // --- PER-CARD CSS GENERATION (Hover & Specifics) ---
      $per_card_css = "";
      foreach($items as $idx => $itm) {
          // Check if we need custom hover CSS
          // We look for any Hover field set, OR if Normal Scale/Lift/Border set (to ensure transition/transform usage matches)
          // Actually, inline style handles Normal State. We only need CSS block for :hover state or if we want to use classes.
          // Since we can't do :hover inline, we MUST do it here.
          
          $h_bg = isset($itm['btn_hover_bg']) ? $itm['btn_hover_bg'] : '';
          $h_tx = isset($itm['btn_hover_text']) ? $itm['btn_hover_text'] : '';
          $h_bd = isset($itm['btn_hover_border_color']) ? $itm['btn_hover_border_color'] : '';
          $h_sh = isset($itm['btn_hover_shadow']) ? $itm['btn_hover_shadow'] : '';
          $h_sc = isset($itm['btn_hover_scale']) ? $itm['btn_hover_scale'] : '';
          $h_li = isset($itm['btn_hover_lift']) ? $itm['btn_hover_lift'] : '';
          
          // Check Global Hover Defaults if per-card is empty?
          // Usually per-card overrides global. If per-card is empty, it falls back to global CSS we already generated.
          // BUT, if we have a specific class, does it override global ID selector?
          // Specificity: `#cid .cgs-btn-0:hover` vs `#cid .button:hover`.
          // Class + ID is higher specificty. So if we generate a rule, it overrides.
          // If we DON'T generate a rule, it uses global.
          // So only generate if at least ONE hover prop is set on the card ?
          // OR if we need to override global?
          // If Card has NO settings, we shouldn't generate CSS, let global apply.
          // If Card HAS settings, we generate.
          
          if($h_bg || $h_tx || $h_bd || $h_sh || $h_sc || $h_li) {
              $rule = "";
              if($h_bg) $rule .= "background-color:{$h_bg} !important;";
              if($h_tx) $rule .= "color:{$h_tx} !important;";
              if($h_bd) $rule .= "border-color:{$h_bd} !important;";
              if($h_sh) $rule .= "box-shadow:{$h_sh} !important;";
              
              // Transform Logic
              // We need Base transform for this card to calculate Hover transform correctly?
              // Base transform is inline.
              // CSS :hover transform REPLACES base transform.
              // So we need to know the Base Scale/Lift for THIS card to fallback if hover is missing specific parts.
              $b_sc = isset($itm['btn_scale']) && $itm['btn_scale']!=='' ? $itm['btn_scale'] : $g_btn_scale;
              $b_li = isset($itm['btn_lift']) && $itm['btn_lift']!=='' ? $itm['btn_lift'] : $g_btn_lift;
              
              // Final Hover Scale/Lift
              $f_sc = $h_sc !== '' ? $h_sc : $b_sc;
              $f_li = $h_li !== '' ? $h_li : $b_li;
              
              $trans = [];
              if($f_sc !== '') $trans[] = "scale({$f_sc})";
              if($f_li !== '') $trans[] = "translateY(-{$f_li}px)";
              
              if(!empty($trans)) {
                  $rule .= "transform: " . implode(' ', $trans) . " !important;";
              }
              
              if($rule) {
                  $per_card_css .= "#{$cid} .cgs-btn-{$idx}:hover { {$rule} } ";
              }
          }
      }

      // Fetch Global Button Styles

      $bp_t = get_post_meta($post_id, '_cgs_btn_padding_top', true);
      $bp_r = get_post_meta($post_id, '_cgs_btn_padding_right', true);
      $bp_b = get_post_meta($post_id, '_cgs_btn_padding_bottom', true);
      $bp_l = get_post_meta($post_id, '_cgs_btn_padding_left', true);
      $br_tl = get_post_meta($post_id, '_cgs_btn_radius_top_lt', true);
      $br_tr = get_post_meta($post_id, '_cgs_btn_radius_top_rt', true);
      $br_br = get_post_meta($post_id, '_cgs_btn_radius_btm_rt', true);
      $br_bl = get_post_meta($post_id, '_cgs_btn_radius_btm_lt', true);
      $btn_w = get_post_meta($post_id, '_cgs_btn_width', true);
      
      // -- Global Button Normal State Extras --
      $g_btn_border_w = get_post_meta($post_id, '_cgs_btn_border_width', true);
      $g_btn_border_s = get_post_meta($post_id, '_cgs_btn_border_style', true);
      $g_btn_border_c = get_post_meta($post_id, '_cgs_btn_border_color', true);
      
      $g_btn_shadow = get_post_meta($post_id, '_cgs_btn_shadow', true);
      $g_btn_scale  = get_post_meta($post_id, '_cgs_btn_scale', true);
      $g_btn_lift   = get_post_meta($post_id, '_cgs_btn_lift', true);

      // -- Global Button Hover State --
      $g_btn_h_bg    = get_post_meta($post_id, '_cgs_btn_hover_bg', true);
      $g_btn_h_text  = get_post_meta($post_id, '_cgs_btn_hover_text', true);
      $g_btn_h_border = get_post_meta($post_id, '_cgs_btn_hover_border_color', true);
      $g_btn_h_shadow = get_post_meta($post_id, '_cgs_btn_hover_shadow', true);
      $g_btn_h_scale  = get_post_meta($post_id, '_cgs_btn_hover_scale', true);
      $g_btn_h_lift   = get_post_meta($post_id, '_cgs_btn_hover_lift', true);
      
      $btn_css_global = "transition: all 0.3s ease;"; // Always add transition
      if($bp_t!=='') $btn_css_global .= "padding-top:{$bp_t}px;";
      if($bp_r!=='') $btn_css_global .= "padding-right:{$bp_r}px;";
      if($bp_b!=='') $btn_css_global .= "padding-bottom:{$bp_b}px;";
      if($bp_l!=='') $btn_css_global .= "padding-left:{$bp_l}px;";
      if($br_tl!=='') $btn_css_global .= "border-top-left-radius:{$br_tl}px;";
      if($br_tr!=='') $btn_css_global .= "border-top-right-radius:{$br_tr}px;";
      if($br_br!=='') $btn_css_global .= "border-bottom-right-radius:{$br_br}px;";
      if($br_bl!=='') $btn_css_global .= "border-bottom-left-radius:{$br_bl}px;";
      if($btn_w!=='') $btn_css_global .= "width:{$btn_w}; display:inline-block; text-align:center;";
      
      // Global Border
      if($g_btn_border_w!=='') $btn_css_global .= "border-width:{$g_btn_border_w}px;";
      if($g_btn_border_s!=='') $btn_css_global .= "border-style:{$g_btn_border_s};";
      if($g_btn_border_c!=='') $btn_css_global .= "border-color:{$g_btn_border_c};";

      // Global Normal Effects
      if($g_btn_shadow!=='') $btn_css_global .= "box-shadow:{$g_btn_shadow};";
      
      // Global Normal Transform
      $g_trans_ops = [];
      if($g_btn_scale!=='') $g_trans_ops[] = "scale({$g_btn_scale})";
      if($g_btn_lift!=='')  $g_trans_ops[] = "translateY(-{$g_btn_lift}px)";
      if(!empty($g_trans_ops)) {
          $btn_css_global .= "transform: " . implode(' ', $g_trans_ops) . ";";
      }

      $cid  = 'cgs-carousel-' . esc_attr($post_id);

      // -- Construct Global Hover CSS Rule --
      $g_hover_css_inner = "";
      if($g_btn_h_bg!=='')   $g_hover_css_inner .= "background-color:{$g_btn_h_bg} !important;";
      if($g_btn_h_text!=='') $g_hover_css_inner .= "color:{$g_btn_h_text} !important;";
      if($g_btn_h_border!=='') $g_hover_css_inner .= "border-color:{$g_btn_h_border} !important;";
      if($g_btn_h_shadow!=='') $g_hover_css_inner .= "box-shadow:{$g_btn_h_shadow} !important;";
      
      $g_h_trans_ops = [];
      // Logic: For hover scale/lift, if not set, should we inherit normal? 
      // Usually CSS doesn't partial-update. If we set transform here, we lose Normal transform.
      // So if Hover Scale is NOT set, use Normal Scale.
      $gl_s = $g_btn_h_scale !== '' ? $g_btn_h_scale : $g_btn_scale;
      $gl_l = $g_btn_h_lift !== '' ? $g_btn_h_lift : $g_btn_lift;
      
      if($gl_s !== '') $g_h_trans_ops[] = "scale({$gl_s})";
      if($gl_l !== '') $g_h_trans_ops[] = "translateY(-{$gl_l}px)";
      
      if(!empty($g_h_trans_ops)) {
           $g_hover_css_inner .= "transform: " . implode(' ', $g_h_trans_ops) . " !important;";
      }
      
      $extra_global_css = "";
      if($g_hover_css_inner !== "") {
          $extra_global_css .= "#{$cid} .cgs-card-inner .button:hover, .cgs-stack-card .button:hover { {$g_hover_css_inner} }";
      }

      $css  =
        "#{$cid} .cgs-card-inner, .cgs-stack-card{background:{$card_bg};color:{$card_txt};"
        . "border-radius:{$r_tl}px {$r_tr}px {$r_br}px {$r_bl}px;box-shadow:{$shd};"
        . "border:{$card_border_width}px {$card_border_style} {$card_border_clr};"
        . "padding:{$padding_top}px {$padding_right}px {$padding_bottom}px {$padding_left}px;"
        . "width: 100%; max-width: {$content_width};}"
        . "#{$cid} .cgs-card-inner .button,.cgs-stack-card .button{background-color:{$btn_bg};color:{$btn_txt};{$btn_css_global}}"
        . "#{$cid} .cgs-card-inner .cgs-card-title,.cgs-stack-card .cgs-card-title{color:{$title_txt_colr};}" 
        . "#{$cid} .cgs-card-inner img, .cgs-stack-card img{border-radius:{$img_tl}px {$img_tr}px {$img_br}px {$img_bl}px;}"
        . $global_typo_css // Append global typography
        . $extra_global_css // Append Global Hover
        . $per_card_css; // Append Per Card Hover

      ob_start();
      echo "<style scoped>{$css}</style>";
?>
      <div class="cgs-carousel-outer" data-layout="<?php echo esc_attr($layout); ?>">
        <!-- frontend design for horizontal carousel -->
        <?php if ('horizontal' === $layout) { ?>
          <div class="cgs-carousel-wrapper" id="<?php echo esc_attr($cid); ?>">
            <div class="cgs-carousel">
              <?php foreach ($items as $i => $item) :
                $img = ! empty($item['image']) ? wp_get_attachment_image_url($item['image'], 'large') : '';
                // Get each card style
                $bg_color   = ! empty($item['card_bg_color']) ? $item['card_bg_color'] : '';
                $text_color = ! empty($item['text_color']) ? $item['text_color'] : '';
                $btn_bg_color = ! empty($item['btn_bg_color']) ? $item['btn_bg_color'] : '';
                $btn_text_color = ! empty($item['btn_text_color']) ? $item['btn_text_color'] : '';
                $card_border_color = ! empty($item['card_border_color']) ? $item['card_border_color'] : '';
                $per_card_border_width = ! empty($item['card_border_width']) ? $item['card_border_width'] : '';
                $per_card_border_style = ! empty($item['card_border_style']) ? $item['card_border_style'] : '';

                $card_bg_image_id = ! empty($item['card_bg_image']) ? $item['card_bg_image'] : '';
                $card_bg_image_url = $card_bg_image_id ? wp_get_attachment_image_url($card_bg_image_id, 'full') : '';

                // Typography Inline Overrides
                $title_style = "";
                $body_style = "";
                $btn_typo_style = ""; // Will append to $btn_style

                foreach ($typo_elements as $slug => $data) {
                    $inline_css = "";
                    foreach ($prop_map as $key_suffix => $css_prop) {
                        $key = "{$slug}_{$key_suffix}";
                        if (!empty($item[$key])) {
                            $inline_css .= "{$css_prop}: {$item[$key]}; ";
                        }
                    }
                    if ($slug === 'title') $title_style = $inline_css;
                    if ($slug === 'body') $body_style = $inline_css;
                    if ($slug === 'button') $btn_typo_style = $inline_css;
                }

                // DETERMINE TAGS
                // Global Defaults
                $g_title_tag = get_post_meta($post_id, "_cgs_title_tag", true) ?: 'h3';
                $g_body_tag  = get_post_meta($post_id, "_cgs_body_tag", true) ?: 'p';
                
                // Item Overrides
                $title_tag = !empty($item['title_tag']) ? $item['title_tag'] : $g_title_tag;
                $body_tag  = !empty($item['body_tag']) ? $item['body_tag'] : $g_body_tag;
                
                // Sanitize tags to be safe (allow h1-h6, p, div, span)
                $allowed_tags = ['h1','h2','h3','h4','h5','h6','p','div','span'];
                if(!in_array($title_tag, $allowed_tags)) $title_tag = 'h3';
                if(!in_array($body_tag, $allowed_tags)) $body_tag = 'p';


                // Generate the inline style
                $wrapper_style = 'width: 100vw; flex-shrink: 0; display: flex; justify-content: center; align-items: center; box-sizing: border-box; ';
                $inner_style = '';
                $btn_style = $btn_typo_style; // Start with typo

                // Background Image (Goes on Wrapper)
                $has_bg_image = false;
                if ($card_bg_image_url) {
                    $wrapper_style .= "background-image: url('" . esc_url($card_bg_image_url) . "'); background-size: cover; background-position: center; ";
                    $has_bg_image = true;
                }

                // Inner Card Styles - Use per-card color if set, otherwise fall back to global
                if (!empty($bg_color)) {
                  $inner_style .= "background-color: {$bg_color}; ";
                } else {
                  $inner_style .= "background-color: {$card_bg}; ";
                }
                
                // Outer Card (Wrapper) Styles
                $per_card_wrapper_bg = !empty($item['wrapper_bg_color']) ? $item['wrapper_bg_color'] : '';
                $global_wrapper_bg = get_post_meta($post_id, '_cgs_wrapper_bg_color', true);
                
                $final_wrapper_bg = '';
                if ( !empty($per_card_wrapper_bg) ) { 
                    $final_wrapper_bg = $per_card_wrapper_bg;
                } elseif ( !empty($global_wrapper_bg) ) {
                    $final_wrapper_bg = $global_wrapper_bg;
                }

                if ( !empty($final_wrapper_bg) ) {
                    $wrapper_style .= "background-color: {$final_wrapper_bg}; ";
                }
                
                if (!empty($text_color)) {
                  $inner_style .= "color: {$text_color}; ";
                } else {
                  $inner_style .= "color: {$card_txt}; ";
                }
                if (!empty($btn_bg_color)) {
                  $btn_style .= "background-color: {$btn_bg_color}; color: {$btn_text_color}; ";
                } else {
                  $btn_style .= "background-color: {$btn_bg}; color: {$btn_txt}; ";
                }
                
                if(isset($item['btn_width']) && $item['btn_width'] !== '') $btn_style .= "width:{$item['btn_width']}; display:inline-block; text-align:center;";
                
                // Card Border (Button)
                if(!empty($item['btn_border_width'])) $btn_style .= "border-width:{$item['btn_border_width']}px;";
                if(!empty($item['btn_border_style'])) $btn_style .= "border-style:{$item['btn_border_style']};";
                if(!empty($item['btn_border_color'])) $btn_style .= "border-color:{$item['btn_border_color']};";
                
                // Card Normal Effects
                if(!empty($item['btn_shadow'])) $btn_style .= "box-shadow:{$item['btn_shadow']};";
                
                $c_tn = [];
                if(!empty($item['btn_scale'])) $c_tn[] = "scale({$item['btn_scale']})";
                if(!empty($item['btn_lift'])) $c_tn[] = "translateY(-{$item['btn_lift']}px)";
                if(!empty($c_tn)) {
                    $btn_style .= "transform: " . implode(' ', $c_tn) . ";";
                }
                
                // Padding
                foreach(['top','right','bottom','left'] as $f) {
                   $key = "btn_padding_{$f}";
                   if(isset($item[$key]) && $item[$key] !== '') $btn_style .= "padding-{$f}:{$item[$key]}px;";
                }
                // Radius
                // Map keys to CSS props
                $rad_map = [
                    'btn_radius_top_lt' => 'border-top-left-radius',
                    'btn_radius_top_rt' => 'border-top-right-radius',
                    'btn_radius_btm_rt' => 'border-bottom-right-radius',
                    'btn_radius_btm_lt' => 'border-bottom-left-radius'
                ];
                foreach($rad_map as $key => $prop) {
                   if(isset($item[$key]) && $item[$key] !== '') $btn_style .= "{$prop}:{$item[$key]}px;";
                }

                // Apply per-card border if set, otherwise fall back to global border
                if (!empty($card_border_color)) {
                  $inner_style .= "border-color: {$card_border_color}; ";
                } else if (!empty($card_border_clr)) {
                  $inner_style .= "border-color: {$card_border_clr}; ";
                }
                
                // Border width: per-card > global
                $final_border_width = !empty($per_card_border_width) ? $per_card_border_width : $card_border_width;
                if (!empty($final_border_width) && $final_border_width !== '0') {
                  $inner_style .= "border-width: {$final_border_width}px; ";
                }
                
                // Border style: per-card > global
                $final_border_style = !empty($per_card_border_style) ? $per_card_border_style : $card_border_style;
                if (!empty($final_border_style) && $final_border_style !== 'none') {
                  $inner_style .= "border-style: {$final_border_style}; ";
                }

                // Default global position is now 'bottom' (from previous code) but we want 'left'/'right' logic
                $item_img_position = isset($item['img_position']) && $item['img_position'] ? $item['img_position'] : $img_position;
                
                // Classes
                $wrapper_classes = "cgs-carousel-card";
                if ($has_bg_image) {
                    $wrapper_classes .= " cgs-has-bg-image";
                }
                
                $inner_classes = "cgs-card-inner cgs-layout-" . esc_attr($item_img_position);

              ?>
                <div class="<?php echo esc_attr($wrapper_classes); ?>" style="<?php echo esc_attr($wrapper_style); ?>">
                    <div class="<?php echo esc_attr($inner_classes); ?>" style="<?php echo esc_attr($inner_style); ?>">
                    
                    <?php if ('right' === $item_img_position) { ?>
                         <!-- Right Layout: Content First, Image Second (handled by flex order or structure) -->
                         <!-- Actually, standard structure is Image then Content. CSS handles row-reverse for Right layout. -->
                    <?php } ?>

                    <?php if ($img) : ?>
                        <div class="cgs-card-image-column">
                             <div class="cgs-card-image-wrap">
                                <img src="<?php echo esc_url($img); ?>" alt="" />
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="cgs-card-content-column">
                        <?php if (! empty($item['title'])) : ?>
                            <div class="cgs-card-header">
                            <<?php echo $title_tag; ?> class="cgs-card-title" style="<?php echo esc_attr($title_style); ?>"><?php echo wp_kses_post($item['title']); ?></<?php echo $title_tag; ?>>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (! empty($item['body'])) : ?>
                            <<?php echo $body_tag; ?> class="cgs-card-body" style="<?php echo esc_attr($body_style); ?>"><?php echo wp_kses_post($item['body']); ?></<?php echo $body_tag; ?>>
                        <?php endif; ?>
                        
                        <?php if (! empty($item['button_text']) && ! empty($item['button_url'])) : ?>
                            <?php 
                                $icon_html = '';
                                if(!empty($item['btn_icon_class'])) {
                                    $icon_html = '<i class="' . esc_attr($item['btn_icon_class']) . '"></i>';
                                }
                                $pos = isset($item['btn_icon_pos']) ? $item['btn_icon_pos'] : 'right';
                                $btn_content = $item['button_text'];
                                
                                // Add spacing if icon exists
                                if($icon_html) {
                                    if($pos === 'left') {
                                        $btn_content = $icon_html . ' <span style="margin-left:5px;">' . $btn_content . '</span>';
                                    } else {
                                        $btn_content = '<span style="margin-right:5px;">' . $btn_content . '</span> ' . $icon_html;
                                    }
                                }
                            ?>
                            <a href="<?php echo esc_url($item['button_url']); ?>" class="button cgs-btn-<?php echo $i; ?>" target="<?php echo esc_attr( !empty($item['btn_target']) ? $item['btn_target'] : '_blank' ); ?>" rel="<?php echo esc_attr( !empty($item['btn_rel']) ? $item['btn_rel'] : 'noopener' ); ?>" style="<?php echo esc_attr($btn_style); ?>">
                            <?php echo wp_kses_post($btn_content); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php } else if ('vertical' === $layout) { ?>
          <!-- frontend design for vertical stack -->
          <div class="cgs-vertical-wrapper" id="<?php echo esc_attr($cid); ?>">
            <div class="cgs-vertical-cards">
              <?php foreach ($items as $i => $item) :
                $img = ! empty($item['image']) ? wp_get_attachment_image_url($item['image'], 'large') : '';
                // Get each card style
                $bg_color   = ! empty($item['card_bg_color']) ? $item['card_bg_color'] : '';
                $text_color = ! empty($item['text_color']) ? $item['text_color'] : '';
                $btn_bg_color = ! empty($item['btn_bg_color']) ? $item['btn_bg_color'] : '';
                $btn_text_color = ! empty($item['btn_text_color']) ? $item['btn_text_color'] : '';
                $card_border_color = ! empty($item['card_border_color']) ? $item['card_border_color'] : '';
                $per_card_border_width = ! empty($item['card_border_width']) ? $item['card_border_width'] : '';
                $per_card_border_style = ! empty($item['card_border_style']) ? $item['card_border_style'] : '';

                $card_bg_image_id = ! empty($item['card_bg_image']) ? $item['card_bg_image'] : '';
                $card_bg_image_url = $card_bg_image_id ? wp_get_attachment_image_url($card_bg_image_id, 'full') : '';

                // Typography Inline Overrides
                $title_style = "";
                $body_style = "";
                $btn_typo_style = "";

                foreach ($typo_elements as $slug => $data) {
                    $inline_css = "";
                    foreach ($prop_map as $key_suffix => $css_prop) {
                        $key = "{$slug}_{$key_suffix}";
                        if (!empty($item[$key])) {
                            $inline_css .= "{$css_prop}: {$item[$key]}; ";
                        }
                    }
                    if ($slug === 'title') $title_style = $inline_css;
                    if ($slug === 'body') $body_style = $inline_css;
                    if ($slug === 'button') $btn_typo_style = $inline_css;
                }

                // DETERMINE TAGS
                $g_title_tag = get_post_meta($post_id, "_cgs_title_tag", true) ?: 'h3';
                $g_body_tag  = get_post_meta($post_id, "_cgs_body_tag", true) ?: 'p';
                $title_tag = !empty($item['title_tag']) ? $item['title_tag'] : $g_title_tag;
                $body_tag  = !empty($item['body_tag']) ? $item['body_tag'] : $g_body_tag;
                $allowed_tags = ['h1','h2','h3','h4','h5','h6','p','div','span'];
                if(!in_array($title_tag, $allowed_tags)) $title_tag = 'h3';
                if(!in_array($body_tag, $allowed_tags)) $body_tag = 'p';

                // Generate the inline style for wrapper (outer card)
                $wrapper_style = '';
                $inner_style = '';
                $btn_style = $btn_typo_style;

                // Background Image (Goes on Wrapper)
                $has_bg_image = false;
                if ($card_bg_image_url) {
                    $wrapper_style .= "background-image: url('" . esc_url($card_bg_image_url) . "'); background-size: cover; background-position: center; ";
                    $has_bg_image = true;
                }

                // Inner Card Styles
                if (!empty($bg_color)) {
                  $inner_style .= "background-color: {$bg_color}; ";
                } else {
                  $inner_style .= "background-color: {$card_bg}; ";
                }
                
                // Outer Card (Wrapper) Background
                $per_card_wrapper_bg = !empty($item['wrapper_bg_color']) ? $item['wrapper_bg_color'] : '';
                $global_wrapper_bg = get_post_meta($post_id, '_cgs_wrapper_bg_color', true);
                $final_wrapper_bg = !empty($per_card_wrapper_bg) ? $per_card_wrapper_bg : (!empty($global_wrapper_bg) ? $global_wrapper_bg : '');
                if (!empty($final_wrapper_bg)) {
                    $wrapper_style .= "--stack-card-bg: {$final_wrapper_bg}; ";
                }
                
                if (!empty($text_color)) {
                  $inner_style .= "color: {$text_color}; ";
                } else {
                  $inner_style .= "color: {$card_txt}; ";
                }
                if (!empty($btn_bg_color)) {
                  $btn_style .= "background-color: {$btn_bg_color}; color: {$btn_text_color}; ";
                } else {
                  $btn_style .= "background-color: {$btn_bg}; color: {$btn_txt}; ";
                }
                
                if(isset($item['btn_width']) && $item['btn_width'] !== '') $btn_style .= "width:{$item['btn_width']}; display:inline-block; text-align:center;";
                
                // Button Border
                if(!empty($item['btn_border_width'])) $btn_style .= "border-width:{$item['btn_border_width']}px;";
                if(!empty($item['btn_border_style'])) $btn_style .= "border-style:{$item['btn_border_style']};";
                if(!empty($item['btn_border_color'])) $btn_style .= "border-color:{$item['btn_border_color']};";
                
                // Button Effects
                if(!empty($item['btn_shadow'])) $btn_style .= "box-shadow:{$item['btn_shadow']};";
                $c_tn = [];
                if(!empty($item['btn_scale'])) $c_tn[] = "scale({$item['btn_scale']})";
                if(!empty($item['btn_lift'])) $c_tn[] = "translateY(-{$item['btn_lift']}px)";
                if(!empty($c_tn)) $btn_style .= "transform: " . implode(' ', $c_tn) . ";";
                
                // Button Padding
                foreach(['top','right','bottom','left'] as $f) {
                   $key = "btn_padding_{$f}";
                   if(isset($item[$key]) && $item[$key] !== '') $btn_style .= "padding-{$f}:{$item[$key]}px;";
                }
                // Button Radius
                $rad_map = [
                    'btn_radius_top_lt' => 'border-top-left-radius',
                    'btn_radius_top_rt' => 'border-top-right-radius',
                    'btn_radius_btm_rt' => 'border-bottom-right-radius',
                    'btn_radius_btm_lt' => 'border-bottom-left-radius'
                ];
                foreach($rad_map as $key => $prop) {
                   if(isset($item[$key]) && $item[$key] !== '') $btn_style .= "{$prop}:{$item[$key]}px;";
                }

                // Card Border
                if (!empty($card_border_color)) {
                  $inner_style .= "border-color: {$card_border_color}; ";
                } else if (!empty($card_border_clr)) {
                  $inner_style .= "border-color: {$card_border_clr}; ";
                }
                $final_border_width = !empty($per_card_border_width) ? $per_card_border_width : $card_border_width;
                if (!empty($final_border_width) && $final_border_width !== '0') {
                  $inner_style .= "border-width: {$final_border_width}px; ";
                }
                $final_border_style = !empty($per_card_border_style) ? $per_card_border_style : $card_border_style;
                if (!empty($final_border_style) && $final_border_style !== 'none') {
                  $inner_style .= "border-style: {$final_border_style}; ";
                }

                // Image position for vertical layout
                $item_img_position = isset($item['ver_img_position']) && $item['ver_img_position'] ? $item['ver_img_position'] : $ver_img_position;
                
                // Classes - use same as horizontal + stack-card for GSAP
                $wrapper_classes = "cgs-carousel-card cgs-stack-card";
                if ($has_bg_image) {
                    $wrapper_classes .= " cgs-has-bg-image";
                }
                $inner_classes = "cgs-card-inner cgs-layout-" . esc_attr($item_img_position);

              ?>
                <div class="<?php echo esc_attr($wrapper_classes); ?>" style="<?php echo esc_attr($wrapper_style); ?>">
                    <div class="<?php echo esc_attr($inner_classes); ?>" style="<?php echo esc_attr($inner_style); ?>">
                    
                    <?php if ($img) : ?>
                        <div class="cgs-card-image-column">
                             <div class="cgs-card-image-wrap">
                                <img src="<?php echo esc_url($img); ?>" alt="" />
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="cgs-card-content-column">
                        <?php if (! empty($item['title'])) : ?>
                            <div class="cgs-card-header">
                            <<?php echo $title_tag; ?> class="cgs-card-title" style="<?php echo esc_attr($title_style); ?>"><?php echo wp_kses_post($item['title']); ?></<?php echo $title_tag; ?>>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (! empty($item['body'])) : ?>
                            <<?php echo $body_tag; ?> class="cgs-card-body" style="<?php echo esc_attr($body_style); ?>"><?php echo wp_kses_post($item['body']); ?></<?php echo $body_tag; ?>>
                        <?php endif; ?>
                        
                        <?php if (! empty($item['button_text']) && ! empty($item['button_url'])) : ?>
                            <?php 
                                $icon_html = '';
                                if(!empty($item['btn_icon_class'])) {
                                    $icon_html = '<i class="' . esc_attr($item['btn_icon_class']) . '"></i>';
                                }
                                $pos = isset($item['btn_icon_pos']) ? $item['btn_icon_pos'] : 'right';
                                $btn_content = $item['button_text'];
                                
                                if($icon_html) {
                                    if($pos === 'left') {
                                        $btn_content = $icon_html . ' <span style="margin-left:5px;">' . $btn_content . '</span>';
                                    } else {
                                        $btn_content = '<span style="margin-right:5px;">' . $btn_content . '</span> ' . $icon_html;
                                    }
                                }
                            ?>
                            <a href="<?php echo esc_url($item['button_url']); ?>" class="button cgs-btn-<?php echo $i; ?>" target="<?php echo esc_attr( !empty($item['btn_target']) ? $item['btn_target'] : '_blank' ); ?>" rel="<?php echo esc_attr( !empty($item['btn_rel']) ? $item['btn_rel'] : 'noopener' ); ?>" style="<?php echo esc_attr($btn_style); ?>">
                            <?php echo wp_kses_post($btn_content); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php } ?>
      </div>
      <?php
      return ob_get_clean();
    }
  }
}

// instantiate so our hooks actually fire
new Cinematic_Scroll_Shortcode();
