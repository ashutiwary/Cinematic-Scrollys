document.addEventListener("DOMContentLoaded", function () {
  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;
  gsap.registerPlugin(ScrollTrigger);


  const layoutElement = document.querySelector('.cgs-carousel-outer');
  const layout = layoutElement ? layoutElement.getAttribute('data-layout') : 'horizontal';
  console.log("The layout value is:", layout);

  // Helper: Detect header height
  function getHeaderHeight() {
    const potentialHeaders = document.querySelectorAll('header, .site-header, #masthead');
    let headerHeight = 0;

    potentialHeaders.forEach(el => {
      const style = window.getComputedStyle(el);
      if (style.position === 'fixed' || style.position === 'sticky') {
        const rect = el.getBoundingClientRect();
        if (rect.top < 50 && rect.height > 0) {
          headerHeight = Math.max(headerHeight, rect.height);
        }
      }
    });

    return headerHeight;
  }

  // Apply padding to inner cards for visual centering (doesn't break ScrollTrigger)
  function applyVisualCentering() {
    const headerHeight = getHeaderHeight();

    if (headerHeight > 0) {
      // Add top padding to the inner card content to push it down, creating visual balance
      document.querySelectorAll('.cgs-card-inner').forEach(inner => {
        inner.style.paddingTop = (headerHeight / 2) + 'px';
      });
    }
  }

  // Apply visual centering on load (only for horizontal layout)
  if (layout === 'horizontal') {
    // Small delay to ensure header is rendered
    setTimeout(applyVisualCentering, 100);
    window.addEventListener('resize', applyVisualCentering);
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

        // Detect sticky/fixed header height
        var headerHeight = getHeaderHeight();

        // Calculate gap needed to make one card take up exactly one viewport width of travel
        var gap = winWidth - cardWidth;
        if (gap < 0) gap = 0;

        // Apply gap
        carousel.style.gap = gap + "px";

        // Center the first and last items
        var centerPadding = (winWidth - cardWidth) / 2;
        carousel.style.paddingLeft = centerPadding + "px";
        carousel.style.paddingRight = centerPadding + "px";

        // ScrollTrigger Calculations
        var totalWidth = (cardWidth * cards.length) + (gap * (cards.length - 1)) + (centerPadding * 2);
        var maxScroll = totalWidth - winWidth;

        gsap.set(carousel, { x: 0 });

        // Kill old trigger if exists to avoid duplication on resize
        if (outer.st) outer.st.kill();

        // Adjust start position for sticky header
        // "top top+=" means: when the top of the wrapper reaches (top of viewport + headerHeight)
        var startPosition = headerHeight > 0 ? "top top+=" + headerHeight : "top top";

        outer.st = ScrollTrigger.create({
          trigger: wrapper,
          start: startPosition,
          end: () => "+=" + maxScroll,
          pin: true,
          pinSpacing: true,
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
