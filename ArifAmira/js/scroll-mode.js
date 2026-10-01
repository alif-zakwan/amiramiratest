/**
 * Scroll mode: sections reveal once as they scroll into view, and the
 * floral sprays drift a few pixels against the scroll for depth.
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
      if (canAnimate) initFloralParallax();
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

/** Gentle parallax on the floral sets (transform only, rAF-throttled). */
function initFloralParallax() {
  const sets = Array.from(document.querySelectorAll('.florals'));
  if (!sets.length) return;

  let viewport = window.innerHeight;
  let queued = false;

  function update() {
    queued = false;
    sets.forEach((set) => {
      const rect = set.getBoundingClientRect();
      if (rect.bottom < -100 || rect.top > viewport + 100) return;
      const fromCentre = rect.top + rect.height / 2 - viewport / 2;
      const offset = Math.max(-14, Math.min(14, fromCentre * -0.03));
      set.style.setProperty('--parallax', offset.toFixed(1));
    });
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
