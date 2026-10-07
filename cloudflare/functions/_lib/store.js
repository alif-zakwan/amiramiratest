/**
 * Shared helpers for the two Functions (rsvp.php and guestbook.php) — a port of
 * ArifAmira/includes/store.php and the helpers inside guestbook.php.
 *
 * Storage: the Google Sheet through the Apps Script web app (docs/google-sheets-rsvp/).
 * If Google can't be reached, an entry is kept in the optional Cloudflare KV
 * namespace bound as FALLBACK, so a submission is not lost. Without that binding
 * the guest is told to try again.
 *
 * Settings come from the environment: RSVP_SHEET_URL and RSVP_SHEET_SECRET
 * (Cloudflare dashboard -> Settings -> Variables and Secrets).
 */

export const TIME_OFFSET = '+08:00';        // Malaysia (no daylight saving), like config 'timezone'

export function settings(env) {
  const url = env.RSVP_SHEET_URL || '';
  return { url, secret: env.RSVP_SHEET_SECRET || '', useSheet: url !== '' };
}

/** Local time with the +08:00 offset, same shape as PHP date('c'). */
export function nowIso() {
  return new Date(Date.now() + 8 * 3600 * 1000).toISOString().replace(/\.\d{3}Z$/, TIME_OFFSET);
}

export function json(status, body) {
  return new Response(JSON.stringify(body), {
    status,
    headers: { 'Content-Type': 'application/json; charset=utf-8', 'Cache-Control': 'no-store' },
  });
}

/**
 * Calls the Apps Script web app for one kind of entry ('rsvp' or 'ucapan').
 * Returns the list it answers with (newest first) or null on any failure.
 * Apps Script answers a POST with a redirect, which fetch follows.
 * For kinds other than 'rsvp' the secret goes as "secret|kind", so an older script that
 * doesn't know kinds refuses it instead of filing a wish in the RSVP tab.
 */
export async function sheetCall(env, kind, payload) {
  const { url, secret } = settings(env);
  const sent = kind === 'rsvp' ? secret : `${secret}|${kind}`;
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), 15000);
  try {
    const response = payload === null
      ? await fetch(`${url}${url.includes('?') ? '&' : '?'}secret=${encodeURIComponent(sent)}`, { redirect: 'follow', signal: controller.signal })
      : await fetch(url, {
          method: 'POST',
          redirect: 'follow',
          signal: controller.signal,
          headers: { 'Content-Type': 'text/plain;charset=utf-8' },
          body: JSON.stringify({ ...payload, secret: sent, kind }),
        });
    const data = await response.json();
    if (!data || !data.ok || !Array.isArray(data.wishes) || (data.kind ?? 'rsvp') !== kind) {
      console.error(`Sheet call (${kind}) failed:`, JSON.stringify(data).slice(0, 200));
      return null;
    }
    return data.wishes;
  } catch (error) {
    console.error(`Sheet call (${kind}) failed:`, String(error));
    return null;
  } finally {
    clearTimeout(timer);
  }
}

// ---- optional KV fallback ---------------------------------------------------------

/** Stores an entry under a time-sorted key. Returns false when no KV namespace is bound. */
export async function kvAppend(env, prefix, entry) {
  if (!env.FALLBACK) return false;
  const key = `${prefix}:${String(Date.now()).padStart(15, '0')}:${Math.random().toString(36).slice(2, 8)}`;
  await env.FALLBACK.put(key, JSON.stringify(entry));
  return true;
}

/** Newest-first entries stored by kvAppend (empty when no KV namespace is bound). */
export async function kvList(env, prefix, limit = 50) {
  if (!env.FALLBACK) return [];
  const { keys } = await env.FALLBACK.list({ prefix: `${prefix}:` });
  const newest = keys.map((k) => k.name).sort().reverse().slice(0, limit);
  const values = await Promise.all(newest.map((name) => env.FALLBACK.get(name, 'json')));
  return values.filter(Boolean);
}

// ---- text handling (mirrors guestbook.php) --------------------------------------------

const CONTROL = /[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/g;
const chars = (text) => Array.from(text);              // counts characters like PHP mb_*, not UTF-16 units

export const length = (text) => chars(text).length;
export const cut = (text, max) => chars(text).slice(0, max).join('');

/** Drops control characters, keeps at most one blank line, trims, cuts to `max` characters. */
export function cleanText(text, max) {
  const tidy = String(text ?? '').replace(/\r\n/g, '\n').replace(CONTROL, '').trim().replace(/\n{3,}/g, '\n\n');
  return cut(tidy, max);
}

export async function readBody(request) {
  try {
    const body = await request.json();
    return body && typeof body === 'object' ? body : {};
  } catch {
    return {};
  }
}
