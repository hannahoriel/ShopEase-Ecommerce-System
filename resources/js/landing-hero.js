/* =========================================================
   SHOPEASE — LANDING HERO (FIRST PAGE)
   Handles: network canvas, cursor spotlight, 3D tilt parallax,
   scroll parallax, live dashboard (counter / bars / feed),
   parcel tracker, add-to-cart loop, magnetic buttons.
   Everything is decorative and degrades gracefully with
   prefers-reduced-motion.
========================================================= */

(function () {
    'use strict';

    var hero = document.getElementById('home');
    var stage = document.getElementById('sxStage');
    if (!hero || !stage || !hero.classList.contains('sx-hero')) return;

    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var finePointer = window.matchMedia('(pointer: fine)').matches;

    var $ = function (id) { return document.getElementById(id); };
    var clamp = function (v, a, b) { return Math.min(b, Math.max(a, v)); };
    var lerp = function (a, b, t) { return a + (b - a) * t; };
    var easeOut = function (t) { return 1 - Math.pow(1 - t, 4); };
    var easeInOut = function (t) { return t < .5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; };

    var heroVisible = true;
    var pointer = { x: 0, y: 0, nx: 0, ny: 0, active: false };

    /* =====================================================
       1. RESPONSIVE SCALE FOR THE 560x520 STAGE
    ===================================================== */

    function fitStage() {
        var w = stage.clientWidth || 560;
        stage.style.setProperty('--sx-scale', clamp(w / 560, .5, 1.1).toFixed(3));
    }
    fitStage();
    if ('ResizeObserver' in window) {
        new ResizeObserver(fitStage).observe(stage);
    } else {
        window.addEventListener('resize', fitStage);
    }

    /* =====================================================
       2. NETWORK CANVAS (nodes + connecting lines + data packets)
    ===================================================== */

    var canvas = $('sxNet');
    var ctx = canvas ? canvas.getContext('2d') : null;
    var nodes = [];
    var pulses = [];
    var cw = 0, ch = 0, dpr = 1;
    var LINK = 140;
    var lastPulse = 0;

    function buildNodes() {
        var count = clamp(Math.round((cw * ch) / 17000), 24, 78);
        nodes = [];
        for (var i = 0; i < count; i++) {
            nodes.push({
                x: Math.random() * cw,
                y: Math.random() * ch,
                vx: (Math.random() - .5) * .36,
                vy: (Math.random() - .5) * .36,
                r: Math.random() * 1.4 + .8
            });
        }
    }

    function sizeCanvas() {
        if (!canvas) return;
        var rect = hero.getBoundingClientRect();
        dpr = Math.min(window.devicePixelRatio || 1, 2);
        cw = rect.width;
        ch = rect.height;
        canvas.width = Math.round(cw * dpr);
        canvas.height = Math.round(ch * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        buildNodes();
        if (reduce) drawNet(0, true);
    }

    function drawNet(now, still) {
        if (!ctx) return;
        ctx.clearRect(0, 0, cw, ch);

        var i, j, a, b, dx, dy, d, alpha;

        if (!still) {
            for (i = 0; i < nodes.length; i++) {
                a = nodes[i];
                a.x += a.vx;
                a.y += a.vy;
                if (a.x < -20) a.x = cw + 20; else if (a.x > cw + 20) a.x = -20;
                if (a.y < -20) a.y = ch + 20; else if (a.y > ch + 20) a.y = -20;

                if (pointer.active) {
                    dx = pointer.x - a.x;
                    dy = pointer.y - a.y;
                    d = Math.sqrt(dx * dx + dy * dy);
                    if (d < 190 && d > 1) {
                        a.x += (dx / d) * .35;
                        a.y += (dy / d) * .35;
                    }
                }
            }
        }

        ctx.lineWidth = 1;
        var links = [];

        for (i = 0; i < nodes.length; i++) {
            a = nodes[i];
            for (j = i + 1; j < nodes.length; j++) {
                b = nodes[j];
                dx = a.x - b.x;
                dy = a.y - b.y;
                d = Math.sqrt(dx * dx + dy * dy);
                if (d < LINK) {
                    alpha = (1 - d / LINK) * .3;
                    ctx.strokeStyle = 'rgba(255,160,135,' + alpha.toFixed(3) + ')';
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(b.x, b.y);
                    ctx.stroke();
                    if (d < LINK * .8) links.push([a, b]);
                }
            }

            if (pointer.active) {
                dx = a.x - pointer.x;
                dy = a.y - pointer.y;
                d = Math.sqrt(dx * dx + dy * dy);
                if (d < 190) {
                    ctx.strokeStyle = 'rgba(255,214,196,' + ((1 - d / 190) * .55).toFixed(3) + ')';
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(pointer.x, pointer.y);
                    ctx.stroke();
                }
            }
        }

        for (i = 0; i < nodes.length; i++) {
            a = nodes[i];
            ctx.fillStyle = 'rgba(255,190,170,.75)';
            ctx.beginPath();
            ctx.arc(a.x, a.y, a.r, 0, 6.2832);
            ctx.fill();
        }

        if (still) return;

        /* data packets travelling along random links */
        if (now - lastPulse > 650 && links.length && pulses.length < 6) {
            var pick = links[(Math.random() * links.length) | 0];
            pulses.push({ a: pick[0], b: pick[1], t: 0, s: .012 + Math.random() * .01 });
            lastPulse = now;
        }
        for (i = pulses.length - 1; i >= 0; i--) {
            var p = pulses[i];
            p.t += p.s;
            if (p.t >= 1) { pulses.splice(i, 1); continue; }
            var px = lerp(p.a.x, p.b.x, p.t);
            var py = lerp(p.a.y, p.b.y, p.t);
            var g = ctx.createRadialGradient(px, py, 0, px, py, 9);
            g.addColorStop(0, 'rgba(255,225,210,.95)');
            g.addColorStop(1, 'rgba(255,135,110,0)');
            ctx.fillStyle = g;
            ctx.beginPath();
            ctx.arc(px, py, 9, 0, 6.2832);
            ctx.fill();
        }
    }

    sizeCanvas();
    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(sizeCanvas, 150);
    }, { passive: true });

    /* =====================================================
       3. POINTER: SPOTLIGHT + 3D TILT
    ===================================================== */

    var spot = $('sxSpot');
    var scene = $('sxScene');
    var spotX = 0, spotY = 0;
    var tiltX = 0, tiltY = 0;

    if (finePointer) {
        hero.addEventListener('pointermove', function (e) {
            var r = hero.getBoundingClientRect();
            pointer.x = e.clientX - r.left;
            pointer.y = e.clientY - r.top;
            pointer.nx = (pointer.x / r.width) * 2 - 1;
            pointer.ny = (pointer.y / r.height) * 2 - 1;
            if (!pointer.active) {
                spotX = pointer.x;
                spotY = pointer.y;
            }
            pointer.active = true;
            hero.classList.add('is-pointer');
        }, { passive: true });

        hero.addEventListener('pointerleave', function () {
            pointer.active = false;
            pointer.nx = 0;
            pointer.ny = 0;
            hero.classList.remove('is-pointer');
        });
    }

    /* =====================================================
       4. SCROLL PARALLAX (copy fades, layers drift)
    ===================================================== */

    var lastScroll = -1;
    function applyScroll() {
        var y = window.pageYOffset || 0;
        if (y === lastScroll) return;
        lastScroll = y;
        var h = hero.offsetHeight || 1;
        var s = clamp(y, 0, h);
        hero.style.setProperty('--sx-sy', s.toFixed(1));
        hero.style.setProperty('--sx-copy-o', clamp(1 - s / (h * .8), 0, 1).toFixed(3));
    }

    /* =====================================================
       5. MAIN RAF LOOP
    ===================================================== */

    function tick(now) {
        requestAnimationFrame(tick);
        if (!heroVisible || document.hidden) return;

        if (finePointer) {
            spotX = lerp(spotX, pointer.x, .12);
            spotY = lerp(spotY, pointer.y, .12);
            if (spot) spot.style.transform = 'translate3d(' + spotX.toFixed(1) + 'px,' + spotY.toFixed(1) + 'px,0)';
        }

        /* gentle idle breathing + pointer-driven tilt */
        var idleY = Math.sin(now * .0006) * 2.2;
        var idleX = Math.cos(now * .0005) * 1.4;
        var targetY = (pointer.active ? pointer.nx * 8 : 0) + idleY;
        var targetX = (pointer.active ? -pointer.ny * 6 : 0) + idleX;
        tiltY = lerp(tiltY, targetY, .06);
        tiltX = lerp(tiltX, targetX, .06);
        if (scene) scene.style.transform = 'rotateX(' + tiltX.toFixed(2) + 'deg) rotateY(' + tiltY.toFixed(2) + 'deg)';

        applyScroll();
        drawNet(now, false);
    }

    if (!reduce) {
        requestAnimationFrame(tick);
    } else {
        applyScroll();
        window.addEventListener('scroll', applyScroll, { passive: true });
    }

    if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (entries) {
            heroVisible = entries[0].isIntersecting;
        }, { threshold: 0 }).observe(hero);
    }

    /* =====================================================
       6. LIVE DASHBOARD — counter, bars, order feed
    ===================================================== */

    var ordersEl = $('sxOrders');
    var barsEl = $('sxBars');
    var feedEl = $('sxFeed');
    var orders = 1284;

    function fmt(n) { return n.toLocaleString('en-US'); }

    function countUp(el, to, duration, delay) {
        if (reduce) { el.textContent = fmt(to); return; }
        setTimeout(function () {
            var start = performance.now();
            (function step(now) {
                var t = clamp((now - start) / duration, 0, 1);
                el.textContent = fmt(Math.round(to * easeOut(t)));
                if (t < 1) requestAnimationFrame(step);
            })(start);
        }, delay);
    }

    if (ordersEl) countUp(ordersEl, orders, 1900, 1200);

    function bump() {
        if (!ordersEl) return;
        ordersEl.classList.remove('is-bump');
        void ordersEl.offsetWidth;
        ordersEl.classList.add('is-bump');
    }

    /* --- bars --- */
    var BAR_COUNT = 24;
    var bars = [];
    var barVals = [];

    if (barsEl) {
        for (var i = 0; i < BAR_COUNT; i++) {
            var bar = document.createElement('i');
            barsEl.appendChild(bar);
            bars.push(bar);
            barVals.push(30 + Math.random() * 50);
        }
        var paintBars = function () {
            for (var k = 0; k < BAR_COUNT; k++) {
                bars[k].style.height = barVals[k].toFixed(1) + '%';
                bars[k].classList.toggle('hot', k === BAR_COUNT - 1);
            }
        };
        setTimeout(paintBars, 1100);
        if (!reduce) {
            setInterval(function () {
                if (!heroVisible || document.hidden) return;
                var last = barVals[barVals.length - 1];
                var next = clamp(last + (Math.random() - .42) * 38, 18, 96);
                barVals.shift();
                barVals.push(next);
                paintBars();
            }, 1500);
        }
    }

    /* --- order feed --- */
    var ICONS = {
        bag: '<path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/>',
        box: '<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9"/>',
        truck: '<path d="M3 6h11v11H3zM14 10h4l3 4v3h-7z"/><circle cx="7.5" cy="18" r="2"/><circle cx="17.5" cy="18" r="2"/>',
        check: '<path d="m5 12 4.5 4.5L19 7"/>'
    };

    var PRODUCTS = [
        ['Wireless Earbuds', 1299], ['Ceramic Mug Set', 459], ['Canvas Tote Bag', 349],
        ['Desk Lamp', 899], ['Running Shoes', 2499], ['Skincare Set', 1149],
        ['Phone Case', 249], ['Coffee Beans 1kg', 689], ['Mechanical Keyboard', 2799]
    ];
    var STATES = [
        { c: 's-confirmed', t: 'Confirmed', i: 'bag' },
        { c: 's-packed', t: 'Packed', i: 'box' },
        { c: 's-ship', t: 'On the way', i: 'truck' },
        { c: 's-done', t: 'Delivered', i: 'check' }
    ];
    var orderSeq = 20838;
    var productIdx = 0;

    function makeRow(stateIdx) {
        var prod = PRODUCTS[productIdx++ % PRODUCTS.length];
        var st = STATES[stateIdx];
        orderSeq += 1 + ((Math.random() * 3) | 0);
        var li = document.createElement('li');
        li.innerHTML =
            '<span class="sx-feed-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' + ICONS[st.i] + '</svg></span>' +
            '<div><b>' + prod[0] + '</b><small>#SE-' + orderSeq + ' · ₱' + fmt(prod[1]) + '</small></div>' +
            '<span class="sx-st ' + st.c + '">' + st.t + '</span>';
        return li;
    }

    if (feedEl) {
        /* seed the first three rows with a staggered entrance */
        [2, 1, 0].forEach(function (stateIdx, n) {
            var li = makeRow(stateIdx);
            if (!reduce) li.classList.add('is-new');
            feedEl.insertBefore(li, feedEl.firstChild);
            if (!reduce) {
                setTimeout(function () { li.classList.remove('is-new'); }, 1250 + n * 220);
            }
        });

        if (!reduce) {
            setInterval(function () {
                if (!heroVisible || document.hidden) return;
                var weights = [0, 0, 1, 1, 2, 3, 0];
                var li = makeRow(weights[(Math.random() * weights.length) | 0]);
                li.classList.add('is-new');
                feedEl.insertBefore(li, feedEl.firstChild);
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () { li.classList.remove('is-new'); });
                });

                var rows = feedEl.children;
                if (rows.length > 3) {
                    var old = rows[rows.length - 1];
                    old.classList.add('is-out');
                    setTimeout(function () { if (old.parentNode) old.parentNode.removeChild(old); }, 700);
                }

                orders += 1 + ((Math.random() * 3) | 0);
                if (ordersEl) { ordersEl.textContent = fmt(orders); bump(); }
            }, 2800);
        }
    }

    /* =====================================================
       7. PARCEL TRACKER (path-synced)
    ===================================================== */

    var routeFill = $('sxRouteFill');
    var parcel = $('sxParcel');
    var statusEl = $('sxStatus');
    var etaEl = $('sxEta');
    var nodeEls = Array.prototype.slice.call(stage.querySelectorAll('.sx-node'));

    if (routeFill && parcel) {
        var len = routeFill.getTotalLength();
        routeFill.style.strokeDasharray = len;

        nodeEls.forEach(function (g) {
            var at = parseFloat(g.getAttribute('data-at')) || 0;
            var pt = routeFill.getPointAtLength(len * at);
            g.setAttribute('transform', 'translate(' + pt.x.toFixed(2) + ' ' + pt.y.toFixed(2) + ')');
        });

        var lastLabel = '';
        var setProgress = function (p) {
            var pt = routeFill.getPointAtLength(len * p);
            parcel.setAttribute('transform', 'translate(' + pt.x.toFixed(2) + ' ' + pt.y.toFixed(2) + ')');
            routeFill.style.strokeDashoffset = (len * (1 - p)).toFixed(2);

            nodeEls.forEach(function (g) {
                var at = parseFloat(g.getAttribute('data-at')) || 0;
                g.classList.toggle('done', p >= at - .001);
            });

            var label = p < .36 ? 'Picked up from seller'
                : p < .72 ? 'At sorting hub'
                : p < .995 ? 'Out for delivery'
                : 'Delivered ✓';
            if (label !== lastLabel && statusEl) {
                statusEl.textContent = label;
                lastLabel = label;
            }
            if (etaEl) etaEl.textContent = p >= .995 ? 'Delivered' : 'ETA ' + Math.max(1, Math.round(24 * (1 - p))) + ' min';
        };

        if (reduce) {
            setProgress(.6);
        } else {
            var CYCLE = 9500, HOLD = 1800, startAt = performance.now() + 1500;
            setProgress(0);
            (function loop(now) {
                requestAnimationFrame(loop);
                if (!heroVisible || document.hidden) return;
                var el = now - startAt;
                if (el < 0) return;
                var t = el % (CYCLE + HOLD);
                setProgress(t >= CYCLE ? 1 : easeInOut(t / CYCLE));
            })(startAt);
        }
    }

    /* =====================================================
       8. ADD-TO-CART LOOP
    ===================================================== */

    var addBtn = $('sxAdd');
    if (addBtn && !reduce) {
        setInterval(function () {
            if (!heroVisible || document.hidden) return;
            if (addBtn.classList.contains('is-added')) {
                addBtn.classList.remove('is-added');
                return;
            }
            addBtn.classList.add('is-press');
            setTimeout(function () {
                addBtn.classList.remove('is-press');
                addBtn.classList.add('is-added');
            }, 220);
        }, 3600);
    }

    /* =====================================================
       9. MAGNETIC BUTTONS
    ===================================================== */

    if (finePointer && !reduce) {
        Array.prototype.forEach.call(hero.querySelectorAll('.sx-magnetic'), function (btn) {
            btn.addEventListener('pointermove', function (e) {
                var r = btn.getBoundingClientRect();
                var dx = e.clientX - (r.left + r.width / 2);
                var dy = e.clientY - (r.top + r.height / 2);
                btn.style.setProperty('--bx', (dx * .2).toFixed(1) + 'px');
                btn.style.setProperty('--by', (dy * .32).toFixed(1) + 'px');
            });
            btn.addEventListener('pointerleave', function () {
                btn.style.setProperty('--bx', '0px');
                btn.style.setProperty('--by', '0px');
            });
        });
    }
})();
