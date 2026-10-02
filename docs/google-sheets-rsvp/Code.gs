/**
 * ArifAmira — RSVP + guestbook → Google Sheet
 *
 * Paste this whole file into the Google Sheet's Apps Script editor
 * (Extensions → Apps Script), set SECRET below, then deploy as a Web app.
 * Step-by-step: see SETUP.md next to this file.
 *
 * The website talks to this script:
 *   RSVP       POST { secret, name, attending, pax, time }   → adds a row to the "RSVP" tab
 *   Guestbook  POST { secret|ucapan, name, message, time }   → adds a row to "Ucapan Tetamu"
 *              GET  ?secret=…|ucapan                         → recent wishes, newest first
 * RSVPs are never listed back to visitors.
 */

// Must match "secret" in config.php (or the RSVP_SHEET_SECRET setting).
// Use a long random phrase WITHOUT the "|" character. Never share it.
const SECRET = 'CHANGE-ME-TO-A-LONG-RANDOM-PHRASE';

const RSVP_SHEET = 'RSVP';
const GUESTBOOK_SHEET = 'Ucapan Tetamu';
const SUMMARY_SHEET = 'Ringkasan';
const MAX_WISHES = 50;

function doPost(e) {
  let body;
  try {
    body = JSON.parse(e.postData.contents);
  } catch (err) {
    return reply_({ ok: false, error: 'bad request' });
  }
  const auth = auth_(body.secret);
  if (!auth) return reply_({ ok: false, error: 'forbidden' });

  // one guest at a time, so two simultaneous submissions never collide
  const lock = LockService.getScriptLock();
  lock.waitLock(15000);
  try {
    if (auth.kind === 'ucapan') {
      const sheet = guestbookSheet_();
      sheet.appendRow([
        String(body.time || new Date().toISOString()),
        String(body.name || '').slice(0, 60),
        String(body.message || '').slice(0, 300),
      ]);
      return reply_({ ok: true, kind: 'ucapan', wishes: wishes_(sheet) });
    }

    rsvpSheet_().appendRow([
      String(body.time || new Date().toISOString()),
      String(body.name || '').slice(0, 80),
      body.attending === 'hadir' ? 'Hadir' : 'Tidak hadir',
      body.attending === 'hadir' ? Math.max(1, Math.min(6, Number(body.pax) || 1)) : 0,
      String(body.message || '').slice(0, 240),
    ]);
    return reply_({ ok: true, kind: 'rsvp', wishes: [] });
  } finally {
    lock.releaseLock();
  }
}

function doGet(e) {
  const auth = auth_(e && e.parameter && e.parameter.secret);
  if (!auth) return reply_({ ok: false, error: 'forbidden' });
  if (auth.kind === 'ucapan') {
    return reply_({ ok: true, kind: 'ucapan', wishes: wishes_(guestbookSheet_()) });
  }
  return reply_({ ok: true, kind: 'rsvp', wishes: [] });   // RSVPs are private
}

/** "phrase" = RSVP, "phrase|ucapan" = guestbook. Returns null if the phrase is wrong. */
function auth_(raw) {
  const match = String(raw || '').match(/^(.*)\|(ucapan)$/);
  const phrase = match ? match[1] : String(raw || '');
  if (phrase !== SECRET) return null;
  return { kind: match ? match[2] : 'rsvp' };
}

/** The RSVP sheet, created (with headings and a summary tab) on first use. */
function rsvpSheet_() {
  const book = SpreadsheetApp.getActiveSpreadsheet();
  let sheet = book.getSheetByName(RSVP_SHEET);
  if (sheet) return sheet;

  sheet = prepareSheet_(book.insertSheet(RSVP_SHEET), ['Masa', 'Nama', 'Kehadiran', 'Bilangan', 'Ucapan'], ['A:C', 'E:E']);
  sheet.setColumnWidths(1, 1, 190);
  sheet.setColumnWidths(2, 1, 220);
  sheet.setColumnWidths(5, 1, 420);

  const summary = book.getSheetByName(SUMMARY_SHEET) || book.insertSheet(SUMMARY_SHEET);
  summary.getRange('A1:B3').setValues([
    ['Akan hadir (jumlah orang)', '=SUMIF(RSVP!C:C,"Hadir",RSVP!D:D)'],
    ['Tidak dapat hadir (jumlah RSVP)', '=COUNTIF(RSVP!C:C,"Tidak hadir")'],
    ['Jumlah RSVP diterima', '=COUNTA(RSVP!B2:B)'],
  ]);
  summary.setColumnWidth(1, 260);
  summary.getRange('A:A').setFontWeight('bold');
  return sheet;
}

/** The guestbook sheet ("Ucapan Tetamu"), created on first use. */
function guestbookSheet_() {
  const book = SpreadsheetApp.getActiveSpreadsheet();
  let sheet = book.getSheetByName(GUESTBOOK_SHEET);
  if (sheet) return sheet;

  sheet = prepareSheet_(book.insertSheet(GUESTBOOK_SHEET), ['Masa', 'Nama', 'Ucapan'], ['A:C']);
  sheet.setColumnWidths(1, 1, 190);
  sheet.setColumnWidths(2, 1, 220);
  sheet.setColumnWidths(3, 1, 520);
  return sheet;
}

function prepareSheet_(sheet, headings, textRanges) {
  sheet.appendRow(headings);
  sheet.setFrozenRows(1);
  sheet.getRange('1:1').setFontWeight('bold');
  // Stored as plain text, so a guest typing "=something" can never run
  // as a spreadsheet formula.
  textRanges.forEach(function (range) { sheet.getRange(range).setNumberFormat('@'); });
  return sheet;
}

/** Guestbook rows, newest first: [{name, message}]. */
function wishes_(sheet) {
  const last = sheet.getLastRow();
  if (last < 2) return [];
  return sheet.getRange(2, 1, last - 1, 3).getValues()
    .reverse()
    .slice(0, MAX_WISHES)
    .map(function (row) {
      return { name: String(row[1]), message: String(row[2]) };
    });
}

function reply_(data) {
  return ContentService.createTextOutput(JSON.stringify(data))
    .setMimeType(ContentService.MimeType.JSON);
}
