/**
 * Guestbook ("Ucapan Tetamu") endpoint — port of ArifAmira/guestbook.php.
 * Same path (/guestbook.php), same rules, same messages.
 *
 *   GET  /guestbook.php            -> { ok:true, wishes:[ {name, message}, ... ] }  newest first
 *   POST /guestbook.php { name, message } -> saves a wish, answers with the same list
 *
 * Only name + message are ever sent to visitors.
 */
import { settings, sheetCall, kvAppend, kvList, nowIso, json, readBody, cleanText, length, whyFailed } from './_lib/store.js';

const LIST_LIMIT = 50;           // newest wishes shown
const CACHE_MS = 30_000;         // Google answers slowly, so the list is kept for 30 s
const MIN_GAP_MS = 8_000;        // per visitor, against rapid-fire spam
const NAME_MAX = 60;
const MESSAGE_MAX = 280;

// Kept in memory by this Worker instance (like the temp-file cache in the PHP version)
let cache = { at: 0, wishes: null };
const lastPost = new Map();      // visitor -> time of their last wish

/** Only what visitors may see: name + message. */
const publicWishes = (entries) => entries.slice(0, LIST_LIMIT).map((e) => ({
  name: String(e.name ?? ''),
  message: String(e.message ?? ''),
}));

/** Newest-first wishes from whichever store is active. */
async function currentWishes(env) {
  if (settings(env).useSheet) {
    if (cache.wishes && Date.now() - cache.at < CACHE_MS) return cache.wishes;
    const wishes = await sheetCall(env, 'ucapan', null);
    if (wishes !== null) {
      cache = { at: Date.now(), wishes };
      return wishes;
    }
  }
  return kvList(env, 'ucapan', LIST_LIMIT);
}

export async function onRequest({ request, env }) {
  if (request.method === 'GET') {
    return json(200, { ok: true, wishes: publicWishes(await currentWishes(env)) });
  }
  if (request.method !== 'POST') {
    return json(405, { ok: false, error: 'Method not allowed' });
  }

  const body = await readBody(request);

  // Spam trap: invisible to people, filled in by bots. Pretend it worked.
  if (String(body.website ?? '').trim() !== '') {
    return json(200, { ok: true, wishes: publicWishes(await currentWishes(env)) });
  }

  const name = cleanText(String(body.name ?? '').replace(/\s+/g, ' '), NAME_MAX);
  const message = cleanText(body.message, MESSAGE_MAX);

  if (name === '') {
    return json(422, { ok: false, error: 'Sila isi nama anda.' });
  }
  if (length(message) < 2) {
    return json(422, { ok: false, error: 'Sila tulis ucapan anda.' });
  }

  // Too soon after this visitor's last wish?
  const visitor = request.headers.get('CF-Connecting-IP') || 'unknown';
  const last = lastPost.get(visitor);
  if (last && Date.now() - last < MIN_GAP_MS) {
    return json(429, { ok: false, error: 'Sila tunggu sebentar sebelum menghantar ucapan lagi.' });
  }

  // The same wish again (double tap, refresh): accept quietly, store once.
  const same = (a, b) => String(a ?? '').trim().toLowerCase() === String(b ?? '').trim().toLowerCase();
  const recent = await currentWishes(env);
  if (recent.slice(0, 20).some((w) => same(w.name, name) && same(w.message, message))) {
    return json(200, { ok: true, wishes: publicWishes(recent) });
  }

  const entry = { name, message, time: nowIso() };
  const remember = () => {
    lastPost.set(visitor, Date.now());
    if (lastPost.size > 5000) lastPost.clear();         // never grows without bound
  };

  if (settings(env).useSheet) {
    const wishes = await sheetCall(env, 'ucapan', entry);
    if (wishes !== null) {
      cache = { at: Date.now(), wishes };
      remember();
      return json(200, { ok: true, wishes: publicWishes(wishes) });
    }
    console.error('Google Sheet unreachable, keeping the wish in KV:', name);
  }

  // Google unreachable (or no sheet set): keep the wish in KV rather than lose it
  if (await kvAppend(env, 'ucapan', { ...entry, ip: request.headers.get('CF-Connecting-IP') || '' })) {
    cache = { at: 0, wishes: null };
    remember();
    return json(200, { ok: true, wishes: publicWishes(await kvList(env, 'ucapan', LIST_LIMIT)) });
  }
  return json(500, { ok: false, error: 'Tidak dapat menyimpan ucapan. Sila cuba lagi sebentar.', why: whyFailed(env) });
}
