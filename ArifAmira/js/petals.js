/**
 * Falling petals: now and then a single blossom drifts down past the
 * page — a gentle sign of life while someone reads, not a snowstorm.
 *
 *  - one new petal every ~2.5–6 s, never more than MAX_AT_ONCE on screen
 *  - they fall mostly along the left and right edges, so they rarely cross the text
 *  - random size, start, sway, spin and speed; each is removed when it finishes,
 *    so the page never accumulates elements
 *  - nothing is spawned while the tab is hidden; none at all with "reduce motion"
 *  - only transform and opacity are animated (smooth on phones)
 */
import { canAnimate } from './animations.js';

const gsap = window.gsap;

const MAX_AT_ONCE = 4;
const SPAWN_MS = [2500, 6000];       // gap between petals (random within)
const FALL_S = [9, 15];              // seconds to cross the screen
const SIZE_PX = [16, 28];
const OPACITY = [0.4, 0.62];

const rand = (min, max) => min + Math.random() * (max - min);

export function initPetals() {
  if (!canAnimate) return;

  const layer = document.createElement('div');
  layer.className = 'petal-layer';
  layer.setAttribute('aria-hidden', 'true');
  document.body.appendChild(layer);

  let alive = 0;

  function spawn() {
    if (document.hidden || alive >= MAX_AT_ONCE) return;
    alive += 1;

    const width = window.innerWidth;
    const height = window.innerHeight;
    const size = rand(...SIZE_PX);
    const duration = rand(...FALL_S);
    const spin = (Math.random() < 0.5 ? -1 : 1) * rand(120, 280);

    // start near the left or right edge
    const startX = Math.random() < 0.5 ? rand(0.02, 0.15) * width : rand(0.85, 0.97) * width;
    const drift = rand(-45, 45);                                   // slow sideways travel while falling

    const wrap = document.createElement('div');                   // falls and drifts
    wrap.className = 'petal';
    wrap.style.width = `${size}px`;
    const img = document.createElement('img');                    // sways and spins
    img.src = 'assets/images/blossom.png';
    img.alt = '';
    img.draggable = false;
    wrap.appendChild(img);
    layer.appendChild(wrap);

    gsap.set(wrap, { x: startX, y: -size * 2, opacity: 0 });
    gsap.set(img, { rotation: rand(-40, 40), scaleX: Math.random() < 0.5 ? -1 : 1 });

    const fall = gsap.timeline({
      onComplete() {
        gsap.killTweensOf([wrap, img]);
        wrap.remove();
        alive -= 1;
      },
    });
    fall.to(wrap, { y: height + size * 2, duration, ease: 'none' }, 0)
        .to(wrap, { x: `+=${drift}`, duration, ease: 'sine.inOut' }, 0)
        .to(wrap, { opacity: rand(...OPACITY), duration: 1.4, ease: 'sine.out' }, 0)
        .to(wrap, { opacity: 0, duration: 1.6, ease: 'sine.in' }, duration - 1.6);
    gsap.to(img, { rotation: `+=${spin}`, duration, ease: 'none' });
    gsap.to(img, { x: rand(10, 22), duration: rand(1.8, 3), ease: 'sine.inOut', yoyo: true, repeat: -1 });   // sway
  }

  function schedule() {
    setTimeout(() => { spawn(); schedule(); }, rand(...SPAWN_MS));
  }

  setTimeout(spawn, 1500);          // the first one soon after the invitation opens
  schedule();
}
