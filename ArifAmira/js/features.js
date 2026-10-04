/**
 * The invitation's working parts — countdown, RSVP,
 * music, gift account copy, share. Behaviour is unchanged from the
 * original main.js; wording now comes from config 'labels'.
 */
import { wireDialog, openDialog } from './dialog.js';

/* ---------------------------------------------------------------
   Countdown — target is ISO-8601 with the venue's UTC offset, so it
   counts to the same moment wherever the guest is.
--------------------------------------------------------------- */
export function initCountdown() {
  const el = document.getElementById('countdownClock');
  if (!el) return;
  const target = new Date(el.dataset.target).getTime();
  const done = document.getElementById('countdownDone');
  const num = (unit) => el.querySelector(`[data-unit="${unit}"]`);
  const fields = { days: num('days'), hours: num('hours'), mins: num('mins'), secs: num('secs') };
  const pad = (n) => String(n).padStart(2, '0');
  let timer = null;

  function tick() {
    const diff = target - Date.now();
    if (Number.isNaN(diff) || diff <= 0) {
      el.hidden = true;
      if (done) done.hidden = false;
      clearInterval(timer);
      return;
    }
    fields.days.textContent = pad(Math.floor(diff / 86400000));
    fields.hours.textContent = pad(Math.floor((diff % 86400000) / 3600000));
    fields.mins.textContent = pad(Math.floor((diff % 3600000) / 60000));
    fields.secs.textContent = pad(Math.floor((diff % 60000) / 1000));
  }

  tick();
  timer = setInterval(tick, 1000);
}

/* ---------------------------------------------------------------
   RSVP form — POSTs JSON to rsvp.php (RSVPs are private; nothing is listed back)
--------------------------------------------------------------- */
export function initRsvp(labels) {
  const form = document.getElementById('rsvpForm');
  if (!form) return;

  const status = document.getElementById('rsvpStatus');
  const submit = document.getElementById('rsvpSubmit');

  const attending = form.elements.attending;
  const paxField = document.getElementById('rsvpPaxField');
  const pax = form.elements.pax;

  // number of guests only makes sense for guests who are coming
  function syncPaxField() {
    const coming = attending.value === 'hadir';
    paxField.hidden = !coming;
    pax.disabled = !coming;
  }
  attending.addEventListener('change', syncPaxField);
  form.addEventListener('reset', () => setTimeout(syncPaxField));

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    const payload = {
      name: form.elements.name.value.trim(),
      attending: form.elements.attending.value,
      pax: attending.value === 'hadir' ? pax.value : 0,
      website: form.elements.website.value,
    };

    if (!payload.name || !payload.attending) {
      setStatus(labels.rsvp_required, 'error');
      return;
    }

    submit.disabled = true;
    submit.textContent = labels.rsvp_sending;

    fetch('rsvp.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.ok) {
          setStatus(labels.rsvp_ok, 'ok');
          form.reset();
        } else {
          setStatus(data.error || labels.rsvp_failed, 'error');
        }
      })
      .catch(() => setStatus(labels.rsvp_offline, 'error'))
      .finally(() => {
        submit.disabled = false;
        submit.textContent = labels.rsvp_submit;
      });
  });

  function setStatus(message, state) {
    status.textContent = message;
    status.setAttribute('data-state', state);
  }
}

/* ---------------------------------------------------------------
   Background music — only present when config 'music_file' is set.
   startMusic() is called from the seal tap (a user gesture), which is
   what lets browsers play sound; a refusal is ignored quietly.
--------------------------------------------------------------- */
export function initMusic() {
  const button = document.getElementById('musicToggle');
  const audio = document.getElementById('bgMusic');
  if (!button || !audio) return { startMusic() {} };

  // The song begins at config 'music_start' (seconds) and, when it ends,
  // loops back to that point rather than to 0:00.
  const start = Number(audio.dataset.start) || 0;
  if (start > 0) {
    audio.addEventListener('loadedmetadata', () => {
      if (audio.duration > start) audio.currentTime = start;
    }, { once: true });
  }
  audio.addEventListener('ended', () => {
    audio.currentTime = audio.duration > start ? start : 0;
    audio.play().catch(() => button.setAttribute('aria-pressed', 'false'));
  });

  const play = () => audio.play()
    .then(() => button.setAttribute('aria-pressed', 'true'))
    .catch(() => button.setAttribute('aria-pressed', 'false'));

  button.addEventListener('click', () => {
    if (audio.paused) {
      play();
    } else {
      audio.pause();
      button.setAttribute('aria-pressed', 'false');
    }
  });

  return { startMusic: play };
}

/* ---------------------------------------------------------------
   Copy bank account number (gift section)
--------------------------------------------------------------- */
export function initGift(labels) {
  const button = document.getElementById('copyAccBtn');
  const account = document.getElementById('giftAccNo');
  if (!button || !account) return;

  button.addEventListener('click', () => {
    const confirm = () => flashLabel(button, labels.gift_copied);
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(account.textContent.trim()).then(confirm, confirm);
    } else {
      confirm();
    }
  });
}

/* ---------------------------------------------------------------
   Share the invitation (native share sheet, falls back to copy link)
--------------------------------------------------------------- */
export function initShare(labels) {
  const button = document.getElementById('shareBtn');
  if (!button) return;

  button.addEventListener('click', () => {
    const url = window.location.href;
    if (navigator.share) {
      navigator.share({ title: document.title, url }).catch(() => {});
      return;
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(() => flashLabel(button, labels.share_copied));
    }
  });
}

function flashLabel(button, text) {
  const original = button.textContent;
  button.textContent = text;
  setTimeout(() => { button.textContent = original; }, 1800);
}

/* ---------------------------------------------------------------
   Map apps — one button, "Buka di aplikasi peta".
   Android understands geo: links and shows the phone's own list of map
   apps. iPhone and computers don't, so they get a small popup with
   Google Maps / Waze / Apple Maps. (Without JavaScript the button is
   simply a Google Maps link.)
--------------------------------------------------------------- */
export function initMapApps() {
  const button = document.getElementById('mapOpen');
  const dialog = document.getElementById('mapDialog');
  if (!button || !dialog) return;

  if (/android/i.test(navigator.userAgent)) {
    button.href = button.dataset.geo;
    button.removeAttribute('target');
    return;
  }

  wireDialog(dialog, document.getElementById('mapClose'));
  button.addEventListener('click', (event) => {
    event.preventDefault();
    openDialog(dialog);
  });
  dialog.addEventListener('click', (event) => {           // choosing an app closes the popup
    if (event.target.closest('.map-choices__btn')) setTimeout(() => dialog.close(), 150);
  });
}
