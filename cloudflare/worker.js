/**
 * Cloudflare Worker entry — used when the site is deployed as a Worker
 * (an address ending in .workers.dev) rather than as a Pages project.
 *
 * It does two things:
 *   /rsvp.php and /guestbook.php  -> the same two handlers the Pages version uses
 *                                    (functions/rsvp.php.js, functions/guestbook.php.js)
 *   everything else               -> the static site in public/ (page, css, js, images, song)
 *
 * Configuration: wrangler.jsonc in this folder.
 */
import { onRequest as rsvp } from './functions/rsvp.php.js';
import { onRequest as guestbook } from './functions/guestbook.php.js';

export default {
  async fetch(request, env, ctx) {
    const { pathname } = new URL(request.url);
    if (pathname === '/rsvp.php') return rsvp({ request, env, ctx });
    if (pathname === '/guestbook.php') return guestbook({ request, env, ctx });
    return env.ASSETS.fetch(request);
  },
};
