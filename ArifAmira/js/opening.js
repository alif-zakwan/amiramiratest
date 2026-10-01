/**
 * Opening sequence (markup: includes/opening.php).
 *
 *   tap seal → seal presses and breaks in two → flaps slide apart
 *   → the emblem in its oval holds for a beat → it lifts away as the
 *   envelope fades and the cover card is revealed underneath.
 *
 * About 2.7s end to end; "Langkau" (skip) fast-forwards it.
 */
import { canAnimate } from './animations.js';

const gsap = window.gsap;

/**
 * @param {object}   hooks
 * @param {Function} hooks.onOpen    called on the tap itself (start music here —
 *                                   browsers only allow audio inside a tap)
 * @param {Function} hooks.onReveal  called when the cover should start appearing
 * @param {Function} hooks.onDone    called once the envelope is gone
 */
export function initOpening({ onOpen, onReveal, onDone }) {
  const opening = document.getElementById('opening');
  const seal = document.getElementById('openSeal');
  const skip = document.getElementById('openingSkip');
  if (!opening || !seal) {
    onReveal();
    onDone();
    return;
  }

  const part = (name) => opening.querySelectorAll(`[data-opening="${name}"]`);
  let timeline = null;

  seal.addEventListener('click', openEnvelope);
  skip.addEventListener('click', () => timeline && timeline.timeScale(6));

  function openEnvelope() {
    if (opening.classList.contains('is-opening')) return;
    opening.classList.add('is-opening');
    seal.disabled = true;
    onOpen();

    if (!canAnimate) {
      onReveal();
      finish();
      return;
    }

    skip.hidden = false;
    timeline = gsap.timeline({ onComplete: finish })
      // the seal gives under the thumb…
      .to(seal, { scale: 0.92, duration: 0.12, ease: 'power2.in' })
      .to(seal, { scale: 1.05, duration: 0.2, ease: 'power2.out' })
      .to(part('seal-letters'), { autoAlpha: 0, duration: 0.2 }, '<')
      // …breaks in two, and the flaps part
      .addLabel('split')
      .to(part('seal-left'), { xPercent: -45, yPercent: 8, rotation: -18, autoAlpha: 0, duration: 0.75, ease: 'power2.in' }, 'split')
      .to(part('seal-right'), { xPercent: 45, yPercent: 8, rotation: 18, autoAlpha: 0, duration: 0.75, ease: 'power2.in' }, 'split')
      .to(part('flap-left'), { xPercent: -104, duration: 1.3, ease: 'power3.inOut' }, 'split+=0.08')
      .to(part('flap-right'), { xPercent: 104, duration: 1.3, ease: 'power3.inOut' }, 'split+=0.08')
      .to(part('florals'), { autoAlpha: 0, scale: 1.06, duration: 0.9, ease: 'power1.inOut' }, 'split+=0.15')
      .fromTo(part('monogram'), { scale: 0.94, autoAlpha: 0 }, { scale: 1, autoAlpha: 1, duration: 1.5, ease: 'power2.out' }, 'split+=0.1')
      // a short hold on the emblem, then it lifts away into the cover
      .addLabel('settle', '+=0.1')
      .add(onReveal, 'settle')
      .to(part('monogram'), { scale: 0.86, y: -24, autoAlpha: 0, duration: 0.8, ease: 'power2.inOut' }, 'settle')
      .to(opening, { autoAlpha: 0, duration: 0.85, ease: 'power1.inOut' }, 'settle+=0.12');
  }

  function finish() {
    opening.classList.add('is-done');
    opening.setAttribute('aria-hidden', 'true');
    onDone();
  }
}
