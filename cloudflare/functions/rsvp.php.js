/**
 * RSVP endpoint — port of ArifAmira/rsvp.php. Same path (/rsvp.php), same rules,
 * same messages, so the page works unchanged.
 *
 *   POST /rsvp.php  { name, attending, pax }  -> saves an RSVP, answers { ok:true }
 *
 * RSVPs are private: nothing lists them back (GET -> 405).
 */
import { settings, sheetCall, kvAppend, nowIso, json, readBody, cut, whyFailed } from './_lib/store.js';

export async function onRequest({ request, env }) {
  if (request.method !== 'POST') {
    return json(405, { ok: false, error: 'Method not allowed' });
  }

  const body = await readBody(request);

  // Spam trap: the "website" field is invisible to people. Bots fill it in;
  // pretend it worked and store nothing.
  if (String(body.website ?? '').trim() !== '') {
    return json(200, { ok: true });
  }

  const name = String(body.name ?? '').trim();
  const attending = String(body.attending ?? '').trim();
  const pax = parseInt(body.pax ?? 1, 10) || 0;

  if (name === '' || !['hadir', 'tidak_hadir'].includes(attending)) {
    return json(422, { ok: false, error: 'Sila lengkapkan nama dan kehadiran.' });
  }

  const entry = {
    name: cut(name, 80),
    attending,
    pax: attending === 'hadir' ? Math.max(1, Math.min(6, pax)) : 0,
    message: '',
    time: nowIso(),
  };

  if (settings(env).useSheet && (await sheetCall(env, 'rsvp', entry)) !== null) {
    return json(200, { ok: true });
  }
  if (settings(env).useSheet) {
    console.error('Google Sheet unreachable, keeping the RSVP in KV:', entry.name);
  }

  // Google unreachable (or no sheet set): keep the RSVP in KV rather than lose it
  const ip = request.headers.get('CF-Connecting-IP') || '';
  if (await kvAppend(env, 'rsvp', { ...entry, ip })) {
    return json(200, { ok: true });
  }
  return json(500, { ok: false, error: 'Tidak dapat menyimpan RSVP. Sila cuba lagi sebentar.', why: whyFailed(env) });
}
