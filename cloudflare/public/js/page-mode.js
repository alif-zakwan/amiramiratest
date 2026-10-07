/**
 * Page mode: one section at a time, turned like the cards of a printed
 * invitation. Same sections and the same reveal as scroll mode; only
 * the navigation and the turn between cards are added here.
 *
 * Turn pages with the ‹ › buttons, the diamonds, a horizontal swipe,
 * or the arrow keys.
 */
import { animateReveal, animateLeave, prepareReveal, canAnimate } from './animations.js';

const SWIPE_MIN = 50;      // px of horizontal travel that counts as a swipe

export function initPageMode(sections) {
  const stage = document.getElementById('experience');
  const nav = document.getElementById('pageNav');
  const dots = document.getElementById('pageDots');
  let current = 0;
  let turning = null;
  let opened = false;

  sections.forEach(prepareReveal);
  sections[0].classList.add('is-active');

  // ---- navigation UI -------------------------------------------------
  const dotButtons = sections.map((section, index) => {
    const item = document.createElement('li');
    const button = document.createElement('button');
    button.type = 'button';
    button.setAttribute('aria-label', `${index + 1} / ${sections.length} — ${section.getAttribute('aria-label') || ''}`);
    button.addEventListener('click', () => goTo(index));
    item.appendChild(button);
    dots.appendChild(item);
    return button;
  });
  const [prevBtn, nextBtn] = nav.querySelectorAll('[data-page-step]');
  prevBtn.addEventListener('click', () => goTo(current - 1));
  nextBtn.addEventListener('click', () => goTo(current + 1));
  updateNav();

  // ---- swipe ---------------------------------------------------------
  let start = null;
  stage.addEventListener('pointerdown', (event) => {
    if (event.target.closest('input, select, textarea, iframe, [data-no-swipe]')) return;
    start = { x: event.clientX, y: event.clientY };
  });
  stage.addEventListener('pointerup', (event) => {
    if (!start) return;
    const dx = event.clientX - start.x;
    const dy = event.clientY - start.y;
    start = null;
    if (Math.abs(dx) > SWIPE_MIN && Math.abs(dx) > Math.abs(dy) * 1.3) {
      goTo(current + (dx < 0 ? 1 : -1));
    }
  });
  stage.addEventListener('pointercancel', () => { start = null; });

  // ---- keyboard ------------------------------------------------------
  document.addEventListener('keydown', (event) => {
    if (!opened || event.target.closest('input, select, textarea')) return;
    if (event.key === 'ArrowRight' || event.key === 'PageDown') goTo(current + 1);
    if (event.key === 'ArrowLeft' || event.key === 'PageUp') goTo(current - 1);
  });

  function goTo(index) {
    if (!opened || index < 0 || index >= sections.length || index === current) return;
    if (turning) turning.progress(1, false);   // finish any turn in progress first (callbacks included)

    const from = sections[current];
    const to = sections[index];
    const direction = index > current ? 1 : -1;
    current = index;
    updateNav();
    animatePageTransition(from, to, direction);
  }

  function animatePageTransition(from, to, direction) {
    const swap = () => {
      from.classList.remove('is-active');
      to.classList.add('is-active');
      const body = to.querySelector('.card__body');
      if (body) body.scrollTop = 0;
    };

    if (!canAnimate) {
      swap();
      return;
    }
    prepareReveal(to);
    turning = window.gsap.timeline({ onComplete: () => { turning = null; } })
      .add(animateLeave(from, direction))
      .add(swap)
      .add(animateReveal(to, { enterX: direction }));
  }

  function updateNav() {
    dotButtons.forEach((button, index) => {
      if (index === current) button.setAttribute('aria-current', 'step');
      else button.removeAttribute('aria-current');
    });
    prevBtn.disabled = current === 0;
    nextBtn.disabled = current === sections.length - 1;
  }

  return {
    /** Called by the opening sequence as the envelope lifts away. */
    revealCover() {
      opened = true;
      animateReveal(sections[0]);
    },
  };
}
