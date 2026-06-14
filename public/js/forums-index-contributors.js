/**
 * Contributor avatar carousels on forum listing cards (same interaction as communities).
 */
(function () {
    function initForumContributorCarousels(scope) {
        var root = scope || document;
        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        root.querySelectorAll('.forum-card__contributors:not([data-carousel-bound])').forEach(function (carousel) {
            carousel.setAttribute('data-carousel-bound', '1');

            var track = carousel.querySelector('.community-room-card__avatar-track');
            var slides = carousel.querySelector('.community-room-card__avatar-slides');
            var prev = carousel.querySelector('.community-room-card__avatar-nav--prev');
            var next = carousel.querySelector('.community-room-card__avatar-nav--next');
            if (!track || !slides) {
                return;
            }

            function gapPx() {
                try {
                    var g = window.getComputedStyle(slides).columnGap || window.getComputedStyle(slides).gap;
                    var n = parseFloat(g);
                    return isNaN(n) ? 8 : n;
                } catch (e) {
                    return 8;
                }
            }

            function itemStep() {
                var first = slides.children[0];
                if (!first) {
                    return 40;
                }
                return first.getBoundingClientRect().width + gapPx();
            }

            function maxScroll() {
                return track.scrollWidth - track.clientWidth;
            }

            function scrollBy(dir) {
                var delta = itemStep() * dir;
                track.scrollTo({
                    left: Math.max(0, Math.min(maxScroll(), track.scrollLeft + delta)),
                    behavior: reduceMotion ? 'auto' : 'smooth'
                });
            }

            if (prev) {
                prev.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    scrollBy(-1);
                });
            }
            if (next) {
                next.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    scrollBy(1);
                });
            }

            var isPlaying = !reduceMotion;
            var rafId = null;
            var scrollFrac = 0;
            var pixelsPerFrame = 0.18;

            function tick() {
                if (!isPlaying) {
                    rafId = null;
                    return;
                }
                var max = maxScroll();
                if (max <= 2) {
                    rafId = requestAnimationFrame(tick);
                    return;
                }
                scrollFrac += pixelsPerFrame;
                if (scrollFrac >= 1) {
                    track.scrollLeft = Math.min(max, track.scrollLeft + 1);
                    scrollFrac = 0;
                    if (track.scrollLeft >= max - 1) {
                        track.scrollLeft = 0;
                    }
                }
                rafId = requestAnimationFrame(tick);
            }

            carousel.addEventListener('mouseenter', function () {
                isPlaying = false;
            });
            carousel.addEventListener('mouseleave', function () {
                if (!reduceMotion) {
                    isPlaying = true;
                    if (!rafId) {
                        rafId = requestAnimationFrame(tick);
                    }
                }
            });

            if (isPlaying && maxScroll() > 2) {
                rafId = requestAnimationFrame(tick);
            }
        });
    }

    window.initForumContributorCarousels = initForumContributorCarousels;

    document.addEventListener('DOMContentLoaded', function () {
        initForumContributorCarousels(document);
    });
})();
