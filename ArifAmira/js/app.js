/**
 * ArifAmira — Wedding Invitation
 * Entry point. Plain ES modules, no build step; GSAP core is loaded
 * from js/vendor before this file (see includes/footer.php).
 *
 *   opening.js     sealed envelope → opening sequence
 *   scroll-mode.js sections revealed as you scroll   (mode "scroll")
 *   page-mode.js   one card at a time, turned        (mode "page", off by default)
 *   animations.js  the shared reveal / leave / drift motions
 *   features.js    countdown, RSVP, music, gift, share
 */
import { initOpening } from './opening.js';
import { initScrollMode } from './scroll-mode.js';
import { initCountdown, initRsvp, initMusic, initGift, initShare } from './features.js';

const settings = JSON.parse(document.getElementById('appSettings').textContent);
const labels = settings.labels;
const sections = Array.from(document.querySelectorAll('[data-section]'));

// page mode is switched off in config; its code is only fetched if it's enabled
const experience = settings.mode === 'page'
  ? (await import('./page-mode.js')).initPageMode(sections)
  : initScrollMode(sections);
const { startMusic } = initMusic();

initOpening({
  onOpen: startMusic,
  onReveal: () => experience.revealCover(),
  onDone: () => {
    document.documentElement.classList.add('is-opened');
    const heading = sections[0].querySelector('h1');
    if (heading) {
      heading.setAttribute('tabindex', '-1');
      heading.focus({ preventScroll: true });
    }
  },
});

initCountdown();
initRsvp(labels);
initGift(labels);
initShare(labels);
