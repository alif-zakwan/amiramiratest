# ArifAmira on Cloudflare Pages

A second way to host the same invitation, built from the **unchanged** `ArifAmira/` folder
(nothing in there, and nothing about the Render/Docker setup, was touched). If you don't
like it, delete this `cloudflare/` folder and carry on as before.

## Why it's faster

| | Render (PHP in Docker) | Cloudflare Pages |
|---|---|---|
| First visit after quiet time | 30–50 s wake-up | none — files come from Cloudflare's nearest edge location |
| The page | built by PHP on every visit | built once, served as a static file |
| What the site contains | everything in the project folder | only the files the page uses: about 4.2 MB, most of it the song (which only downloads when the seal is tapped) |
| Fonts | fetched from Google | self-hosted (Cinzel, Montserrat, Pinyon Script, Amiri) |
| Cost | free tier sleeps | free: static files are not counted, the two endpoints are within 100,000 calls/day |

## What is in this folder

```
cloudflare/
├── build.php          builds public/ from ArifAmira/ (runs your PHP page once)
├── fonts.php          one-time: downloads the fonts into fonts/ (already done)
├── fonts/             the self-hosted fonts (open-source licence)
├── static/            _headers (caching + security), _routes.json, _redirects, 404.html
├── functions/         the two small server pieces, ported from PHP
│   ├── rsvp.php.js        = ArifAmira/rsvp.php
│   ├── guestbook.php.js   = ArifAmira/guestbook.php
│   └── _lib/store.js      = ArifAmira/includes/store.php
├── public/            WHAT CLOUDFLARE SERVES (generated; commit it)
└── .dev.vars.example  placeholders for running the Functions on your computer
```

The page still calls `/rsvp.php` and `/guestbook.php`, so the browser code is exactly the
same as before. Both endpoints give the same answers and messages as the PHP versions
(rules: spam trap, 8-second gap per visitor, duplicate guard, 60/280 character limits,
RSVPs never listed, only name + message sent to visitors).

## Deploy (about 10 minutes)

You need the code on GitHub (it is) and a free Cloudflare account — no credit card.

1. Open <https://dash.cloudflare.com> -> **Workers & Pages** -> **Create** -> **Pages** ->
   **Connect to Git** -> pick your `amiramiratest` repository.
2. Build settings:
   - **Production branch:** `main`
   - **Framework preset:** None
   - **Build command:** *(leave empty)*
   - **Build output directory:** `public`
   - **Root directory (advanced):** `cloudflare`
3. **Save and Deploy.** In a minute you get an address like `https://arifamira.pages.dev`.
4. Add your two settings (so RSVPs and wishes reach your Google Sheet):
   **Settings -> Variables and Secrets -> Add** (for *Production*, type **Secret**):
   - `RSVP_SHEET_URL` = your Google web-app URL (the long link ending in `/exec`)
   - `RSVP_SHEET_SECRET` = your password phrase from the Google script

   Then **Deployments -> the latest -> Retry deployment** so the settings take effect.
5. *(Recommended)* a safety net for when Google can't be reached, so nobody's RSVP is lost:
   **Storage & databases -> KV -> Create** a namespace (any name), then in your Pages project
   **Settings -> Bindings -> Add -> KV namespace**: variable name **`FALLBACK`**, pick that namespace.
   Without it, a guest simply sees "try again" if Google is down at that moment.
6. Open the address on your phone and check the list in "After you deploy" below.

Never put the Google URL or password in any file in this repository — only in the Cloudflare
settings above (or in `.dev.vars` on your own computer, which git ignores).

## Changing something later

The site is built from `ArifAmira/`, so you edit it the same way as always
(`ArifAmira/config/config.php`, the section files, CSS, images, the song...). Then:

```
php cloudflare/build.php
git add -A
git commit -m "Update invitation"
git push
```

Cloudflare redeploys by itself in about a minute. (`build.php` stops with a message if it finds
your Google password in anything it is about to publish.) Keep `config.local.php` as it is on your
computer — the built site never contains it.

## After you deploy — check

- The invitation opens, the seal works and the music starts.
- Leave one RSVP and one wish; both appear in your Google Sheet (RSVP tab / Ucapan Tetamu tab).
- `https://your-address/config/config.php` and `/data/rsvp.json` show "404".
- The old link `https://your-address/index.php` takes you to the invitation.

## Try it on your own computer first (optional)

```
copy cloudflare\.dev.vars.example cloudflare\.dev.vars      (then fill in the two values)
cd cloudflare
npx wrangler@4 pages dev public --kv FALLBACK
```

Open <http://localhost:8788>. This runs the same Functions Cloudflare will run.

## Differences from the PHP version (all deliberate)

- The page is built ahead of time, so a change to `config.php` needs `php cloudflare/build.php`
  before it goes live (above).
- If Google is down, entries go to the optional KV namespace instead of a local file.
- The wish list is remembered by each Cloudflare worker for 30 seconds (like the PHP temp file),
  so a brand-new wish can take up to 30 seconds to show on someone else's phone.
- The 8-second gap between wishes is tracked per Cloudflare location, which is more than
  enough to stop accidental double sends and simple spam.

## Undo

Delete the Cloudflare project (Settings -> Delete project) and/or this `cloudflare/` folder.
Render and `ArifAmira/` are unaffected either way.
