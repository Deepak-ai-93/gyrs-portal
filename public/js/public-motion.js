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
            var stage = overlay.querySelector('.preloader-stage');
            var mark = overlay.querySelector('.preloader-mark');
            var arrowSystem = overlay.querySelector('.pl-arrow-system');
            var orbitGroup = overlay.querySelector('.pl-orbit-group');
            var outerFrame = overlay.querySelector('.pl-outer-frame');
            var dialTrack = overlay.querySelector('.pl-dial-track');
            var cardinals = overlay.querySelectorAll('.pl-cardinal');
            var pips = overlay.querySelectorAll('.pl-pip');
            var innerRing = overlay.querySelector('.pl-inner-ring');
            var crosshairs = overlay.querySelectorAll('.pl-crosshair');
            var bull = overlay.querySelector('.pl-bull');
            var ripple1 = overlay.querySelector('.pl-ripple-1');
            var ripple2 = overlay.querySelector('.pl-ripple-2');
            var sparks = overlay.querySelector('.pl-sparks');
            var chips = Array.prototype.slice.call(overlay.querySelectorAll('.pl-chip'));
            var statusText = document.getElementById('pl-status-text');
            var meterBar = document.getElementById('pl-meter-bar');
            var counter = document.getElementById('pl-counter');

            /* Initial states */
            if (stage) gsap.set(stage, { opacity: 0, scale: 0.94 });
            if (arrowSystem) gsap.set(arrowSystem, { rotation: -130, svgOrigin: '100 100' });
            if (bull) gsap.set(bull, { scale: 0.8, svgOrigin: '100 100' });
            if (cardinals.length || pips.length) {
                gsap.set([cardinals, pips], { scale: 0, opacity: 0, svgOrigin: '100 100' });
            }
            if (chips.length) {
                gsap.set(chips, { opacity: 0, scale: 0.85, y: 14 });
            }

            var progressObj = { value: 0 };
            var tl = gsap.timeline();

            /* 1. Stage Fade-in and outer dial construction */
            if (stage) {
                tl.to(stage, { opacity: 1, scale: 1, duration: 0.45, ease: 'power2.out' });
            }
            if (outerFrame) {
                tl.fromTo(outerFrame, { scale: 0.7, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.5, ease: 'back.out(1.4)', svgOrigin: '100 100' }, '<');
            }
            if (dialTrack) {
                tl.fromTo(dialTrack, { rotation: -90, opacity: 0 }, { rotation: 0, opacity: 1, duration: 0.6, ease: 'power2.out', svgOrigin: '100 100' }, '<0.1');
            }
            if (cardinals.length || pips.length) {
                tl.to([cardinals, pips], { scale: 1, opacity: 1, duration: 0.35, stagger: 0.03, ease: 'back.out(2)', svgOrigin: '100 100' }, '<0.15');
            }
            if (innerRing) {
                tl.fromTo(innerRing, { scale: 0.5, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.4, ease: 'power2.out', svgOrigin: '100 100' }, '<0.1');
            }
            if (crosshairs.length) {
                tl.fromTo(crosshairs, { scale: 0, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.35, ease: 'power2.out', svgOrigin: '100 100' }, '<0.1');
            }

            /* 2. Orbital gyro rotation (spin continuously) */
            if (orbitGroup) {
                gsap.to(orbitGroup, {
                    rotation: 360,
                    duration: 7,
                    ease: 'none',
                    repeat: -1,
                    svgOrigin: '100 100'
                });
            }

            /* 3. Floating chips reveal */
            if (chips.length) {
                tl.to(chips, {
                    opacity: 1,
                    scale: 1,
                    y: 0,
                    duration: 0.5,
                    stagger: 0.08,
                    ease: 'back.out(1.7)'
                }, 0.25);

                chips.forEach(function (chip, i) {
                    gsap.to(chip, {
                        y: (i % 2 === 0 ? -6 : 6),
                        duration: 1.8 + i * 0.3,
                        ease: 'sine.inOut',
                        yoyo: true,
                        repeat: -1,
                        delay: 0.6 + i * 0.1
                    });
                });
            }

            /* 4. Progress count-up & status evolution */
            tl.to(progressObj, {
                value: 68,
                duration: 0.8,
                ease: 'power2.out',
                onUpdate: function () {
                    var val = Math.round(progressObj.value);
                    if (counter) counter.textContent = val + '%';
                    if (meterBar) meterBar.style.width = val + '%';
                }
            }, 0.2);

            tl.add(function () {
                if (statusText) statusText.textContent = 'MAPPING CAREER PATHWAYS';
            }, 0.6);

            /* 5. Pathfinder needle sweeping and locking onto 45 degrees */
            if (arrowSystem) {
                tl.to(arrowSystem, {
                    rotation: 58,
                    duration: 0.75,
                    ease: 'power3.inOut',
                    svgOrigin: '100 100'
                }, 0.5)
                .to(arrowSystem, {
                    rotation: 45,
                    duration: 0.35,
                    ease: 'elastic.out(1.2, 0.4)',
                    svgOrigin: '100 100'
                }, '-=0.05');
            }

            /* 6. Lock Moment: Bullseye punch, Shockwaves & Spark burst */
            tl.add(function () {
                if (statusText) statusText.textContent = 'OPPORTUNITIES CONNECTED';
            }, '-=0.25')
            .to(progressObj, {
                value: 100,
                duration: 0.4,
                ease: 'power2.out',
                onUpdate: function () {
                    var val = Math.round(progressObj.value);
                    if (counter) counter.textContent = val + '%';
                    if (meterBar) meterBar.style.width = val + '%';
                }
            }, '<');

            if (bull) {
                tl.to(bull, {
                    scale: 1.6,
                    duration: 0.15,
                    yoyo: true,
                    repeat: 1,
                    ease: 'power3.out',
                    svgOrigin: '100 100'
                }, '<');
            }

            if (ripple1) {
                tl.fromTo(ripple1,
                    { scale: 0.5, opacity: 0.9, svgOrigin: '100 100' },
                    { scale: 4.5, opacity: 0, duration: 0.65, ease: 'power2.out' },
                    '<'
                );
            }

            if (ripple2) {
                tl.fromTo(ripple2,
                    { scale: 0.5, opacity: 0.7, svgOrigin: '100 100' },
                    { scale: 3.2, opacity: 0, duration: 0.55, ease: 'power2.out' },
                    '<0.1'
                );
            }

            if (sparks) {
                tl.fromTo(sparks,
                    { scale: 0.3, opacity: 1, svgOrigin: '100 100' },
                    { scale: 1.4, opacity: 0, duration: 0.5, ease: 'power3.out' },
                    '<'
                );
            }

            if (mark) {
                tl.to(mark, {
                    scale: 1.04,
                    duration: 0.18,
                    yoyo: true,
                    repeat: 1,
                    ease: 'power2.out'
                }, '<');
            }

            /* 7. Brief hold of completion */
            tl.to({}, { duration: 0.35 });

            /* 8. Curtain Lift */
            if (stage) {
                tl.to(stage, {
                    scale: 0.94,
                    opacity: 0,
                    duration: 0.35,
                    ease: 'power2.in'
                });
            }

            tl.to(overlay, {
                yPercent: -100,
                duration: 0.75,
                ease: 'power4.inOut',
                onComplete: function () {
                    if (orbitGroup) gsap.killTweensOf(orbitGroup);
                    chips.forEach(function (c) { gsap.killTweensOf(c); });
                    releasePreloader();
                }
            }, '-=0.1');

            return Math.max(0, tl.duration() - 0.95);
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
