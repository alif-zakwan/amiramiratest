# Save RSVPs and guestbook wishes in a Google Sheet

About 10 minutes, one time. Every RSVP then lands in a spreadsheet the
couple can open on their phone, and nothing is lost when the website
redeploys. Tabs in the sheet:

- **RSVP** — name, attending, number of guests (private, never shown on the site)
- **Ringkasan** — totals: how many people are coming
- **Ucapan Tetamu** — the guestbook wishes shown on the invitation

## 1. Create the sheet and the script

1. Go to <https://sheets.new> (sign in with the Google account that should own the guest list).
   Name it e.g. **Amira & Arif — RSVP**.
2. Menu **Extensions → Apps Script**.
3. Delete the code in the editor, then paste the whole of `Code.gs`
   (in this folder).
4. Near the top, change `SECRET` to a long random phrase, e.g.
   `kapal-terbang-biru-7391-limau-nipis-ceria`. Keep it private.
5. Click the **Save** icon.

## 2. Publish it as a web app

1. Click **Deploy → New deployment**.
2. Click the gear icon next to "Select type" → **Web app**.
3. Set:
   - **Execute as:** *Me*
   - **Who has access:** *Anyone*
   (The script rejects any request that doesn't carry your secret.)
4. Click **Deploy**. Google asks you to **authorize access**: choose your
   account → *Advanced* → *Go to … (unsafe)* → *Allow*. (It says "unsafe"
   only because you wrote the script yourself.)
5. Copy the **Web app URL** (it ends in `/exec`).

## 3. Connect the website

**Local / XAMPP** — edit `ArifAmira/config/config.php`:

```php
'rsvp_sheet' => [
    'url'    => 'https://script.google.com/macros/s/.../exec',
    'secret' => 'the same phrase you put in Code.gs',
],
```

**Render** — don't put the secret in git. In the Render dashboard open the
service → **Environment** → add two variables:

| Key | Value |
|---|---|
| `RSVP_SHEET_URL` | the web-app URL |
| `RSVP_SHEET_SECRET` | the same phrase as in `Code.gs` |

Render redeploys automatically.

## 4. Test

1. Open the invitation, submit an RSVP.
2. Open the sheet: a tab called **RSVP** now has the entry (the tab and
   its headings are created on the first RSVP). The **Ringkasan** tab
   shows the totals.
3. In **Ucapan Tetamu** on the invitation, leave a wish. A tab called
   **Ucapan Tetamu** appears in the sheet with it, and the wish shows up
   as a note on the invitation.

## Already set this up before? Update the script (2 minutes)

The guestbook needs the newer `Code.gs`. Until you update it, wishes are
still saved (on the website server) but not in the sheet, and nothing is
written to the wrong tab.

1. In the sheet: **Extensions → Apps Script**.
2. Replace all the code with the new `Code.gs` and set `SECRET` to **the
   same phrase as before**. Save.
3. **Deploy → Manage deployments → ✏️ (edit) → Version: New version → Deploy.**
   The web-app URL stays the same, so nothing needs changing on Render or
   in `config.local.php`.

## Good to know

- **Editing the script later:** after any change to `Code.gs`, use
  **Deploy → Manage deployments → ✏️ → Version: New version → Deploy**.
  The URL stays the same.
- **Safety net:** if Google can't be reached at that moment, the RSVP or
  wish is saved on the server (`data/rsvp.json` / `data/ucapan.json`) instead
  of being lost. On Render
  free that file is wiped on redeploy, so check the sheet for gaps after
  any outage.
- **Guestbook speed:** the list of wishes refreshes at most every 30
  seconds, since Google answers more slowly than a local file.
- **Removing a wish:** delete its row in the **Ucapan Tetamu** tab; it
  disappears from the invitation within about 30 seconds.
- **Privacy:** RSVPs are never shown on the invitation. The sheet stores
  name, attendance, number of guests and time (RSVP tab), and name,
  message and time (guestbook tab) — no IP addresses. Share the sheet
  only with the couple and family.
- **Spreadsheet safety:** entries are stored as plain text, so a guest
  typing something like `=IMPORTXML(...)` can't run anything.
- **If it doesn't work on XAMPP** and the PHP error log mentions an SSL
  certificate problem: this is a common Windows/XAMPP setting, not a
  bug in the site. Download <https://curl.se/ca/cacert.pem>, save it as
  `C:\xampp\php\extras\ssl\cacert.pem`, and in `php.ini` set
  `curl.cainfo = "C:\xampp\php\extras\ssl\cacert.pem"`, then restart Apache.
