document.addEventListener("DOMContentLoaded", function () {
  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;
  gsap.registerPlugin(ScrollTrigger);


  const layoutElement = document.querySelector('.cgs-carousel-outer');
  const layout = layoutElement ? layoutElement.getAttribute('data-layout') : 'horizontal';
  console.log("The layout value is:", layout);

  // Helper: Calculate Available Height
  function getAvailableHeight() {
    // Basic viewport height
    let h = window.innerHeight;

    // Try to find a sticky/fixed header
    // Common selectors or check computed style
    const potentialHeaders = document.querySelectorAll('header, .site-header, #masthead, .elementor-section-type-header');
    let headerHeight = 0;

    potentialHeaders.forEach(el => {
      const style = window.getComputedStyle(el);
      if (style.position === 'fixed' || style.position === 'sticky') {
        // Only if it's currently at the top
        const rect = el.getBoundingClientRect();
        if (rect.top < 50 && rect.height > 0) {
          headerHeight = Math.max(headerHeight, rect.height);
        }
      }
    });

    // If we found a header, subtract it
    if (headerHeight > 0) {
      h -= headerHeight;
    }

    // Return pixel value string
    return h + 'px';
  }

  // Apply height to horizontal wrappers
  if (layout === 'horizontal') {
    document.querySelectorAll('.cgs-carousel-wrapper').forEach(wrapper => {
      const setHeight = () => {
        wrapper.style.height = getAvailableHeight();
      };
      setHeight();
      window.addEventListener('resize', setHeight);
    });
  }

  if (layout === 'vertical') {
    // Vertical Stack Carousel
    document.querySelectorAll(".cgs-carousel-outer").forEach(function (outer) {
      const cards = gsap.utils.toArray(".cgs-stack-card");
      const spacer = 20;
      const minScale = 0.8;

      const distributor = gsap.utils.distribute({ base: minScale, amount: 0.2 });

      cards.forEach((card, index) => {
        const scaleVal = distributor(index, cards[index], cards);

        const tween = gsap.to(card, {
          scrollTrigger: {
            trigger: card,
            start: `top top`,
            scrub: true,
            // markers: true,
            invalidateOnRefresh: true
          },
          ease: "none",
          scale: scaleVal
        });

        ScrollTrigger.create({
          trigger: card,
          start: `top-=${index * spacer} top`,
          endTrigger: '.cards',
          end: `bottom top+=${200 + (cards.length * spacer)}`,
          pin: true,
          pinSpacing: false,
          // markers: true,
          id: 'pin',
          invalidateOnRefresh: true,
        });
      });
    });


  } else if (layout === 'horizontal') {
    // Horizontal Carousel
    document.querySelectorAll(".cgs-carousel-outer").forEach(function (outer) {
      var wrapper = outer.querySelector(".cgs-carousel-wrapper");
      var carousel = outer.querySelector(".cgs-carousel");
      var cards = outer.querySelectorAll(".cgs-carousel-card");
      if (!carousel || cards.length === 0) return;

      // Function to recalculate and refresh
      function initCarousel() {
        var cardWidth = cards[0].offsetWidth;
        var winWidth = window.innerWidth;

        // Calculate gap needed to make one card take up exactly one viewport width of travel
        // Distance from Center of Card A to Center of Card B should be winWidth.
        // Center A + (CardHalf + Gap + CardHalf) = Center B ??
        // Left A to Left B = cardWidth + gap.
        // We want Left A to Left B = winWidth.
        // Therefore: gap = winWidth - cardWidth.
        var gap = winWidth - cardWidth;

        // Ensure gap is not negative 
        if (gap < 0) gap = 0;

        // Apply gap
        carousel.style.gap = gap + "px";

        // Center the first and last items
        // Padding needed = (winWidth - cardWidth) / 2
        var centerPadding = (winWidth - cardWidth) / 2;
        carousel.style.paddingLeft = centerPadding + "px";
        carousel.style.paddingRight = centerPadding + "px";

        // ScrollTrigger Calculations
        // Total real width of content
        var totalWidth = (cardWidth * cards.length) + (gap * (cards.length - 1)) + (centerPadding * 2);

        // Visible width is just the viewport
        var wrapperWidth = winWidth;

        // Max scroll is total content width minus the viewport width
        // BUT: We need to verify if "totalWidth" calculation matches the actual scrollWidth roughly.
        // Actually, simplest definition of maxScroll for horizontal scrubbing:
        // move from x:0 to x: -(totalScrollableDistance).
        // If we want the LAST card to end up Centered:
        // The container width is "totalWidth".
        // Viewport is "winWidth".
        // We stop when the right edge of container aligns with right edge of viewport.
        // totalScroll = totalWidth - winWidth.
        var maxScroll = totalWidth - winWidth;

        gsap.set(carousel, { x: 0 });

        // Kill old trigger if exists to avoid duplication on resize
        if (outer.st) outer.st.kill();

        outer.st = ScrollTrigger.create({
          trigger: wrapper,
          start: "top top",
          end: () => "+=" + maxScroll,
          pin: true,
          scrub: 1,
          anticipatePin: 1,
          invalidateOnRefresh: true,
          onUpdate: (self) => {
            gsap.to(carousel, {
              x: -maxScroll * self.progress,
              duration: 0.1,
              overwrite: "auto",
              ease: "none",
            });
          },
        });
      }

      // Initialize
      initCarousel();

      // Responsive: Debounced recalculate on resize
      let resizeTimeout;
      window.addEventListener("resize", function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
          initCarousel();
          ScrollTrigger.refresh();
        }, 100);
      });
    });
  }
});
