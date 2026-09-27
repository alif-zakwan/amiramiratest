/**
 * ArifAmira — Wedding Invitation
 * Vanilla JS only — no build step, no external libraries, so this
 * runs as-is on a plain PHP localhost server.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', init);

  function init() {
    lockScroll(true);
    initPetals();
    initEnvelope();
    initCountdown();
    initScrollReveal();
    initMusic();
    initRsvp();
    initGift();
    initShare();
    initFloralParallax();
  }

  /* ---------------------------------------------------------------
     Gentle scroll-driven parallax on the corner florals, layered on
     top of their existing idle sway animation (#9 general polish)
  --------------------------------------------------------------- */
  function initFloralParallax() {
    var reduceMotion = window.matchMedia &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) return;

    var sets = Array.prototype.slice.call(document.querySelectorAll('.corner-floral-set'));
    if (!sets.length) return;

    var ticking = false;
    var vh = window.innerHeight || 1;

    function update() {
      ticking = false;
      sets.forEach(function (set) {
        var rect = set.getBoundingClientRect();
        var centerOffset = (rect.top + rect.height / 2) - vh / 2;
        // small, clamped offset so it reads as gentle depth, not scroll-jank
        var offset = Math.max(-10, Math.min(10, centerOffset * -0.02));
        set.style.setProperty('--parallax-y', offset.toFixed(2));
      });
    }

    function onScroll() {
      if (!ticking) {
        window.requestAnimationFrame(update);
        ticking = true;
      }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', function () { vh = window.innerHeight || 1; onScroll(); });
    update();
  }

  /* ---------------------------------------------------------------
     Body scroll lock while the envelope is showing
  --------------------------------------------------------------- */
  function lockScroll(locked) {
    document.documentElement.style.overflow = locked ? 'hidden' : '';
  }

  /* ---------------------------------------------------------------
     Floating petals — ambient background motion
  --------------------------------------------------------------- */
  function initPetals() {
    var field = document.getElementById('petalField');
    if (!field) return;

    var count = window.innerWidth < 640 ? 10 : 16;
    var svg = '<svg viewBox="0 0 32 32"><path fill="currentColor" d="M16 2c4 4 4 10 0 14-4-4-4-10 0-14zm0 14c4 4 4 10 0 14-4-4-4-10 0-14zM2 16c4-4 10-4 14 0-4 4-10 4-14 0zm14 0c4-4 10-4 14 0-4 4-10 4-14 0z" opacity=".9"/></svg>';

    for (var i = 0; i < count; i++) {
      var petal = document.createElement('span');
      petal.className = 'petal';
      petal.innerHTML = svg;
      petal.style.left = Math.random() * 100 + 'vw';
      petal.style.setProperty('--drift', (Math.random() * 80 - 40) + 'px');
      petal.style.width = petal.style.height = (8 + Math.random() * 10) + 'px';
      petal.style.animationDuration = (10 + Math.random() * 14) + 's';
      petal.style.animationDelay = (Math.random() * -20) + 's';
      field.appendChild(petal);
    }
  }

  /* ---------------------------------------------------------------
     Envelope opening sequence
  --------------------------------------------------------------- */
  function initEnvelope() {
    var screen = document.getElementById('envelopeScreen');
    var envelope = document.getElementById('envelope');
    var seal = document.getElementById('waxSeal');
    var hint = document.getElementById('envelopeHint');
    if (!screen || !envelope || !seal) return;

    seal.addEventListener('click', openEnvelope);
    seal.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') openEnvelope();
    });

    function openEnvelope() {
      if (envelope.classList.contains('is-open')) return;
      seal.disabled = true;
      if (hint) hint.style.opacity = '0';

      envelope.classList.add('is-cracking');

      setTimeout(function () {
        envelope.classList.add('is-open');
      }, 350);

      setTimeout(function () {
        screen.classList.add('is-open');
        lockScroll(false);
        var main = document.getElementById('invitation');
        if (main) {
          var heroPanel = main.querySelector('.hero');
          if (heroPanel) {
            heroPanel.classList.add('is-visible', 'sheen-play');
          }
        }
        // start reveal check now that content is visible
        revealCheck();

        // reuse the seal's sparkle once more on the monogram frame,
        // timed to the moment the envelope has finished opening
        var monoSpark = document.getElementById('monogramSpark');
        if (monoSpark) {
          monoSpark.classList.add('is-sparking');
          monoSpark.addEventListener('animationend', function handler() {
            monoSpark.classList.remove('is-sparking');
            monoSpark.removeEventListener('animationend', handler);
          });
        }
      }, 1450);
    }
  }

  /* ---------------------------------------------------------------
     Countdown timer
  --------------------------------------------------------------- */
  function initCountdown() {
    var el = document.getElementById('countdown');
    if (!el) return;
    var target = new Date(el.dataset.target.replace(' ', 'T')).getTime();

    var days = document.getElementById('cd-days');
    var hours = document.getElementById('cd-hours');
    var mins = document.getElementById('cd-mins');
    var secs = document.getElementById('cd-secs');
    var doneMsg = document.getElementById('countdownDone');

    function tick() {
      var diff = target - Date.now();
      if (diff <= 0) {
        el.hidden = true;
        if (doneMsg) doneMsg.hidden = false;
        clearInterval(timer);
        return;
      }
      var d = Math.floor(diff / 86400000);
      var h = Math.floor((diff % 86400000) / 3600000);
      var m = Math.floor((diff % 3600000) / 60000);
      var s = Math.floor((diff % 60000) / 1000);
      days.textContent = pad(d);
      hours.textContent = pad(h);
      mins.textContent = pad(m);
      secs.textContent = pad(s);
    }
    function pad(n) { return String(n).padStart(2, '0'); }

    tick();
    var timer = setInterval(tick, 1000);
  }

  /* ---------------------------------------------------------------
     Fade-up reveal for each section as it scrolls into view
  --------------------------------------------------------------- */
  var revealItems = [];
  var revealObserver;

  function initScrollReveal() {
    revealItems = Array.prototype.slice.call(document.querySelectorAll('.reveal'));
    if (!('IntersectionObserver' in window)) {
      revealItems.forEach(function (el) { el.classList.add('is-visible'); });
      return;
    }
    revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        // toggle both ways so panels also fade out as they leave the
        // viewport, instead of only ever revealing once (unobserve
        // removed on purpose — scrolling up and down both feel alive)
        entry.target.classList.toggle('is-visible', entry.isIntersecting);

        // re-trigger the diagonal sheen sweep (#7) and drifting floral
        // sprite (#8) each time a panel re-enters view
        if (entry.isIntersecting) {
          entry.target.classList.remove('sheen-play');
          // force reflow so re-adding the class restarts the animation
          void entry.target.offsetWidth;
          entry.target.classList.add('sheen-play');

          if (entry.target.classList.contains('formal')) {
            entry.target.classList.remove('sprite-play');
            void entry.target.offsetWidth;
            entry.target.classList.add('sprite-play');
          }
        }
      });
    }, { threshold: 0.15 });

    revealItems.forEach(function (el) { revealObserver.observe(el); });
  }
  function revealCheck() {
    // nudge the observer by dispatching a scroll/resize so anything
    // already in view (e.g. the hero, right after the envelope opens)
    // gets its class immediately instead of waiting for user scroll.
    window.dispatchEvent(new Event('scroll'));
  }

  /* ---------------------------------------------------------------
     Background music toggle
  --------------------------------------------------------------- */
  function initMusic() {
    var btn = document.getElementById('musicToggle');
    var audio = document.getElementById('bgMusic');
    if (!btn || !audio) return;

    btn.addEventListener('click', function () {
      if (audio.paused) {
        audio.play().catch(function () { /* file missing or blocked — ignore */ });
        btn.setAttribute('aria-pressed', 'true');
      } else {
        audio.pause();
        btn.setAttribute('aria-pressed', 'false');
      }
    });
  }

  /* ---------------------------------------------------------------
     RSVP form — submits to rsvp.php, then re-renders the wishes wall
  --------------------------------------------------------------- */
  function initRsvp() {
    var form = document.getElementById('rsvpForm');
    if (!form) return;

    var status = document.getElementById('rsvpStatus');
    var submitBtn = document.getElementById('rsvpSubmit');

    loadWishes();

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var payload = {
        name: document.getElementById('rsvpName').value.trim(),
        attending: document.getElementById('rsvpAttending').value,
        pax: document.getElementById('rsvpPax').value,
        message: document.getElementById('rsvpMessage').value.trim()
      };

      if (!payload.name || !payload.attending) {
        setStatus('Sila lengkapkan nama dan kehadiran.', 'error');
        return;
      }

      submitBtn.disabled = true;
      submitBtn.textContent = 'Menghantar…';

      fetch('rsvp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.ok) {
            setStatus('Terima kasih! RSVP anda telah diterima.', 'ok');
            form.reset();
            renderWishes(data.wishes);
          } else {
            setStatus(data.error || 'Maaf, sesuatu tidak kena. Cuba lagi.', 'error');
          }
        })
        .catch(function () {
          setStatus('Tidak dapat menghubungi pelayan. Cuba lagi sebentar.', 'error');
        })
        .finally(function () {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Hantar';
        });
    });

    function setStatus(msg, state) {
      if (!status) return;
      status.textContent = msg;
      status.setAttribute('data-state', state);
    }

    function loadWishes() {
      fetch('rsvp.php')
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.ok) renderWishes(data.wishes);
        })
        .catch(function () { /* wishes wall is decorative — fail quietly */ });
    }

    function renderWishes(wishes) {
      var list = document.getElementById('wishesList');
      if (!list) return;
      list.innerHTML = '';

      if (!wishes || !wishes.length) {
        var empty = document.createElement('li');
        empty.className = 'wishes-list__empty';
        empty.id = 'wishesEmpty';
        empty.textContent = 'Jadilah tetamu pertama menghantar ucapan!';
        list.appendChild(empty);
        return;
      }

      wishes.slice(0, 30).forEach(function (w) {
        var li = document.createElement('li');
        var strong = document.createElement('strong');
        strong.textContent = w.name + (w.attending === 'hadir' ? ' — akan hadir' : ' — tidak dapat hadir');
        li.appendChild(strong);
        if (w.message) {
          var em = document.createElement('em');
          em.textContent = w.message;
          li.appendChild(em);
        }
        list.appendChild(li);
      });
    }
  }

  /* ---------------------------------------------------------------
     Copy bank account number
  --------------------------------------------------------------- */
  function initGift() {
    var btn = document.getElementById('copyAccBtn');
    var accEl = document.getElementById('giftAccNo');
    if (!btn || !accEl) return;

    btn.addEventListener('click', function () {
      var text = accEl.textContent.trim();
      var done = function () {
        var original = btn.textContent;
        btn.textContent = 'Disalin!';
        setTimeout(function () { btn.textContent = original; }, 1800);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done).catch(done);
      } else {
        done();
      }
    });
  }

  /* ---------------------------------------------------------------
     Share the invitation link (Web Share API with clipboard fallback)
  --------------------------------------------------------------- */
  function initShare() {
    var btn = document.getElementById('shareBtn');
    if (!btn) return;

    btn.addEventListener('click', function () {
      var url = window.location.href;
      var title = document.title;

      if (navigator.share) {
        navigator.share({ title: title, url: url }).catch(function () {});
        return;
      }
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function () {
          var original = btn.textContent;
          btn.textContent = 'Pautan disalin!';
          setTimeout(function () { btn.textContent = original; }, 1800);
        });
      }
    });
  }
})();
