document.addEventListener("DOMContentLoaded", function () {
  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;
  gsap.registerPlugin(ScrollTrigger);

  // IMPORTANT: Disable scroll-behavior:smooth as it conflicts with ScrollTrigger
  // This is a JS fallback for browsers that don't support CSS :has() selector
  document.documentElement.style.scrollBehavior = 'auto';

  // Mobile optimization: prevent unnecessary refreshes on address bar changes
  ScrollTrigger.config({
    ignoreMobileResize: true
  });

  // Detect touch devices for optimized scrolling
  const isTouchDevice = window.matchMedia("(pointer: coarse)").matches;

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
      if (cards.length === 0) return;

      const headerHeight = getHeaderHeight();
      const spacer = 30; // Space between stacked cards
      const minScale = 0.9; // Cards scale down to 90% when stacked

      cards.forEach((card, index) => {
        // Pin each card at the top as user scrolls
        ScrollTrigger.create({
          trigger: card,
          start: `top top+=${headerHeight + (index * spacer)}`,
          endTrigger: cards[cards.length - 1], // Last card ends the animation
          end: `top top+=${headerHeight + (index * spacer) + 100}`,
          pin: true,
          pinSpacing: false,
          invalidateOnRefresh: true,
          id: `pin-${index}`,
        });

        // Scale down this card as the NEXT card scrolls over it
        if (index < cards.length - 1) {
          gsap.to(card, {
            scrollTrigger: {
              trigger: cards[index + 1],
              start: "top bottom",
              end: `top top+=${headerHeight + ((index + 1) * spacer)}`,
              scrub: isTouchDevice ? 0.5 : 1,
              invalidateOnRefresh: true,
            },
            scale: minScale,
            ease: "none"
          });
        }
      });

      // Handle resize for vertical layout
      let resizeTimeout;
      window.addEventListener("resize", function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
          ScrollTrigger.refresh();
        }, 100);
      });

      // Handle orientation change
      window.addEventListener("orientationchange", function () {
        setTimeout(function () {
          ScrollTrigger.refresh(true);
        }, 200);
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

        // When pinned, GSAP positions element at top:0. If there's a sticky header,
        // we need to push the wrapper content down to appear below the header.
        // This ensures content is visually centered in the VISIBLE area (viewport - header)
        if (headerHeight > 0) {
          wrapper.style.paddingTop = headerHeight + 'px';
          wrapper.style.boxSizing = 'border-box';
        } else {
          wrapper.style.paddingTop = '0';
        }

        // Pin when wrapper top hits viewport top (under header if present)
        var startPosition = headerHeight > 0 ? "top top+=" + headerHeight : "top top";

        // Get dots for navigation
        const dots = wrapper.querySelectorAll('.cgs-dot');
        const totalSlides = cards.length;

        outer.st = ScrollTrigger.create({
          trigger: wrapper,
          start: startPosition,
          end: () => "+=" + maxScroll,
          pin: true,
          pinSpacing: true,
          scrub: isTouchDevice ? 0.2 : 0.3, // Reduced for snappier response
          anticipatePin: 1,
          invalidateOnRefresh: true,
          onUpdate: (self) => {
            // Set position directly instead of tweening (eliminates jitter from tween-within-tween)
            gsap.set(carousel, {
              x: -maxScroll * self.progress
            });

            // Update active dot based on scroll progress
            if (dots.length > 0 && totalSlides > 0) {
              const activeIndex = Math.min(
                Math.round(self.progress * (totalSlides - 1)),
                totalSlides - 1
              );
              dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === activeIndex);
              });
            }
          },
        });

        // Dot click navigation
        if (dots.length > 0) {
          // Store the pin start position for navigation
          const pinStart = wrapper.getBoundingClientRect().top + window.scrollY - headerHeight;

          dots.forEach((dot, index) => {
            dot.addEventListener('click', (e) => {
              e.preventDefault();

              // Remove focus from button to prevent blue highlight
              dot.blur();

              // Calculate target scroll position based on dot index
              const progress = totalSlides > 1 ? index / (totalSlides - 1) : 0;
              const targetScroll = pinStart + (maxScroll * progress);

              // Re-enable smooth scroll temporarily for click navigation
              document.documentElement.style.scrollBehavior = 'smooth';
              window.scrollTo({ top: targetScroll, behavior: 'smooth' });

              // Restore auto scroll behavior after scroll completes
              setTimeout(() => {
                document.documentElement.style.scrollBehavior = 'auto';
              }, 1000);
            });
          });
        }
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

      // Mobile: Handle orientation changes
      window.addEventListener("orientationchange", function () {
        // Delay to allow browser to finish orientation change
        setTimeout(function () {
          initCarousel();
          ScrollTrigger.refresh(true); // true = force recalc
        }, 200);
      });
    });
  }
});
