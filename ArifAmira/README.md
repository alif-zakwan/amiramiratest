# ArifAmira — Wedding Invitation

A dynamic, single‑page wedding invitation built with plain **PHP, HTML,
CSS and JavaScript** — no framework, no build step, no database. It
opens on a wax‑sealed envelope; tapping the seal breaks it open and
reveals the invitation.

## Running it locally

Any of these work — pick whichever you already have:

**Option A — PHP's built‑in server (simplest)**
```bash
cd ArifAmira
php -S localhost:8000
```
Then open **http://localhost:8000/**

**Option B — XAMPP / MAMP / WAMP**
Copy the whole `ArifAmira` folder into your `htdocs` (XAMPP) or
`www` (MAMP) directory, start Apache, then open
**http://localhost/ArifAmira/**

**Option C — VS Code "PHP Server" extension**
Open the `ArifAmira` folder in VS Code, right‑click `index.php` →
"PHP Server: Serve project".

> The RSVP feature writes to `data/rsvp.json`, so make sure the `data/`
> folder is writable by the web server (`chmod 775 data` on
> Mac/Linux — Windows/XAMPP usually needs no change).

## Editing the content

**You should only ever need to open one file: `config/config.php`.**
Every name, date, time, venue, contact number, quote, toggle (RSVP,
gift section, music) and every fixed label on the page (`labels`) lives
there with comments explaining each field.

## Scroll experience (page mode is switched off)

The invitation is one long scrolling card. A page-by-page version
(swipe / tap to turn cards) is built but **switched off** at the client's
request: `?mode=page` is ignored and no switcher is shown. To bring it back
for a demo, in `config/config.php` set
`'allowed_modes' => ['scroll', 'page']` (and `'show_mode_switcher' => true`
to show the small Skrol / Halaman switch); then `?mode=page` works.

## Folder structure

```
ArifAmira/
├── index.php              Entry point: header → opening → sections → footer
├── rsvp.php               RSVP API (GET = list wishes, POST = submit one)
├── .htaccess              Blocks data/, config/, includes/ from the web
├── config/
│   └── config.php         ← EDIT THIS: all wedding details + labels
├── includes/
│   ├── bootstrap.php      Loads config, ?mode=, section order, helpers
│   ├── components.php     Reusable card pieces: frame, florals, divider, date line
│   ├── header.php         <head>, fonts, ornament sprite, music + mode switch
│   ├── opening.php        The sealed envelope
│   ├── footer.php         Settings for JS, scripts (+ page-mode navigation when enabled)
│   └── sections/          One file per section, in this order:
│                          cover, countdown, invitation, details, venue,
│                          contacts, rsvp, wishes, gift (optional), closing
├── css/
│   ├── tokens.css         Colours, fonts, spacing — retheme from here
│   ├── base.css           Reset, embossed paper background, shared type
│   ├── components.css     Card frame, florals, ornaments, buttons, fields
│   ├── sections.css       Per-section content styling
│   ├── opening.css        Envelope, flaps, wax seal
│   └── modes.css          Scroll vs page layout, page navigation
├── js/
│   ├── app.js             Entry: wires opening + mode + features
│   ├── animations.js      Shared motions: reveal, leave, drifting blossom
│   ├── opening.js         Envelope opening timeline
│   ├── scroll-mode.js     Reveal-on-scroll, floral parallax
│   ├── page-mode.js       Page turns, swipe/keys/dots (loaded only if page mode is enabled) (only loaded if page mode is enabled)
│   ├── features.js        Countdown, RSVP + wishes, music, gift, share
│   └── vendor/gsap.min.js GSAP 3.13 core (bundled, no CDN)
├── data/
│   └── rsvp.json          RSVPs when no Google Sheet is set (and as a backup if Google is down; private)
└── assets/
    ├── images/            Emblem, floral sprays, paper + grain textures
    └── audio/             Drop a background-music MP3 here (optional)
```

## What happens on the page

- **Opening** — a sealed burgundy envelope addressed to the guest. Tapping
  the wax seal: the seal gives, breaks in two, the flaps slide apart,
  the emblem holds in its oval for a beat, then lifts away as the cover
  card appears (~2.7s, with a "Langkau" skip). Music starts on that tap
  if one is configured.
- **Cards** — the cover, invitation and details follow the printed card
  (maroon panel, scooped corners, white keyline, white-and-gold sprays);
  countdown, map, contacts, RSVP and wishes are ivory "insert cards".
- **Reveals** — each card rises in, its sprays bloom, then each line comes
  into focus in turn. Once per section in scroll mode; on every turn in
  page mode. A single blossom drifts across the invitation card.
- **Live countdown**, **RSVP** (saved to `data/rsvp.json`, with a hidden
  spam trap), **wishes wall**, **copy bank account**, **share** (native
  share sheet or copy link), **music toggle**..
- **Reduced motion** — if the phone asks for less motion, the envelope
  simply opens and everything is shown without animation.

## Notes on the design

Colours are sampled from the printed card (`#620909` maroon on ivory
paper); type is Pinyon Script (names), Cinzel (engraved capitals),
Montserrat (spaced-out body text) and Amiri (Arabic greeting). The floral
sprays and emblem are the client's own artwork — see
`assets/images/README.txt` for how to replace them with sharper exports.
