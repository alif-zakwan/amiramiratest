/**
 * Shared motion vocabulary, used by the opening, scroll mode and page
 * mode alike. Everything targets the data-anim hooks in the markup
 * (see includes/components.php), never specific wording.
 *
 *   card   → rises and settles
 *   floral → blooms in slowly after the card (low emphasis)
 *   item   → content lines, in three strengths set by data-emph:
 *              focus  couple's names: rise + settle + soft focus (highest)
 *              high   key wedding info: rise + settle
 *              (none) everything else: a short fade + rise
 *            dividers draw outward from the centre
 *   drift  → a single blossom drifting across the card
 *
 * Only transform and opacity are animated (plus a small blur on the names),
 * so it stays smooth on phones.
 */

const gsap = window.gsap;

export const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
export const canAnimate = Boolean(gsap) && !reducedMotion;

const parts = (section) => ({
  card: section.querySelector('[data-anim="card"]'),
  florals: section.querySelectorAll('[data-anim="floral"]'),
  items: section.querySelectorAll('[data-anim="item"]'),
  drift: section.querySelector('[data-anim="drift"]'),
});

/** How strongly each kind of line moves. gap = delay before the next line starts. */
const EMPHASIS = {
  focus: { y: 18, scale: 0.97, blur: 4, duration: 1.1, gap: 0.17 },
  high:  { y: 16, scale: 1,    blur: 0, duration: 0.95, gap: 0.12 },
  base:  { y: 12, scale: 1,    blur: 0, duration: 0.8,  gap: 0.09 },
};

/** Hide a section's animated parts so they can be revealed later. */
export function prepareReveal(section) {
  if (!canAnimate) return;
  const { card, florals, items } = parts(section);
  gsap.set([card, ...florals, ...items].filter(Boolean), { autoAlpha: 0 });
}

/**
 * Reveal a section: card, then florals, then content lines one after
 * another. `enterX` (−1 / 0 / 1) slides the card in sideways, for page turns.
 */
export function animateReveal(section, { enterX = 0 } = {}) {
  const { card, florals, items, drift } = parts(section);
  if (!canAnimate) {
    if (gsap) gsap.set([card, ...florals, ...items].filter(Boolean), { clearProps: 'all' });
    return null;
  }

  const tl = gsap.timeline();
  if (card) {
    tl.fromTo(card,
      { autoAlpha: 0, y: enterX ? 0 : 26, scale: enterX ? 1 : 0.985, xPercent: enterX * 7, rotation: enterX * 1.2 },
      { autoAlpha: 1, y: 0, scale: 1, xPercent: 0, rotation: 0, duration: 0.9, ease: 'power2.out' });
  }
  if (florals.length) {                                   // low emphasis: slow, a little later
    tl.fromTo(florals,
      { autoAlpha: 0, scale: 0.92, y: 10 },
      { autoAlpha: 1, scale: 1, y: 0, duration: 1.6, ease: 'power2.out', stagger: 0.18 },
      0.4);
  }

  let at = 0.35;
  items.forEach((el) => {
    if (el.matches('.divider')) {                         // low emphasis: draws outward
      tl.fromTo(el,
        { autoAlpha: 0, scaleX: 0.55, transformOrigin: '50% 50%' },
        { autoAlpha: 1, scaleX: 1, duration: 0.9, ease: 'power2.out', clearProps: 'transform' },
        at);
      at += 0.08;
      return;
    }
    const e = EMPHASIS[el.dataset.emph] || EMPHASIS.base;
    const from = { autoAlpha: 0, y: e.y, scale: e.scale };
    const to = { autoAlpha: 1, y: 0, scale: 1, duration: e.duration, ease: 'power2.out', clearProps: 'transform' };
    if (e.blur) {
      from.filter = `blur(${e.blur}px)`;
      to.filter = 'blur(0px)';
      to.clearProps = 'transform,filter';
    }
    tl.fromTo(el, from, to, at);
    at += e.gap;
  });

  if (drift) {
    tl.add(driftBlossom(drift, section), 1);
  }
  return tl;
}

/** Fade a section's content away before the page turns. */
export function animateLeave(section, direction) {
  const { card, florals, items } = parts(section);
  const tl = gsap.timeline();
  tl.to(items, { autoAlpha: 0, y: -6, duration: 0.3, ease: 'power1.in', stagger: 0.015 }, 0);
  tl.to(florals, { autoAlpha: 0, scale: 0.95, duration: 0.45, ease: 'power1.in' }, 0);
  if (card) {
    tl.to(card, { autoAlpha: 0, xPercent: direction * -7, rotation: direction * -1.2, duration: 0.55, ease: 'power2.in' }, 0.12);
  }
  return tl;
}

/** One blossom drifting slowly across a card, like a petal falling past. */
export function driftBlossom(el, section) {
  const width = section.offsetWidth || 320;
  const height = section.offsetHeight || 480;
  return gsap.timeline()
    .fromTo(el,
      { x: 0, y: 0, rotation: -25, autoAlpha: 0 },
      { x: width * 0.95, y: height * 0.45, rotation: 140, duration: 10, ease: 'sine.inOut' })
    .to(el, { autoAlpha: 0.9, duration: 1.4, ease: 'sine.out' }, 0)
    .to(el, { autoAlpha: 0, duration: 2, ease: 'sine.in' }, 8);
}
