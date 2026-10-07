/**
 * Scroll mode: sections reveal once as they scroll into view (see
 * animations.js), with a little scroll-linked depth on the decoration.
 */
import { animateReveal, prepareReveal, canAnimate } from './animations.js';

export function initScrollMode(sections) {
  // the envelope covers the page, so always start the story at the top
  if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
  window.scrollTo(0, 0);

  const [cover, ...rest] = sections;
  sections.forEach(prepareReveal);

  return {
    /** Called by the opening sequence as the envelope lifts away. */
    revealCover() {
      animateReveal(cover);
      observeSections(rest);
      if (canAnimate) initParallax();
    },
  };
}

function observeSections(sections) {
  if (!('IntersectionObserver' in window)) {
    sections.forEach((section) => animateReveal(section));
    return;
  }
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      observer.unobserve(entry.target);
      animateReveal(entry.target);
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -6% 0px' });
  sections.forEach((section) => observer.observe(section));
}

/**
 * Gentle scroll-linked depth (transform only, one rAF-throttled listener):
 *  - the corner flowers and the ghost emblem on the invitation card shift a few
 *    pixels as they cross the screen
 *  - the embossed sprays on the paper background drift slower than the page
 */
function initParallax() {
  const sets = Array.from(document.querySelectorAll('.florals, .watermark'));
  const backdrop = document.querySelector('.paper-backdrop');

  let viewport = window.innerHeight;
  let queued = false;

  function update() {
    queued = false;
    sets.forEach((set) => {
      const rect = set.getBoundingClientRect();
      if (rect.bottom < -100 || rect.top > viewport + 100) return;
      const fromCentre = rect.top + rect.height / 2 - viewport / 2;
      const strong = set.classList.contains('florals');
      const offset = Math.max(strong ? -14 : -10, Math.min(strong ? 14 : 10, fromCentre * (strong ? -0.03 : 0.04)));
      set.style.setProperty('--parallax', offset.toFixed(1));
    });
    if (backdrop) {
      const range = document.documentElement.scrollHeight - viewport;
      const progress = range > 0 ? window.scrollY / range : 0;
      backdrop.style.setProperty('--bg-shift', ((0.5 - progress) * 70).toFixed(1));   // about +-35px over the whole page
    }
  }
  function queue() {
    if (queued) return;
    queued = true;
    requestAnimationFrame(update);
  }

  window.addEventListener('scroll', queue, { passive: true });
  window.addEventListener('resize', () => { viewport = window.innerHeight; queue(); });
  update();
}
