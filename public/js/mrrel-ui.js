/* MrRel UI layer: page progress, scroll reveal, count-up, tilt, spotlight, ripple */
(function () {
  var d = document, root = d.documentElement;
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var canHover = window.matchMedia && matchMedia('(hover: hover)').matches;
  root.classList.add('js');

  function ready(fn) { d.readyState !== 'loading' ? fn() : d.addEventListener('DOMContentLoaded', fn); }

  ready(function () {

    /* menu icons, chosen from each link's label */
    var P = {
      home: '<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v9.5h13V10"/>',
      place: '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
      building: '<path d="M5 21V4h9v17"/><path d="M14 9h5v12"/><path d="M8 8h3M8 12h3M8 16h3"/>',
      eye: '<path d="M2 12s3.6-6.5 10-6.5S22 12 22 12s-3.6 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="2.8"/>',
      truck: '<path d="M2 6h11v10H2z"/><path d="M13 9h4.5L21 12.5V16h-8"/><circle cx="6.5" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
      message: '<path d="M4 5h16v11H9l-5 4z"/>',
      card: '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3 10h18"/>',
      check: '<circle cx="12" cy="12" r="9"/><path d="M8 12.5l3 3 5-6"/>',
      users: '<circle cx="9" cy="8.5" r="3"/><path d="M3 19c0-3 2.7-5 6-5s6 2 6 5"/><circle cx="17" cy="9" r="2.4"/><path d="M17 14c2.5 0 4.5 1.6 4.5 4"/>',
      cog: '<circle cx="12" cy="12" r="3"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/>',
      wallet: '<path d="M4 7h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M4 7l11-3v3"/><circle cx="16" cy="13.5" r="1.2"/>',
      search: '<circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/>',
      help: '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3.5"/><path d="M5.6 5.6l3.9 3.9M14.5 14.5l3.9 3.9M18.4 5.6l-3.9 3.9M9.5 14.5l-3.9 3.9"/>',
      user: '<circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-3.6 3-6 7-6s7 2.4 7 6"/>'
    };
    function iconFor(t) {
      t = t.toLowerCase();
      if (/browse|search/.test(t)) return 'search';
      if (/find/.test(t)) return 'place';
      if (/home|overview/.test(t)) return 'home';
      if (/propert/.test(t)) return 'building';
      if (/viewing/.test(t)) return 'eye';
      if (/move|job/.test(t)) return 'truck';
      if (/message/.test(t)) return 'message';
      if (/subscription/.test(t)) return 'card';
      if (/approval/.test(t)) return 'check';
      if (/user|people/.test(t)) return 'users';
      if (/operation/.test(t)) return 'cog';
      if (/finance|money/.test(t)) return 'wallet';
      if (/support/.test(t)) return 'help';
      if (/account|business|profile/.test(t)) return 'user';
      return null;
    }
    d.querySelectorAll('.sd a, .mb a').forEach(function (a) {
      var key = iconFor(a.textContent.replace(/\d+/g, '').trim());
      if (!key || a.querySelector('.mi')) return;
      var svg = d.createElementNS('http://www.w3.org/2000/svg', 'svg');
      svg.setAttribute('viewBox', '0 0 24 24'); svg.setAttribute('class', 'mi'); svg.setAttribute('aria-hidden', 'true');
      svg.innerHTML = P[key];
      a.insertBefore(svg, a.firstChild);
    });

    /* top progress bar */
    var bar = d.createElement('div'); bar.id = 'pbar'; d.body.appendChild(bar);
    function setBar(w) { bar.style.opacity = '1'; bar.style.width = w + '%'; }
    setBar(35);
    window.addEventListener('load', function () { setBar(100); setTimeout(function () { bar.style.opacity = '0'; }, 450); });

    /* scroll reveal with stagger */
    var els = [].slice.call(d.querySelectorAll('.mn > h1, .mn > .m, .mn > .stb, .mn .card, .mn .hero, body > .card, .rvl'));
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
