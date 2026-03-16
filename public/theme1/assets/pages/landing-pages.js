document.addEventListener("DOMContentLoaded", () => {
   // HEADER DROPDOWN HOVER FUNCTIONALITY
   // ----------------------------------------------
   const header = document.querySelector("#header");
   const dropdownHover = document.querySelector(".dropdown-hover");

   // Initialize dropdown hover functionality
   if (dropdownHover) {
      const dropdownToggle = dropdownHover.querySelector(".dropdown-toggle");
      const dropdownMenu = dropdownHover.querySelector(".dropdown-menu");

      // Ensure the dropdown toggle and menu exist before adding event listeners
      if (dropdownToggle && dropdownMenu) {
         // Add hover event listeners for the dropdown
         dropdownToggle.addEventListener("mouseenter", () => {
            dropdownToggle.click();
         });

         // Close the dropdown when the mouse leaves the dropdown hover area
         dropdownHover.addEventListener("mouseleave", (e) => {
            document.body.click();
         });
      }
   }

   // HEADER SCROLL FUNCTIONALITY
   // ----------------------------------------------
   // This function checks the scroll position and adds/removes the class based on the scroll offset
   if (header) {
      const handleScroll = () => {
         const offset = window.scrollY;
         if (offset > 100) {
            //setIsScrolled(true);
            header.classList.add("header--shrink");
         } else if (offset < 10) {
            //setIsScrolled(false);
            header.classList.remove("header--shrink");
         }
      };

      // Initial check on page load
      handleScroll();

      // Add event listener
      window.addEventListener("scroll", handleScroll);

      // If you want to remove the event listener (e.g., on cleanup), call:
      // window.removeEventListener("scroll", handleScroll);
   }

   // COLOR SCHEMES FUNCTIONALITY
   // ----------------------------------------------
   // Find element #_dm_colorSchemesContainer then add event click for each button children,
   // when user click on button change the documentElement attribute "data-scheme" to the button data-color value.
   const colorSchemesContainer = document.getElementById(
      "_dm_colorSchemesContainer"
   );

   if (colorSchemesContainer) {
      const buttons =
         colorSchemesContainer.querySelectorAll("button[data-color]");

      // Helper to remove all checklists
      const removeAllChecklists = () => {
         buttons.forEach((btn) => {
            btn.classList.remove("active");
            btn.querySelector(".color-checklist")?.remove();
         });
      };

      // Helper to create checklist SVG
      const createChecklistSvg = () => {
         const svg = document.createElementNS(
            "http://www.w3.org/2000/svg",
            "svg"
         );
         svg.classList.add("color-checklist");
         svg.setAttribute("width", "16");
         svg.setAttribute("height", "16");
         svg.setAttribute("viewBox", "0 0 24 24");
         svg.setAttribute("fill", "none");
         svg.innerHTML = `<path d="M5 13l5 5L19 7" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></path>`;
         return svg;
      };

      buttons.forEach((btn) => {
         btn.addEventListener("click", () => {
            const color = btn.dataset.color;
            if (color) {
               document.documentElement.setAttribute("data-scheme", color);
            }
            removeAllChecklists();
            btn.classList.add("active");
            btn.appendChild(createChecklistSvg());
         });
      });

      // Set initial color scheme based on data-scheme attribute
      const initialColorScheme = colorSchemesContainer.querySelector(
         `[data-color="${document.documentElement.getAttribute(
            "data-scheme"
         )}"]`
      );
      if (initialColorScheme) {
         initialColorScheme.classList.add("active");
         initialColorScheme.appendChild(createChecklistSvg());
      }
   }

   // MOTION ANIMATION FOR LANDING PAGES
   // ----------------------------------------------
   const { animate, inView } = window.Motion;
   const runAnimationOnce = false;
   const createMotionAnimation = (selector, fromVars, toVars, options) => {
      if (!selector.length) selector = [selector];

      return {
         in: () =>
            selector.forEach((el, id) => {
               animate(el, fromVars, {
                  ...options,
                  // durations: 5,
                  easing: "ease-in-out",
                  delay: (options.delay || 0) + id * 0.2,
                  type: "spring",
                  visualDuration: options.duration || 0.5,
                  bounce: 0.5,
               });
            }),

         out: () =>
            selector.forEach((el, id) => {
               animate(el, toVars, {
                  ...options,
                  easing: "ease-in-out",
                  delay: (options.delay || 0) + id * 0.2,
                  type: "spring",
                  visualDuration: options.duration || 0.5,
                  bounce: 0.5,
               });
            }),
      };
   };

   // MOTION SLIDE UP FOR COLORS SECTION
   // ----------------------------------------------
   const colorsContainer = document.querySelector("#colors");
   if (colorsContainer) {
      const motionColorSlideUp = createMotionAnimation(
         colorsContainer,
         { opacity: 1, y: 0 },
         { opacity: 0, y: 100 },
         { duration: 0.5 }
      );

      // Initial state
      motionColorSlideUp.out();

      const colorAnimation = inView(
         colorsContainer,
         () => {
            // Animation when the colorsContainer element enters the viewport
            motionColorSlideUp.in();

            // If you want to run the animation only once
            if (runAnimationOnce) colorAnimation();

            // Animation when the colorsContainer element leaves the viewport
            return () => {
               motionColorSlideUp.out();
            };
         },
         { margin: "200px", amount: 0.5 }
      );
   }

   // MOTION SCALE FOR FEATURES SECTION
   // ----------------------------------------------
   const featuresContainer = document.querySelector("#features-container");

   if (featuresContainer) {
      const featuresScaleEl =
         featuresContainer.querySelectorAll("._dm_motionScale");

      // Helper functions for motion scale in and out
      const motionFeatureScale = createMotionAnimation(
         featuresScaleEl,
         { opacity: 1, scale: 1 },
         { opacity: 0, scale: 0 },
         { duration: 0.5 }
      );

      // Initial state
      motionFeatureScale.out();

      const featuresAnimation = inView(
         featuresContainer,
         () => {
            // Animation when the featuresContainer element enters the viewport
            motionFeatureScale.in();

            // If you want to run the animation only once
            if (runAnimationOnce) featuresAnimation();

            // Animation when the featuresContainer element leaves the viewport
            return () => {
               motionFeatureScale.out();
            };
         },
         { margin: "100px", amount: 0.5 }
      );
   }

   // MOTION SLIDE LEFT & RIGHT FOR ABOUT SECTION
   // ----------------------------------------------
   const aboutContainer = document.querySelector("#about");
   const aboutSlideLeftEl = document.querySelector("._dm_motionSlideLeft");
   const aboutSlideRightEl = document.querySelector("._dm_motionSlideRight");

   if (aboutContainer && aboutSlideLeftEl && aboutSlideRightEl) {
      const motionSlideLeft = createMotionAnimation(
         [aboutSlideLeftEl],
         { opacity: 1, x: 0 },
         { opacity: 0, x: 100 },
         { duration: 0.5, delay: 1 }
      );

      const motionSlideRight = createMotionAnimation(
         [aboutSlideRightEl],
         { opacity: 1, x: 0 },
         { opacity: 0, x: -100 },
         { duration: 0.5 }
      );

      // Initial state
      motionSlideLeft.out();
      motionSlideRight.out();

      const aboutAnimation = inView(
         aboutContainer,
         () => {
            // Animation when the aboutContainer element enters the viewport
            motionSlideLeft.in();
            motionSlideRight.in();

            // If you want to run the animation only once
            if (runAnimationOnce) aboutAnimation();

            // Animation when the aboutContainer element leaves the viewport
            return () => {
               motionSlideLeft.out();
               motionSlideRight.out();
            };
         },
         { margin: "100px", amount: 0.5 }
      );
   }

   // MOTION SLIDE UP FOR PRICING SECTION
   // ----------------------------------------------
   const pricingContainer = document.querySelector("#pricing");

   if (pricingContainer) {
      const pricingSlideUpEl =
         pricingContainer.querySelectorAll("._dm_motionSlideUp");

      const motionPricingSlideUp = createMotionAnimation(
         pricingSlideUpEl,
         { opacity: 1, y: 0 },
         { opacity: 0, y: 100 },
         { duration: 0.5 }
      );

      // Initial state
      motionPricingSlideUp.out();

      const pricingAnimation = inView(
         pricingContainer,
         () => {
            // Animation when the pricingContainer element enters the viewport
            motionPricingSlideUp.in();

            // If you want to run the animation only once
            if (runAnimationOnce) pricingAnimation();

            // Animation when the pricingContainer element leaves the viewport
            return () => {
               motionPricingSlideUp.out();
            };
         },
         { margin: "100px", amount: 0.5 }
      );
   }

   // MOTION SLIDE UP FOR TESTIMONIALS SECTION
   // ----------------------------------------------
   const testimonialsContainer = document.querySelector("#testimonials");
   if (testimonialsContainer) {
      const motionSlideUpTestimonial = createMotionAnimation(
         testimonialsContainer,
         { opacity: 1, y: 0 },
         { opacity: 0, y: 100 },
         { duration: 0.5 }
      );

      // Initial state
      motionSlideUpTestimonial.out();

      const testimonialAnimation = inView(
         testimonialsContainer,
         () => {
            // Animation when the testimonialsContainer element enters the viewport
            motionSlideUpTestimonial.in();

            // If you want to run the animation only once
            if (runAnimationOnce) testimonialAnimation();

            // Animation when the testimonialsContainer element leaves the viewport
            return () => {
               motionSlideUpTestimonial.out();
            };
         },
         { margin: "200px", amount: 0.5 }
      );
   }

   // MOTION FADE IN FOR CTA SECTION
   // ----------------------------------------------
   const ctaContainer = document.querySelector("#calltoaction");
   if (ctaContainer) {
      const motionCTAFade = createMotionAnimation(
         ctaContainer,
         { opacity: 1 },
         { opacity: 0 },
         { duration: 2 }
      );

      // Initial state
      motionCTAFade.out();

      const ctaAnimation = inView(
         ctaContainer,
         () => {
            // Animation when the ctaContainer element enters the viewport
            motionCTAFade.in();

            // If you want to run the animation only once
            if (runAnimationOnce) ctaAnimation();

            // Animation when the ctaContainer element leaves the viewport
            return () => {
               motionCTAFade.out();
            };
         },
         { margin: "100px", amount: 0.5 }
      );
   }
});
