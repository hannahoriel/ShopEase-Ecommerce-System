/* =========================================================
   SHOPEASE LOGISTICS — LANDING HERO (FIRST PAGE)
   Handles: network canvas, cursor spotlight, 3D tilt parallax,
   scroll parallax, live route map (trucks), parcel scan, rider
   progress, partner status chip, magnetic buttons, section polish.
   Everything is decorative and degrades gracefully with
   prefers-reduced-motion.
========================================================= */

(function () {
    'use strict';

    var hero = document.getElementById('home');
    var stage = document.getElementById('lxStage');
    if (!hero || !stage || !hero.classList.contains('lx-hero')) return;

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
        stage.style.setProperty('--lx-scale', clamp(w / 560, .5, 1.1).toFixed(3));
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

    var canvas = $('lxNet');
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

    var spot = $('lxSpot');
    var scene = $('lxScene');
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
        hero.style.setProperty('--lx-sy', s.toFixed(1));
        hero.style.setProperty('--lx-copy-o', clamp(1 - s / (h * .8), 0, 1).toFixed(3));
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
       6. LIVE ROUTE MAP — trucks travelling along routes
    ===================================================== */

    var stat = function (id, to, delay) {
        var el = $(id);
        if (el) countUp(el, to, 1700, delay);
        return el;
    };

    function countUp(el, to, duration, delay) {
        if (reduce) { el.textContent = to.toLocaleString('en-US'); return; }
        setTimeout(function () {
            var start = performance.now();
            (function step(now) {
                var t = clamp((now - start) / duration, 0, 1);
                el.textContent = Math.round(to * easeOut(t)).toLocaleString('en-US');
                if (t < 1) requestAnimationFrame(step);
            })(start);
        }, delay);
    }

    var routesEl = stat('lxRoutes', 24, 1200);
    var parcelsEl = stat('lxParcels', 1284, 1300);
    stat('lxHubs', 12, 1400);

    var parcels = 1284;
    function bump(el) {
        if (!el) return;
        el.classList.remove('is-bump');
        void el.offsetWidth;
        el.classList.add('is-bump');
    }
    if (parcelsEl && !reduce) {
        setInterval(function () {
            if (!heroVisible || document.hidden) return;
            parcels += 1 + ((Math.random() * 4) | 0);
            parcelsEl.textContent = parcels.toLocaleString('en-US');
            bump(parcelsEl);
        }, 2600);
    }

    var trucks = Array.prototype.slice.call(stage.querySelectorAll('.lx-truck')).map(function (g) {
        var route = document.getElementById(g.getAttribute('data-route'));
        if (!route) return null;
        var len = route.getTotalLength();
        route.style.strokeDasharray = len;
        route.style.strokeDashoffset = len;
        return { g: g, route: route, len: len, dur: +g.getAttribute('data-dur') || 8000, off: +g.getAttribute('data-off') || 0 };
    }).filter(Boolean);

    function placeTruck(t, p) {
        var pt = t.route.getPointAtLength(t.len * p);
        var pt2 = t.route.getPointAtLength(Math.min(t.len, t.len * p + 1.5));
        var ang = Math.atan2(pt2.y - pt.y, pt2.x - pt.x) * 180 / Math.PI;
        var flip = Math.abs(ang) > 90;
        t.g.setAttribute('transform', 'translate(' + pt.x.toFixed(2) + ' ' + pt.y.toFixed(2) + ') rotate(' + (flip ? ang + 180 : ang).toFixed(1) + ')' + (flip ? ' scale(-1 1)' : ''));
        t.route.style.strokeDashoffset = (t.len * (1 - p)).toFixed(2);
    }

    if (reduce) {
        trucks.forEach(function (t) { placeTruck(t, .6); });
    } else if (trucks.length) {
        var t0 = performance.now() + 1100;
        (function mapLoop(now) {
            requestAnimationFrame(mapLoop);
            if (!heroVisible || document.hidden) return;
            var el = now - t0;
            if (el < 0) return;
            trucks.forEach(function (t) {
                var cycle = t.dur + 900;
                var k = (el + t.off) % cycle;
                placeTruck(t, k >= t.dur ? 1 : easeInOut(k / t.dur));
            });
        })(t0);
    }

    /* =====================================================
       7. PARCEL SCAN (QR + scan line)
    ===================================================== */

    var qr = $('lxQr');
    var scan = $('lxScan');
    var scanState = $('lxScanState');
    if (qr) {
        var line = qr.querySelector('.lx-scanline');
        var corner = function (r, c) { return (r < 3 && c < 3) || (r < 3 && c > 3) || (r > 3 && c < 3); };
        for (var r = 0; r < 7; r++) {
            for (var c = 0; c < 7; c++) {
                var cell = document.createElement('u');
                if (!corner(r, c) && r !== 3 && Math.random() < .42) cell.className = 'off';
                if ((r === 1 || r === 5) && (c === 1 || c === 5) && corner(r, c)) cell.className = 'off';
                qr.insertBefore(cell, line);
            }
        }
    }
    if (scan && !reduce) {
        var scanning = true;
        var flip = function () {
            if (heroVisible && !document.hidden) {
                scanning = !scanning;
                scan.classList.toggle('is-done', !scanning);
                if (scanState) scanState.textContent = scanning ? 'Scanning…' : 'Scanned';
            }
            setTimeout(flip, scanning ? 2700 : 1900);
        };
        setTimeout(flip, 3000);
    } else if (scan) {
        scan.classList.add('is-done');
        if (scanState) scanState.textContent = 'Scanned';
    }

    /* =====================================================
       8. RIDER PROGRESS + PARTNER STATUS CHIP
    ===================================================== */

    var barFill = $('lxBarFill');
    var etaEl = $('lxEta');
    var riderState = $('lxRiderState');
    var lastRider = '';
    function setRider(p) {
        if (barFill) barFill.style.width = (p * 100).toFixed(1) + '%';
        var label = p >= .995 ? 'Delivered ✓' : p < .08 ? 'Rider picked up' : 'Out for delivery';
        if (riderState && label !== lastRider) { riderState.textContent = label; lastRider = label; }
        if (etaEl) etaEl.textContent = p >= .995 ? 'Delivered' : 'ETA ' + Math.max(1, Math.round(12 * (1 - p))) + ' min';
    }
    if (reduce) {
        setRider(.62);
    } else {
        var r0 = performance.now() + 1500;
        setRider(0);
        (function riderLoop(now) {
            requestAnimationFrame(riderLoop);
            if (!heroVisible || document.hidden) return;
            var el = now - r0;
            if (el < 0) return;
            var t = el % 9000;
            setRider(t >= 7200 ? 1 : easeInOut(t / 7200));
        })(r0);
    }

    var partner = $('lxPartner');
    var partnerLabel = $('lxPartnerLabel');
    if (partner && partnerLabel) {
        var labels = ['Application sent', 'Under review', 'Partner approved ✓'];
        var step = reduce ? 2 : 0;
        var paint = function () {
            partner.setAttribute('data-step', step);
            partnerLabel.textContent = labels[step];
        };
        paint();
        if (!reduce) {
            setInterval(function () {
                if (!heroVisible || document.hidden) return;
                step = (step + 1) % 3;
                paint();
            }, 2300);
        }
    }

    /* =====================================================
       9. MAGNETIC BUTTONS
    ===================================================== */

    if (finePointer && !reduce) {
        Array.prototype.forEach.call(hero.querySelectorAll('.lx-magnetic'), function (btn) {
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

    /* =====================================================
       10. SECTION POLISH — card spotlight, scroll-lit steps, CTA
    ===================================================== */

    Array.prototype.forEach.call(document.querySelectorAll('.logistics-feature'), function (card, i) {
        if (!reduce) {
            card.style.transitionDelay = (i * 110) + 'ms';
            setTimeout(function () { card.style.transitionDelay = ''; }, 1800 + i * 110);
        }
        if (finePointer) {
            card.addEventListener('pointermove', function (e) {
                var r = card.getBoundingClientRect();
                card.style.setProperty('--mx', (e.clientX - r.left).toFixed(0) + 'px');
                card.style.setProperty('--my', (e.clientY - r.top).toFixed(0) + 'px');
            }, { passive: true });
        }
    });

    var stepsWrap = document.querySelector('.logistics-steps');
    var stepEls = stepsWrap ? Array.prototype.slice.call(stepsWrap.querySelectorAll('.logistics-step')) : [];
    function updateSteps() {
        if (!stepsWrap) return;
        var r = stepsWrap.getBoundingClientRect();
        var mid = window.innerHeight * .62;
        var prog = clamp((mid - r.top) / Math.max(1, r.height), 0, 1);
        stepsWrap.style.setProperty('--lx-prog', prog.toFixed(3));
        stepEls.forEach(function (el) {
            var sr = el.getBoundingClientRect();
            el.classList.toggle('is-lit', sr.top + sr.height * .35 < mid);
        });
    }
    if (stepsWrap) {
        updateSteps();
        window.addEventListener('scroll', updateSteps, { passive: true });
        window.addEventListener('resize', updateSteps, { passive: true });
    }

    /* CTA buttons reuse the existing login / signup handlers of the page */
    var ctaSignup = $('lxCtaSignup');
    var ctaLogin = $('lxCtaLogin');
    if (ctaSignup) ctaSignup.addEventListener('click', function () {
        var b = $('logisticsSignup'); if (b) b.click();
    });
    if (ctaLogin) ctaLogin.addEventListener('click', function () {
        var b = $('logisticsLogin'); if (b) b.click();
    });
})();
