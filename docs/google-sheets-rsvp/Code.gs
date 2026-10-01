/**
 * ArifAmira — RSVP → Google Sheet
 *
 * Paste this whole file into the Google Sheet's Apps Script editor
 * (Extensions → Apps Script), set SECRET below, then deploy as a Web app.
 * Step-by-step: see SETUP.md next to this file.
 *
 * The website's rsvp.php talks to this script:
 *   POST  { secret, name, attending, pax, message, time }  → adds a row
 *   GET   ?secret=…                                         → recent wishes
 * Both answer { ok: true, wishes: [ {time,name,attending,message} … newest first ] }
 */

// Must match "secret" in config.php (or the RSVP_SHEET_SECRET setting).
// Use a long random phrase. Never share it.
const SECRET = 'CHANGE-ME-TO-A-LONG-RANDOM-PHRASE';

const SHEET_NAME = 'RSVP';
const SUMMARY_NAME = 'Ringkasan';
const HEADERS = ['Masa', 'Nama', 'Kehadiran', 'Bilangan', 'Ucapan'];
const MAX_WISHES = 50;

function doPost(e) {
  let body;
  try {
    body = JSON.parse(e.postData.contents);
  } catch (err) {
    return reply_({ ok: false, error: 'bad request' });
  }
  if (body.secret !== SECRET) return reply_({ ok: false, error: 'forbidden' });

  // one guest at a time, so two simultaneous RSVPs never collide
  const lock = LockService.getScriptLock();
  lock.waitLock(15000);
  try {
    const sheet = sheet_();
    sheet.appendRow([
      String(body.time || new Date().toISOString()),
      String(body.name || '').slice(0, 80),
      body.attending === 'hadir' ? 'Hadir' : 'Tidak hadir',
      body.attending === 'hadir' ? Math.max(1, Math.min(6, Number(body.pax) || 1)) : 0,
      String(body.message || '').slice(0, 240),
    ]);
    return reply_({ ok: true, wishes: wishes_(sheet) });
  } finally {
    lock.releaseLock();
  }
}

function doGet(e) {
  if (!e || !e.parameter || e.parameter.secret !== SECRET) {
    return reply_({ ok: false, error: 'forbidden' });
  }
  return reply_({ ok: true, wishes: wishes_(sheet_()) });
}

/** The RSVP sheet, created (with headings and a summary tab) on first use. */
function sheet_() {
  const book = SpreadsheetApp.getActiveSpreadsheet();
  let sheet = book.getSheetByName(SHEET_NAME);
  if (sheet) return sheet;

  sheet = book.insertSheet(SHEET_NAME);
  sheet.appendRow(HEADERS);
  sheet.setFrozenRows(1);
  sheet.getRange('1:1').setFontWeight('bold');
  // Time, name, attendance and message are stored as plain text, so a
  // guest typing "=something" can never run as a spreadsheet formula.
  sheet.getRange('A:C').setNumberFormat('@');
  sheet.getRange('E:E').setNumberFormat('@');
  sheet.setColumnWidths(1, 1, 190);
  sheet.setColumnWidths(2, 1, 220);
  sheet.setColumnWidths(5, 1, 420);

  const summary = book.getSheetByName(SUMMARY_NAME) || book.insertSheet(SUMMARY_NAME);
  summary.getRange('A1:B3').setValues([
    ['Akan hadir (jumlah orang)', '=SUMIF(RSVP!C:C,"Hadir",RSVP!D:D)'],
    ['Tidak dapat hadir (jumlah RSVP)', '=COUNTIF(RSVP!C:C,"Tidak hadir")'],
    ['Jumlah RSVP diterima', '=COUNTA(RSVP!B2:B)'],
  ]);
  summary.setColumnWidth(1, 260);
  summary.getRange('A:A').setFontWeight('bold');
  return sheet;
}

function wishes_(sheet) {
  const last = sheet.getLastRow();
  if (last < 2) return [];
  return sheet.getRange(2, 1, last - 1, 5).getValues()
    .reverse()
    .slice(0, MAX_WISHES)
    .map(function (row) {
      return {
        time: String(row[0]),
        name: String(row[1]),
        attending: row[2] === 'Hadir' ? 'hadir' : 'tidak_hadir',
        message: String(row[4]),
      };
    });
}

function reply_(data) {
  return ContentService.createTextOutput(JSON.stringify(data))
    .setMimeType(ContentService.MimeType.JSON);
}
