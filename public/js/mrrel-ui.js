/* MrRel UI layer: page progress, scroll reveal, count-up, tilt, spotlight, ripple */
(function () {
  var d = document, root = d.documentElement;
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var canHover = window.matchMedia && matchMedia('(hover: hover)').matches;
  root.classList.add('js');

  function ready(fn) { d.readyState !== 'loading' ? fn() : d.addEventListener('DOMContentLoaded', fn); }

  ready(function () {
    /* top progress bar */
    var bar = d.createElement('div'); bar.id = 'pbar'; d.body.appendChild(bar);
    function setBar(w) { bar.style.opacity = '1'; bar.style.width = w + '%'; }
    setBar(35);
    window.addEventListener('load', function () { setBar(100); setTimeout(function () { bar.style.opacity = '0'; }, 450); });

    /* scroll reveal with stagger */
    var els = [].slice.call(d.querySelectorAll('.mn > h1, .mn > .m, .mn > .stb, .mn .card, body > .card'));
    if ('IntersectionObserver' in window && els.length) {
      els.forEach(function (el) { el.classList.add('rv'); });
      var io = new IntersectionObserver(function (entries) {
        var batch = entries.filter(function (e) { return e.isIntersecting; });
        batch.forEach(function (e, i) {
          var el = e.target; io.unobserve(el);
          el.style.setProperty('--d', (i * 80) + 'ms');
          requestAnimationFrame(function () { el.classList.add('in'); });
          setTimeout(function () { el.classList.remove('rv', 'in'); el.style.removeProperty('--d'); }, 950 + i * 80);
        });
      }, { threshold: 0.06, rootMargin: '0px 0px -3% 0px' });
      els.forEach(function (el) { io.observe(el); });
      setTimeout(function () { els.forEach(function (el) { el.classList.add('in'); }); }, 2800); /* safety net */
    }

    /* count-up for big numbers like 12, 1,250.00 or R299.00 */
    if (!reduce && 'IntersectionObserver' in window) {
      var co = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          if (!e.isIntersecting) return;
          var el = e.target; co.unobserve(el);
          var original = el.textContent, m = original.trim().match(/^(R\s?)?(\d[\d,\s]*)(?:\.(\d+))?$/);
          if (!m) return;
          var target = parseFloat(m[2].replace(/[,\s]/g, '') + (m[3] ? '.' + m[3] : ''));
          var dec = m[3] ? m[3].length : 0, comma = m[2].indexOf(',') > -1, prefix = m[1] || '', t0 = null;
          if (!isFinite(target) || target === 0) return;
          function fmt(v) {
            return prefix + (comma ? v.toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec }) : v.toFixed(dec));
          }
          function step(ts) {
            if (!t0) t0 = ts;
            var p = Math.min((ts - t0) / 1000, 1), eased = 1 - Math.pow(2, -10 * p);
            el.textContent = p < 1 ? fmt(target * eased) : original;
            if (p < 1) requestAnimationFrame(step);
          }
          requestAnimationFrame(step);
        });
      }, { threshold: 0.4 });
      d.querySelectorAll('.big').forEach(function (el) { if (!el.closest('table')) co.observe(el); });
    }

    /* cards that hold maps must not move when hovered */
    d.querySelectorAll('.card').forEach(function (c) {
      if (c.querySelector('iframe, canvas, .leaflet-container, [id*="map"]')) c.classList.add('no-lift');
    });

    /* spotlight that follows the pointer */
    if (canHover) {
      d.addEventListener('pointermove', function (e) {
        var c = e.target.closest && e.target.closest('.card');
        if (!c) return;
        var r = c.getBoundingClientRect();
        c.style.setProperty('--mx', (e.clientX - r.left) + 'px');
        c.style.setProperty('--my', (e.clientY - r.top) + 'px');
      }, { passive: true });
    }

    /* 3D tilt on stat cards and the login card */
    if (!reduce && canHover) {
      d.querySelectorAll('.g3 > .card, .g4 > .card, body > .card').forEach(function (c) {
        if (!c.matches('body > .card') && !c.querySelector('.big')) return;
        c.addEventListener('mousemove', function (e) {
          var r = c.getBoundingClientRect(), x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
          c.style.transition = 'transform .08s linear, box-shadow .4s';
          c.style.transform = 'perspective(900px) rotateX(' + ((0.5 - y) * 7) + 'deg) rotateY(' + ((x - 0.5) * 9) + 'deg) translateY(-4px)';
        });
        c.addEventListener('mouseleave', function () { c.style.transition = ''; c.style.transform = ''; });
      });
    }

    /* ripple on buttons */
    if (!reduce) {
      d.addEventListener('click', function (e) {
        var b = e.target.closest && e.target.closest('.btn');
        if (!b) return;
        var r = b.getBoundingClientRect(), s = Math.max(r.width, r.height), span = d.createElement('span');
        span.className = 'rip';
        span.style.cssText = 'width:' + s + 'px;height:' + s + 'px;left:' + (e.clientX - r.left - s / 2) + 'px;top:' + (e.clientY - r.top - s / 2) + 'px';
        b.appendChild(span); setTimeout(function () { span.remove(); }, 700);
      });
    }

    /* soft fade when leaving a page; progress bar on forms */
    d.addEventListener('click', function (e) {
      var a = e.target.closest && e.target.closest('a[href]');
      if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button) return;
      var h = a.getAttribute('href');
      if (!h || h.charAt(0) === '#' || /^(javascript|mailto|tel):/i.test(h) || a.target === '_blank' || a.hasAttribute('download')) return;
      if (a.origin !== location.origin) return;
      setBar(70); d.body.classList.add('leaving');
      setTimeout(function () { d.body.classList.remove('leaving'); }, 4000);
    });
    d.addEventListener('submit', function () { setBar(70); });
    window.addEventListener('pageshow', function () { d.body.classList.remove('leaving'); });
  });
})();
