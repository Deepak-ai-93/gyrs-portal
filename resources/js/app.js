(function () {
    'use strict';

    document.documentElement.classList.remove('no-js');

    function toArray(scope, selector) {
        return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
    }

    function focusable(panel) {
        return toArray(
            panel,
            'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
        ).filter(function (el) {
            return el.offsetWidth > 0 || el.offsetHeight > 0 || el === document.activeElement;
        });
    }

    /* Mobile navigation */
    toArray(document, '[data-menu-toggle]').forEach(function (button) {
        var target = document.getElementById(button.getAttribute('aria-controls'));
        if (!target) {
            return;
        }

        button.addEventListener('click', function () {
            var open = target.classList.toggle('is-open');
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && target.classList.contains('is-open')) {
                target.classList.remove('is-open');
                button.setAttribute('aria-expanded', 'false');
                button.focus();
            }
        });
    });

    /* Dismissible alerts */
    toArray(document, '[data-alert-dismiss]').forEach(function (button) {
        button.addEventListener('click', function () {
            var alert = button.closest('.alert');
            if (alert) {
                alert.remove();
            }
        });
    });

    toArray(document, '[data-alert-autodismiss]').forEach(function (alert) {
        window.setTimeout(function () {
            alert.style.transition = 'opacity .4s ease';
            alert.style.opacity = '0';
            window.setTimeout(function () {
                alert.remove();
            }, 420);
        }, 7000);
    });

    /* Accessible modals */
    var lastFocused = null;

    function openModal(name, opener) {
        var modal = document.querySelector('[data-modal="' + name + '"]');
        if (!modal) {
            return;
        }

        lastFocused = opener || document.activeElement;
        modal.hidden = false;
        document.body.classList.add('modal-open');

        var panel = modal.querySelector('.modal-panel') || modal;
        if (!panel.hasAttribute('tabindex')) {
            panel.setAttribute('tabindex', '-1');
        }

        var first = focusable(panel)[0];
        (first || panel).focus();

        modal.__trap = function (event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                closeModal(name);
                return;
            }

            if (event.key !== 'Tab') {
                return;
            }

            var list = focusable(panel);
            if (!list.length) {
                event.preventDefault();
                return;
            }

            var firstEl = list[0];
            var lastEl = list[list.length - 1];

            if (event.shiftKey && document.activeElement === firstEl) {
                event.preventDefault();
                lastEl.focus();
            } else if (!event.shiftKey && document.activeElement === lastEl) {
                event.preventDefault();
                firstEl.focus();
            }
        };

        modal.addEventListener('keydown', modal.__trap);
    }

    function closeModal(name) {
        var modal = document.querySelector('[data-modal="' + name + '"]');
        if (!modal) {
            return;
        }

        modal.hidden = true;
        document.body.classList.remove('modal-open');

        if (modal.__trap) {
            modal.removeEventListener('keydown', modal.__trap);
        }

        if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
        }
    }

    toArray(document, '[data-modal-open]').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            openModal(button.getAttribute('data-modal-open'), button);
        });
    });

    toArray(document, '[data-modal-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            var modal = button.closest('[data-modal]');
            if (modal) {
                closeModal(modal.getAttribute('data-modal'));
            }
        });
    });

    window.gyrsModal = { open: openModal, close: closeModal };

    /* Confirmation before destructive actions */
    document.addEventListener('submit', function (event) {
        var form = event.target;
        var message = form && form.getAttribute ? form.getAttribute('data-confirm') : null;

        if (message && !window.confirm(message)) {
            event.preventDefault();
        }
    });

    toArray(document, 'a[data-confirm], button[data-confirm]').forEach(function (element) {
        element.addEventListener('click', function (event) {
            if (!window.confirm(element.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    /* Unsaved-change guard for admin forms */
    toArray(document, '[data-guard-unsaved]').forEach(function (form) {
        var dirty = false;
        var submitting = false;

        form.addEventListener('input', function () {
            dirty = true;
        });

        form.addEventListener('change', function () {
            dirty = true;
        });

        form.addEventListener('submit', function () {
            submitting = true;
        });

        window.addEventListener('beforeunload', function (event) {
            if (dirty && !submitting) {
                event.preventDefault();
                event.returnValue = '';
                return '';
            }
        });
    });

    /* Loading feedback for server-backed forms */
    toArray(document, '[data-loading-form]').forEach(function (form) {
        form.addEventListener('submit', function () {
            form.setAttribute('aria-busy', 'true');
            var submit = form.querySelector('[type="submit"]');
            if (submit && !submit.disabled) {
                submit.classList.add('is-loading');
                submit.disabled = true;
            }
        });
    });

    /* Text rotators (headline keywords, storytelling lines) */
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    toArray(document, '[data-rotator]').forEach(function (rotator) {
        var items = toArray(rotator, '[data-rotator-item]');
        if (items.length < 2) {
            return;
        }

        var interval = parseInt(rotator.getAttribute('data-rotator-interval'), 10) || 3200;
        var index = 0;

        items.forEach(function (item, i) {
            item.classList.toggle('is-active', i === 0);
        });

        if (reduceMotion) {
            return;
        }

        window.setInterval(function () {
            items[index].classList.remove('is-active');
            index = (index + 1) % items.length;
            items[index].classList.add('is-active');
        }, interval);
    });

    /* Copy-to-clipboard helpers */
    toArray(document, '[data-copy]').forEach(function (button) {
        button.addEventListener('click', function () {
            var value = button.getAttribute('data-copy');

            if (!value || !navigator.clipboard) {
                return;
            }

            navigator.clipboard.writeText(value).then(function () {
                var original = button.textContent;
                button.textContent = 'Copied';
                window.setTimeout(function () {
                    button.textContent = original;
                }, 1400);
            });
        });
    });

    /* Demo credential autofill */
    toArray(document, '[data-demo-fill]').forEach(function (button) {
        button.addEventListener('click', function () {
            var form = document.querySelector(button.getAttribute('data-demo-form'));

            if (!form) {
                return;
            }

            var loginField = form.querySelector('[name="' + button.getAttribute('data-demo-login-name') + '"]');
            var passwordField = form.querySelector('[name="password"]');

            if (loginField) {
                loginField.value = button.getAttribute('data-demo-login') || '';
                loginField.dispatchEvent(new Event('input', { bubbles: true }));
            }

            if (passwordField) {
                passwordField.value = button.getAttribute('data-demo-password') || '';
                passwordField.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    });
})();
