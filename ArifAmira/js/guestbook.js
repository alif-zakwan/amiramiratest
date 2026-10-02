/**
 * Ucapan Tetamu — the guestbook (markup: includes/sections/wishes.php,
 * backend: guestbook.php).
 *
 * The form sends a name and a message; the wishes come back newest first
 * and are shown as paper notes in a swipeable row. Everything the guest
 * typed is inserted as text (never as HTML).
 */

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initGuestbook(labels) {
  const form = document.getElementById('guestbookForm');
  const track = document.getElementById('gbTrack');
  if (!form || !track) return;

  const status = document.getElementById('gbStatus');
  const submit = document.getElementById('gbSubmit');
  const count = document.getElementById('gbCount');
  const empty = document.getElementById('gbEmpty');
  const nav = document.getElementById('gbNav');
  const counter = document.getElementById('gbCounter');
  const prev = document.getElementById('gbPrev');
  const next = document.getElementById('gbNext');
  const nameInput = form.elements.name;
  const messageInput = form.elements.message;
  const MAX = Number(messageInput.maxLength) || 280;

  loadWishes();

  // ---- form -----------------------------------------------------------
  messageInput.addEventListener('input', updateCount);
  form.addEventListener('reset', () => setTimeout(updateCount));

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    if (submit.disabled) return;                   // already sending: ignore a double tap

    const name = nameInput.value.trim();
    const message = messageInput.value.trim();
    clearInvalid();
    if (!name) return invalid(nameInput, labels.gb_name_required);
    if (message.length < 2) return invalid(messageInput, labels.gb_message_required);

    submit.disabled = true;
    submit.textContent = labels.gb_sending;

    fetch('guestbook.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, message, website: form.elements.website.value }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.ok) {
          setStatus(labels.gb_ok, 'ok');
          form.reset();
          renderWishes(data.wishes, { highlightFirst: true });
        } else {
          setStatus(data.error || labels.gb_failed, 'error');
        }
      })
      .catch(() => setStatus(labels.gb_offline, 'error'))
      .finally(() => {
        // brief pause so a double tap can't send the same wish twice
        setTimeout(() => {
          submit.disabled = false;
          submit.textContent = labels.gb_submit;
        }, 600);
      });
  });

  function updateCount() {
    count.textContent = `${messageInput.value.length} / ${MAX}`;
  }

  function setStatus(text, state) {
    status.textContent = text;
    status.setAttribute('data-state', state);
  }

  function invalid(field, text) {
    setStatus(text, 'error');
    field.setAttribute('aria-invalid', 'true');
    field.focus();
  }

  function clearInvalid() {
    nameInput.removeAttribute('aria-invalid');
    messageInput.removeAttribute('aria-invalid');
    setStatus('', '');
  }

  // ---- wishes ---------------------------------------------------------
  function loadWishes() {
    fetch('guestbook.php')
      .then((response) => response.json())
      .then((data) => { if (data.ok) renderWishes(data.wishes); })
      .catch(() => { /* the guestbook is a bonus — fail quietly */ });
  }

  function renderWishes(wishes, { highlightFirst = false } = {}) {
    track.replaceChildren();
    const hasWishes = Array.isArray(wishes) && wishes.length > 0;
    empty.hidden = hasWishes;
    track.hidden = !hasWishes;
    nav.hidden = !hasWishes || wishes.length < 2;
    if (!hasWishes) return;

    wishes.forEach((wish, index) => {
      const note = document.createElement('li');
      note.className = 'note';
      if (highlightFirst && index === 0) note.classList.add('is-new');

      const heart = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
      heart.setAttribute('class', 'note__heart');
      heart.setAttribute('aria-hidden', 'true');
      const use = document.createElementNS('http://www.w3.org/2000/svg', 'use');
      use.setAttribute('href', '#ornament-heart');
      heart.appendChild(use);

      const text = document.createElement('p');
      text.className = 'note__text';
      text.textContent = wish.message;

      const name = document.createElement('p');
      name.className = 'note__name';
      name.textContent = wish.name;

      note.append(heart, text, name);
      track.appendChild(note);
    });

    track.scrollTo({ left: 0 });
    updateCounter();
  }

  // ---- carousel ---------------------------------------------------------
  const step = () => {
    const first = track.firstElementChild;
    if (!first) return track.clientWidth;
    const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
    return first.getBoundingClientRect().width + gap;
  };
  const scrollBehavior = reducedMotion ? 'auto' : 'smooth';

  prev.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: scrollBehavior }));
  next.addEventListener('click', () => track.scrollBy({ left: step(), behavior: scrollBehavior }));

  let queued = false;
  track.addEventListener('scroll', () => {
    if (queued) return;
    queued = true;
    requestAnimationFrame(() => { queued = false; updateCounter(); });
  }, { passive: true });

  function updateCounter() {
    const total = track.children.length;
    if (!total) return;
    const index = Math.min(total - 1, Math.max(0, Math.round(track.scrollLeft / step())));
    counter.textContent = `${index + 1} / ${total}`;
    prev.disabled = index === 0;
    next.disabled = index === total - 1;
  }
}
