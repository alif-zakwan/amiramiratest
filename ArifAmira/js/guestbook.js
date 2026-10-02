/**
 * Ucapan Tetamu — the guestbook (markup: includes/sections/wishes.php,
 * backend: guestbook.php).
 *
 * The wishes are a stack of paper cards. The front card is readable, the
 * cards behind peek out, each with its own little tilt. You flip through
 * them with the arrows, the arrow keys, or by dragging/swiping: a short
 * swipe moves one card, a long or fast one fans through several at once.
 * A button opens a popup where guests write a wish.
 *
 * Everything the guest typed is inserted as text, never as HTML.
 */
import { canAnimate } from './animations.js';

const gsap = window.gsap;

const TILTS = [-3.2, 2.6, -1.8, 3.4, -2.4, 1.6, -3.6, 2.2];  // each card's resting tilt while it waits behind
const PEEK_DEPTH = 3;          // how many cards peek out behind the front one
const DRAG_PER_CARD = 0.55;    // dragging this fraction of a card's width moves one card
const FLICK_MS = 260;          // how far a flick carries: velocity x this
const MIN_SWIPE_PX = 36;       // a swipe at least this long always moves one card

export function initGuestbook(labels) {
  const $ = (id) => document.getElementById(id);
  const form = $('guestbookForm');
  const stack = $('gbStack');
  if (!form || !stack) return;

  const dialog = $('gbDialog');
  const cardsEl = $('gbCards');
  const stage = $('gbStage');
  const empty = $('gbEmpty');
  const counter = $('gbCounter');
  const prev = $('gbPrev');
  const next = $('gbNext');
  const status = $('gbStatus');
  const thanks = $('gbThanks');
  const submit = $('gbSubmit');
  const count = $('gbCount');
  const nameInput = form.elements.name;
  const messageInput = form.elements.message;
  const MAX = Number(messageInput.maxLength) || 280;

  let cards = [];
  let cardWidth = 300;
  let shownIndex = -1;
  const view = { pos: 0, fan: 0 };       // pos: which card is in front (fractional while moving); fan: 0..1 spread

  loadWishes();
  window.addEventListener('resize', () => { measure(); render(); });

  // =========================================================================
  //  Popup + form
  // =========================================================================
  $('gbAdd').addEventListener('click', openDialog);
  $('gbClose').addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });  // click on the dim backdrop
  dialog.addEventListener('close', () => document.documentElement.classList.remove('gb-modal-open'));

  function openDialog() {
    clearInvalid();
    updateCount();
    document.documentElement.classList.add('gb-modal-open');
    if (typeof dialog.showModal === 'function') dialog.showModal();
    else dialog.setAttribute('open', '');
  }

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
          form.reset();
          dialog.close();
          renderWishes(data.wishes, { dealNewest: true });
          say(labels.gb_ok);
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

  /** The thank-you line under the button, shown for a few seconds. */
  let thanksTimer = 0;
  function say(text) {
    thanks.textContent = text;
    thanks.setAttribute('data-state', 'ok');
    clearTimeout(thanksTimer);
    thanksTimer = setTimeout(() => { thanks.textContent = ''; }, 5000);
  }

  // =========================================================================
  //  The wishes
  // =========================================================================
  function loadWishes() {
    fetch('guestbook.php')
      .then((response) => response.json())
      .then((data) => { if (data.ok) renderWishes(data.wishes); })
      .catch(() => { /* the guestbook is a bonus — fail quietly */ });
  }

  function renderWishes(wishes, { dealNewest = false } = {}) {
    gsap && gsap.killTweensOf(view);
    cardsEl.replaceChildren();
    cards = [];
    shownIndex = -1;

    const hasWishes = Array.isArray(wishes) && wishes.length > 0;
    empty.hidden = hasWishes;
    stage.hidden = !hasWishes;
    counter.hidden = !hasWishes || wishes.length < 2;
    if (!hasWishes) return;

    wishes.forEach((wish) => {
      const note = document.createElement('li');
      note.className = 'note';

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
      cardsEl.appendChild(note);
      cards.push(note);
    });

    stack.classList.toggle('is-single', cards.length < 2);
    measure();
    view.pos = 0;
    view.fan = 0;
    render();
    if (dealNewest && canAnimate && cards.length > 1) {
      // the newest wish slides up from behind the stack into the front
      view.pos = -0.9;
      render();
      glideTo(0, { duration: 0.55 });
    }
  }

  // =========================================================================
  //  The stack: where each card goes for a given position
  // =========================================================================
  function measure() {
    cardWidth = stack.clientWidth || cardWidth;
  }

  /** d = how far this card is from the front: 0 front, 1 next behind, -1 just flipped away. */
  function place(d, i, fan) {
    if (d >= 0) {                                       // waiting behind the front card
      const depth = Math.min(d, PEEK_DEPTH + 1);
      const tilt = TILTS[i % TILTS.length];
      return {
        x: Math.sign(tilt) * depth * 5 * (1 + fan * 2),
        y: depth * 10,
        rot: tilt * Math.min(d, 1) * (1 + fan),
        scale: 1 - depth * 0.025,
        opacity: d <= PEEK_DEPTH ? 1 : Math.max(0, PEEK_DEPTH + 1 - d),
        z: 1000 - i,
      };
    }
    const away = -d;                                    // swung off to the left
    const swing = Math.min(away, 1);
    const beyond = Math.max(0, away - 1);               // cards further along fan out behind it
    return {
      x: -swing * cardWidth * 0.95 - beyond * (28 + fan * 60),
      y: -swing * 4,
      rot: -swing * 14 - beyond * (5 + fan * 9),
      scale: 1,
      // stays solid while it swings (no ghosting over the cards behind), then fades as it leaves
      opacity: Math.max(0, Math.min(1, (1 - away * (1 - fan * 0.75)) / 0.4)),
      z: 2000 + i,
    };
  }

  function render() {
    const n = cards.length;
    if (!n) return;
    cards.forEach((card, i) => {
      const p = place(i - view.pos, i, view.fan);
      card.style.transform = `translate3d(${p.x.toFixed(1)}px, ${p.y.toFixed(1)}px, 0) rotate(${p.rot.toFixed(2)}deg) scale(${p.scale.toFixed(3)})`;
      card.style.opacity = p.opacity.toFixed(3);
      card.style.zIndex = p.z;
      card.style.visibility = p.opacity < 0.01 ? 'hidden' : 'visible';
    });

    const index = currentIndex();
    counter.textContent = `${index + 1} / ${n}`;
    prev.disabled = index === 0;
    next.disabled = index === n - 1;
    if (index !== shownIndex) {                         // only the front card is read out
      shownIndex = index;
      cards.forEach((card, i) => card.setAttribute('aria-hidden', i === index ? 'false' : 'true'));
    }
  }

  const currentIndex = () => Math.min(cards.length - 1, Math.max(0, Math.round(view.pos)));

  /** Move to a card with a smooth settle (instant when motion is reduced). */
  function glideTo(target, { duration = 0.42, ease = 'power2.out' } = {}) {
    const to = Math.min(cards.length - 1, Math.max(0, target));
    gsap && gsap.killTweensOf(view);
    if (!canAnimate) {
      view.pos = to;
      view.fan = 0;
      render();
      return;
    }
    gsap.to(view, { pos: to, fan: 0, duration, ease, onUpdate: render, onComplete: render });
  }

  prev.addEventListener('click', () => glideTo(currentIndex() - 1));
  next.addEventListener('click', () => glideTo(currentIndex() + 1));
  stack.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowLeft') { event.preventDefault(); glideTo(currentIndex() - 1); }
    if (event.key === 'ArrowRight') { event.preventDefault(); glideTo(currentIndex() + 1); }
  });

  // =========================================================================
  //  Dragging / swiping (touch, pen and mouse)
  // =========================================================================
  let drag = null;

  stack.addEventListener('pointerdown', (event) => {
    if (cards.length < 2 || (event.pointerType === 'mouse' && event.button !== 0)) return;
    gsap && gsap.killTweensOf(view);
    drag = {
      id: event.pointerId,
      x0: event.clientX,
      lastX: event.clientX,
      lastT: performance.now(),
      pos0: view.pos,
      index0: currentIndex(),
      v: 0,                                             // px per ms, smoothed
      moved: false,
    };
    measure();
  });

  stack.addEventListener('pointermove', (event) => {
    if (!drag || event.pointerId !== drag.id) return;
    const dx = event.clientX - drag.x0;
    if (!drag.moved) {
      if (Math.abs(dx) < 5) return;
      drag.moved = true;
      stack.setPointerCapture(drag.id);                 // keep following the finger outside the stack
      stack.classList.add('is-dragging');
    }

    const now = performance.now();
    const dt = Math.max(1, now - drag.lastT);
    drag.v = drag.v * 0.75 + ((event.clientX - drag.lastX) / dt) * 0.25;
    drag.lastX = event.clientX;
    drag.lastT = now;

    // cards follow the finger; past either end they resist
    let pos = drag.pos0 - dx / (cardWidth * DRAG_PER_CARD);
    const last = cards.length - 1;
    if (pos < 0) pos *= 0.3;
    if (pos > last) pos = last + (pos - last) * 0.3;
    view.pos = pos;

    // the faster and further you go, the wider the fan
    const fanTarget = Math.min(1, Math.abs(drag.v) * 0.9 + Math.abs(view.pos - drag.pos0) * 0.12);
    view.fan += (fanTarget - view.fan) * 0.35;
    render();
  });

  function endDrag(event) {
    if (!drag || event.pointerId !== drag.id) return;
    const { moved, v, index0, x0, lastX } = drag;
    drag = null;
    stack.classList.remove('is-dragging');
    if (!moved) return;
    if (stack.hasPointerCapture(event.pointerId)) stack.releasePointerCapture(event.pointerId);

    // where would the flick carry us? a long or fast swipe lands several cards on
    const projected = view.pos + (-v * FLICK_MS) / (cardWidth * DRAG_PER_CARD);
    let target = Math.round(projected);
    if (target === index0 && (Math.abs(lastX - x0) > MIN_SWIPE_PX || Math.abs(v) > 0.3)) {
      target = index0 + (((lastX - x0) || v) < 0 ? 1 : -1);   // a short swipe still moves one card
    }
    const to = Math.min(cards.length - 1, Math.max(0, target));
    glideTo(to, { duration: Math.min(0.55, 0.3 + Math.abs(to - view.pos) * 0.05), ease: 'power3.out' });
  }
  stack.addEventListener('pointerup', endDrag);
  stack.addEventListener('pointercancel', endDrag);
}
