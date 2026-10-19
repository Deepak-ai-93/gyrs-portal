/*
 * GYRS public motion layer.
 *
 * Progressive enhancement only: nothing is hidden until GSAP is confirmed
 * present, and every effect is skipped when the visitor prefers reduced
 * motion. Colour tokens are never touched here — motion and layout only.
 */
(function () {
    'use strict';

    function start(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn, { once: true });
        } else {
            fn();
        }
    }

    /* A thin scroll rail that shows reading progress. Functional, not
       decorative, so it also runs when motion is reduced. */
    function initProgressRail() {
        var bar = document.createElement('div');
        bar.className = 'motion-progress';
        bar.setAttribute('aria-hidden', 'true');
        document.body.appendChild(bar);

        var ticking = false;

        function update() {
            var doc = document.documentElement;
            var max = doc.scrollHeight - window.innerHeight;
            var ratio = max > 0 ? (window.scrollY || doc.scrollTop) / max : 0;
            ratio = Math.min(1, Math.max(0, ratio));
            bar.style.transform = 'scaleX(' + ratio + ')';
            ticking = false;
        }

        function request() {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(update);
            }
        }

        window.addEventListener('scroll', request, { passive: true });
        window.addEventListener('resize', request);
        update();
    }

    start(function () {
        initProgressRail();

        var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var gsap = window.gsap;
        var ScrollTrigger = window.ScrollTrigger;

        var root = document.documentElement;
        var overlay = document.getElementById('gyrs-preloader');
        var hold = 0;

        function releasePreloader() {
            if (typeof window.__gyrsPreloaderRelease === 'function') {
                window.__gyrsPreloaderRelease();
            } else {
                root.classList.remove('is-preloading');
            }
        }

        function runPreloader() {
            var rings = overlay.querySelectorAll('.pl-ring');
            var bull = overlay.querySelector('.pl-bull');
            var arrow = overlay.querySelector('.pl-arrow');
            var ripple = overlay.querySelector('.pl-ripple');
            var mark = overlay.querySelector('.preloader-mark');
            var words = Array.prototype.slice.call(overlay.querySelectorAll('.pl-word'));

            if (words.length) {
                gsap.set(words, { opacity: 0, scale: 0.9 });
            }

            var tl = gsap.timeline();

            tl.from(rings, {
                scale: 0,
                opacity: 0,
                duration: 0.55,
                stagger: 0.1,
                svgOrigin: '70 70',
                ease: 'power3.out'
            })
                .from(bull, { scale: 0, duration: 0.35, ease: 'back.out(2)', svgOrigin: '70 70' }, '-=0.25')
                .to(
                    words,
                    { opacity: 1, scale: 1, duration: 0.5, stagger: 0.09, ease: 'back.out(1.6)' },
                    0.35
                )
                .fromTo(
                    arrow,
                    { x: 80, y: 80, opacity: 0 },
                    { x: 0, y: 0, opacity: 1, duration: 0.5, ease: 'power3.in' },
                    '-=0.1'
                )
                .to(bull, { scale: 1.4, duration: 0.12, yoyo: true, repeat: 1, ease: 'power2.out', svgOrigin: '70 70' }, '-=0.02')
                .fromTo(
                    ripple,
                    { scale: 0.6, opacity: 0.8, svgOrigin: '70 70' },
                    { scale: 6, opacity: 0, duration: 0.6, ease: 'power2.out' },
                    '<'
                )
                .to(mark, { scale: 1.05, duration: 0.22, ease: 'power2.out' }, '<')
                .to(mark, { scale: 1, duration: 0.3 })
                .to(words, { opacity: 0, duration: 0.3, ease: 'power1.in' }, '-=0.35')
                .to(overlay, {
                    yPercent: -100,
                    duration: 0.7,
                    ease: 'power3.inOut',
                    onComplete: function () {
                        if (words.length) {
                            gsap.killTweensOf(words);
                        }
                        releasePreloader();
                    }
                });

            words.forEach(function (word, i) {
                gsap.to(word, {
                    y: i % 2 ? 10 : -10,
                    rotation: i % 2 ? 4 : -4,
                    duration: 2 + i * 0.5,
                    ease: 'sine.inOut',
                    yoyo: true,
                    repeat: -1,
                    delay: 0.8 + i * 0.12
                });
            });

            /* Let the page begin revealing as the curtain starts to lift. */
            return Math.max(0, tl.duration() - 0.9);
        }

        if (overlay && gsap && !prefersReduced) {
            hold = runPreloader();
        } else {
            releasePreloader();
        }

        if (!gsap || !ScrollTrigger || prefersReduced) {
            return;
        }

        gsap.registerPlugin(ScrollTrigger);

        var list = function (root, selector) {
            return Array.prototype.slice.call(root.querySelectorAll(selector));
        };

        /* ── Hero: masked line reveal, supporting fade-up, parallax ── */
        (function hero() {
            var stage = document.querySelector('[data-motion-hero]');
            if (!stage) {
                return;
            }

            var lines = list(stage, '[data-motion-line]');
            if (lines.length) {
                gsap.set(lines, { yPercent: 100 });
                gsap.to(lines, {
                    yPercent: 0,
                    duration: 1.05,
                    ease: 'power4.out',
                    stagger: 0.12,
                    delay: hold + 0.05
                });
            }

            var rest = Array.prototype.filter.call(stage.children, function (el) {
                return el.tagName !== 'H1';
            });
            if (rest.length) {
                gsap.set(rest, { opacity: 0, y: 26 });
                gsap.to(rest, {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: 'power3.out',
                    stagger: 0.09,
                    delay: hold + 0.55
                });
            }

            var section = stage.closest('.hero');
            if (!section || !window.matchMedia('(min-width: 768px)').matches) {
                return;
            }

            gsap.to(stage, {
                y: -46,
                ease: 'none',
                scrollTrigger: {
                    trigger: section,
                    start: 'top top',
                    end: 'bottom top',
                    scrub: true
                }
            });

            list(section, '.hero-glow-a').forEach(function (el) {
                gsap.to(el, {
                    yPercent: 34,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: section,
                        start: 'top top',
                        end: 'bottom top',
                        scrub: true
                    }
                });
            });

            list(section, '.hero-glow-b').forEach(function (el) {
                gsap.to(el, {
                    yPercent: -24,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: section,
                        start: 'top top',
                        end: 'bottom top',
                        scrub: true
                    }
                });
            });
        })();

        /* ── Metric values count up when the strip is reached ── */
        list(document, '[data-count-to]').forEach(function (el) {
            var to = parseFloat(el.getAttribute('data-count-to'));
            if (isNaN(to)) {
                return;
            }

            var suffix = el.getAttribute('data-count-suffix') || '';
            var state = { value: 0 };

            ScrollTrigger.create({
                trigger: el,
                start: 'top 88%',
                once: true,
                onEnter: function () {
                    gsap.to(state, {
                        value: to,
                        duration: 1.5,
                        ease: 'power2.out',
                        onUpdate: function () {
                            el.textContent = Math.round(state.value).toLocaleString('en-US') + suffix;
                        },
                        onComplete: function () {
                            el.textContent = to.toLocaleString('en-US') + suffix;
                        }
                    });
                }
            });
        });

        /* ── Section reveals, grouped per section (not per element) ── */
        list(document, '[data-motion-reveal]').forEach(function (el) {
            if (el.hasAttribute('data-motion-stagger')) {
                var kids = Array.prototype.slice.call(el.children);
                if (!kids.length) {
                    return;
                }

                gsap.set(kids, { opacity: 0, y: 28 });
                ScrollTrigger.batch(kids, {
                    start: 'top 90%',
                    once: true,
                    onEnter: function (batch) {
                        gsap.to(batch, {
                            opacity: 1,
                            y: 0,
                            duration: 0.6,
                            ease: 'power3.out',
                            stagger: 0.08,
                            overwrite: true
                        });
                    }
                });
            } else {
                gsap.set(el, { opacity: 0, y: 28 });
                ScrollTrigger.create({
                    trigger: el,
                    start: 'top 88%',
                    once: true,
                    onEnter: function () {
                        gsap.to(el, {
                            opacity: 1,
                            y: 0,
                            duration: 0.7,
                            ease: 'power3.out'
                        });
                    }
                });
            }
        });

        /* ── Process steps: scroll-linked highlight and a drawing rail ── */
        (function steps() {
            var track = document.querySelector('[data-motion-steps]');
            if (!track) {
                return;
            }

            list(track, '[data-motion-step]').forEach(function (card) {
                gsap.set(card, { opacity: 0, y: 30 });

                ScrollTrigger.create({
                    trigger: card,
                    start: 'top 88%',
                    once: true,
                    onEnter: function () {
                        card.classList.add('is-reached');
                        gsap.to(card, {
                            opacity: 1,
                            y: 0,
                            duration: 0.6,
                            ease: 'power3.out'
                        });

                        var num = card.querySelector('.step-num');
                        if (num) {
                            gsap.fromTo(
                                num,
                                { scale: 0.55 },
                                { scale: 1, duration: 0.5, ease: 'back.out(1.8)' }
                            );
                        }
                    }
                });

                ScrollTrigger.create({
                    trigger: card,
                    start: 'top 60%',
                    end: 'bottom 55%',
                    toggleClass: 'is-current'
                });
            });

            var fill = track.querySelector('[data-motion-steps-fill]');
            if (fill) {
                gsap.fromTo(
                    fill,
                    { scaleX: 0 },
                    {
                        scaleX: 1,
                        ease: 'none',
                        transformOrigin: 'left center',
                        scrollTrigger: {
                            trigger: track,
                            start: 'top 72%',
                            end: 'bottom 55%',
                            scrub: true
                        }
                    }
                );
            }
        })();

        /* ── FAQ: smooth accordion that answers the click ── */
        (function faq() {
            list(document, '.faq-list details').forEach(function (details) {
                var summary = details.querySelector('summary');
                var answer = details.querySelector('.faq-answer');
                if (!summary || !answer) {
                    return;
                }

                answer.style.overflow = 'hidden';

                summary.addEventListener('click', function (event) {
                    event.preventDefault();

                    if (details.open) {
                        gsap.to(answer, {
                            height: 0,
                            opacity: 0,
                            duration: 0.28,
                            ease: 'power2.in',
                            onComplete: function () {
                                details.open = false;
                                gsap.set(answer, { clearProps: 'height,opacity' });
                            }
                        });
                    } else {
                        details.open = true;
                        gsap.fromTo(
                            answer,
                            { height: 0, opacity: 0 },
                            {
                                height: 'auto',
                                opacity: 1,
                                duration: 0.34,
                                ease: 'power2.out',
                                onComplete: function () {
                                    gsap.set(answer, { clearProps: 'height,opacity' });
                                }
                            }
                        );
                    }
                });
            });
        })();

        window.addEventListener('load', function () {
            ScrollTrigger.refresh();
        });
    });
})();
