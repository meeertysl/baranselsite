(function () {
    'use strict';

    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    var lerp = function (a, b, t) { return a + (b - a) * t; };

    /* ---------- Mobil menü ---------- */
    var toggle = document.querySelector('[data-nav-toggle]');
    var nav = document.querySelector('[data-nav]');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    /* ---------- Header gölgesi ---------- */
    var header = document.querySelector('.site-header');
    var onScrollHeader = function () { header && header.classList.toggle('scrolled', window.scrollY > 10); };
    window.addEventListener('scroll', onScrollHeader, { passive: true });
    onScrollHeader();

    /* ---------- Başlıkları kelimelere böl ---------- */
    document.querySelectorAll('[data-split]').forEach(function (el) {
        var words = el.textContent.trim().split(/\s+/);
        el.textContent = '';
        words.forEach(function (w, i) {
            var outer = document.createElement('span');
            outer.className = 'w';
            var inner = document.createElement('span');
            inner.textContent = w;
            inner.style.transitionDelay = (0.08 * i) + 's';
            outer.appendChild(inner);
            el.appendChild(outer);
            if (i < words.length - 1) el.appendChild(document.createTextNode(' '));
        });
    });

    /* ---------- Kaydırınca belirme ---------- */
    var revealEls = document.querySelectorAll('.reveal, .split');
    if (reduce || !('IntersectionObserver' in window)) {
        revealEls.forEach(function (el) { el.classList.add('in'); });
    } else {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        revealEls.forEach(function (el) { io.observe(el); });
    }

    /* ---------- Okuma ilerleme çubuğu ---------- */
    var progress = document.querySelector('[data-progress]');
    if (progress) {
        var prose = document.querySelector('.prose');
        var onProgress = function () {
            var top = prose.offsetTop;
            var h = prose.offsetHeight - window.innerHeight * 0.6;
            var p = Math.min(1, Math.max(0, (window.scrollY - top + window.innerHeight * 0.4) / Math.max(1, h)));
            progress.style.transform = 'scaleX(' + p + ')';
        };
        window.addEventListener('scroll', onProgress, { passive: true });
        onProgress();
    }

    if (reduce) return;

    /* ---------- Fare konumu (ortak) ---------- */
    var mouse = { x: window.innerWidth / 2, y: window.innerHeight / 2, active: false };
    window.addEventListener('mousemove', function (e) {
        mouse.x = e.clientX; mouse.y = e.clientY; mouse.active = true;
    }, { passive: true });
    window.addEventListener('mouseleave', function () { mouse.active = false; });

    /* ---------- Özel imleç ---------- */
    var cursor = document.querySelector('.cursor');
    var cursorDot = document.querySelector('.cursor-dot');
    if (finePointer && cursor && cursorDot) {
        var cx = mouse.x, cy = mouse.y, shown = false;
        var tickCursor = function () {
            cx = lerp(cx, mouse.x, 0.18);
            cy = lerp(cy, mouse.y, 0.18);
            cursor.style.transform = 'translate(' + cx + 'px,' + cy + 'px) translate(-50%,-50%)' + (cursor.classList.contains('down') ? ' scale(.8)' : '');
            cursorDot.style.transform = 'translate(' + mouse.x + 'px,' + mouse.y + 'px) translate(-50%,-50%)';
            if (!shown && mouse.active) { document.documentElement.classList.add('has-cursor'); shown = true; }
            requestAnimationFrame(tickCursor);
        };
        tickCursor();
        document.addEventListener('mouseover', function (e) {
            var t = e.target.closest('a, button, .card, .chip, input, textarea, label');
            cursor.classList.toggle('hover', !!t && !t.matches('input, textarea'));
            cursor.classList.toggle('text', !!t && t.matches('input, textarea'));
        });
        document.addEventListener('mousedown', function () { cursor.classList.add('down'); });
        document.addEventListener('mouseup', function () { cursor.classList.remove('down'); });
    }

    /* ---------- Hero: ışık noktası ve paralaks ---------- */
    var hero = document.querySelector('[data-hero]');
    if (hero) {
        var depthEls = hero.querySelectorAll('[data-depth]');
        var px = 0, py = 0;
        var tickHero = function () {
            var r = hero.getBoundingClientRect();
            var tx = mouse.x - r.left, ty = mouse.y - r.top;
            px = lerp(px, (tx / r.width - 0.5), 0.06);
            py = lerp(py, (ty / r.height - 0.5), 0.06);
            hero.style.setProperty('--sx', (tx / r.width * 100) + '%');
            hero.style.setProperty('--sy', (ty / r.height * 100) + '%');
            depthEls.forEach(function (el) {
                var d = parseFloat(el.getAttribute('data-depth')) * 1000;
                el.style.transform = 'translate(' + (-px * d) + 'px,' + (-py * d) + 'px)';
            });
            requestAnimationFrame(tickHero);
        };
        tickHero();
    }

    /* ---------- Kartlarda 3B eğilme ve parlama ---------- */
    if (finePointer) {
        document.querySelectorAll('.tilt').forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                var r = card.getBoundingClientRect();
                var x = (e.clientX - r.left) / r.width;
                var y = (e.clientY - r.top) / r.height;
                card.style.setProperty('--mx', (x * 100) + '%');
                card.style.setProperty('--my', (y * 100) + '%');
                if (card.classList.contains('in')) {
                    card.style.transform = 'perspective(900px) rotateX(' + ((0.5 - y) * 8) + 'deg) rotateY(' + ((x - 0.5) * 10) + 'deg) translateY(-6px)';
                }
            });
            card.addEventListener('mouseleave', function () { card.style.transform = ''; });
        });

        /* ---------- Mıknatıs butonlar ---------- */
        document.querySelectorAll('.magnetic').forEach(function (btn) {
            var inner = btn.querySelector('span');
            btn.addEventListener('mousemove', function (e) {
                var r = btn.getBoundingClientRect();
                var dx = e.clientX - (r.left + r.width / 2);
                var dy = e.clientY - (r.top + r.height / 2);
                btn.style.transform = 'translate(' + dx * 0.25 + 'px,' + dy * 0.35 + 'px)';
                if (inner) inner.style.transform = 'translate(' + dx * 0.12 + 'px,' + dy * 0.15 + 'px)';
            });
            btn.addEventListener('mouseleave', function () {
                btn.style.transform = '';
                if (inner) inner.style.transform = '';
            });
        });
    }

    /* ---------- Arka plan parçacık ağı ---------- */
    var canvas = document.getElementById('bg-canvas');
    if (canvas && canvas.getContext) {
        var ctx = canvas.getContext('2d');
        var dpr = Math.min(window.devicePixelRatio || 1, 2);
        var W, H, dots = [];
        var COUNT = window.innerWidth < 700 ? 28 : 60;
        var LINK = 140;
        var resize = function () {
            W = canvas.width = window.innerWidth * dpr;
            H = canvas.height = window.innerHeight * dpr;
            canvas.style.width = window.innerWidth + 'px';
            canvas.style.height = window.innerHeight + 'px';
        };
        var seed = function () {
            dots = [];
            for (var i = 0; i < COUNT; i++) {
                dots.push({
                    x: Math.random() * W, y: Math.random() * H,
                    vx: (Math.random() - 0.5) * 0.25 * dpr, vy: (Math.random() - 0.5) * 0.25 * dpr,
                    r: (1 + Math.random() * 1.8) * dpr,
                    warm: Math.random() < 0.25
                });
            }
        };
        resize(); seed();
        window.addEventListener('resize', function () { resize(); seed(); });

        var visible = true;
        document.addEventListener('visibilitychange', function () { visible = !document.hidden; });

        var draw = function () {
            if (visible) {
                ctx.clearRect(0, 0, W, H);
                var mx = mouse.x * dpr, my = mouse.y * dpr;
                for (var i = 0; i < dots.length; i++) {
                    var d = dots[i];
                    if (mouse.active) {
                        var ddx = d.x - mx, ddy = d.y - my;
                        var dist = Math.sqrt(ddx * ddx + ddy * ddy);
                        var radius = 160 * dpr;
                        if (dist < radius && dist > 0) {
                            var f = (radius - dist) / radius * 0.6;
                            d.vx += ddx / dist * f * 0.4;
                            d.vy += ddy / dist * f * 0.4;
                        }
                    }
                    d.vx *= 0.985; d.vy *= 0.985;
                    var speed = Math.sqrt(d.vx * d.vx + d.vy * d.vy);
                    if (speed < 0.08 * dpr) { d.vx += (Math.random() - 0.5) * 0.05; d.vy += (Math.random() - 0.5) * 0.05; }
                    d.x += d.vx; d.y += d.vy;
                    if (d.x < 0) d.x = W; if (d.x > W) d.x = 0;
                    if (d.y < 0) d.y = H; if (d.y > H) d.y = 0;
                }
                for (i = 0; i < dots.length; i++) {
                    for (var j = i + 1; j < dots.length; j++) {
                        var a = dots[i], b = dots[j];
                        var dx = a.x - b.x, dy = a.y - b.y;
                        var dd = dx * dx + dy * dy;
                        var max = LINK * dpr;
                        if (dd < max * max) {
                            var alpha = (1 - Math.sqrt(dd) / max) * 0.35;
                            ctx.strokeStyle = 'rgba(31,95,91,' + alpha + ')';
                            ctx.lineWidth = dpr * 0.8;
                            ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
                        }
                    }
                }
                for (i = 0; i < dots.length; i++) {
                    d = dots[i];
                    ctx.fillStyle = d.warm ? 'rgba(200,116,61,.75)' : 'rgba(31,95,91,.7)';
                    ctx.beginPath(); ctx.arc(d.x, d.y, d.r, 0, Math.PI * 2); ctx.fill();
                }
                if (mouse.active) {
                    for (i = 0; i < dots.length; i++) {
                        d = dots[i];
                        var qx = d.x - mx, qy = d.y - my;
                        var q = Math.sqrt(qx * qx + qy * qy);
                        var lim = 200 * dpr;
                        if (q < lim) {
                            ctx.strokeStyle = 'rgba(200,116,61,' + ((1 - q / lim) * 0.5) + ')';
                            ctx.lineWidth = dpr;
                            ctx.beginPath(); ctx.moveTo(d.x, d.y); ctx.lineTo(mx, my); ctx.stroke();
                        }
                    }
                }
            }
            requestAnimationFrame(draw);
        };
        draw();
    }
})();
