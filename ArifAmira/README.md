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

## Personalising a link per guest

Add `?to=NameHere` to the URL and it appears on the envelope and in
greetings, e.g.:
```
http://localhost:8000/index.php?to=Puan+Salmah
```

## Editing the content

**You should only ever need to open one file: `config/config.php`.**
Every name, date, time, venue, contact number, quote and toggle
(RSVP on/off, gift section on/off, background music) lives there with
comments explaining each field.

## Folder structure

```
ArifAmira/
├── index.php              Entry point — wires everything together
├── rsvp.php                RSVP API (GET = list wishes, POST = submit one)
├── config/
│   └── config.php          ← EDIT THIS: all wedding details live here
├── includes/
│   ├── header.php           <head>, fonts, stylesheet, music button
│   ├── envelope.php         The wax-seal envelope cover screen
│   ├── invitation.php       All content sections (hero, formal text,
│   │                        countdown, details, map, RSVP, wishes, gift)
│   └── footer.php           Closes the page, loads main.js
├── css/
│   └── style.css            All styling & animation (one file, organised
│                             by section, see the comment banners)
├── js/
│   └── main.js               Envelope animation, countdown, scroll-reveal,
│                             RSVP fetch calls, music toggle, share/copy
├── data/
│   └── rsvp.json             RSVP submissions are appended here (auto-created)
└── assets/
    ├── images/                Drop your own photos / flower PNGs here (optional)
    └── audio/                 Drop a background-music MP3 here (optional)
```

## Dynamic / animated features included

- **Envelope intro** — wax seal cracks, the flap opens, the letter
  rises away to reveal the invitation (`js/main.js` → `initEnvelope`).
- **Live countdown** to the wedding date/time.
- **Scroll‑reveal** — each section fades up into view as guests scroll.
- **Floating petals** drifting gently in the background.
- **RSVP form** — saved to `data/rsvp.json` via `rsvp.php`, no database
  needed. Automatically re-renders below.
- **Guest wishes wall** — every RSVP message appears publicly under the
  form.
- **Copy‑to‑clipboard** for the bank account number (gift section).
- **Share‑this‑invitation** button (uses the native share sheet on
  phones, falls back to "copy link").
- **Background‑music toggle** (only shown once you add an mp3 and set
  `music_file` in the config).
- **Personalised guest name** via `?to=` in the URL.

## Notes on the design

Colours, type and layout follow the reference card you shared: deep
burgundy (`--maroon-…`) panels on cream paper, antique‑gold hairline
framing, a script font for the couple's names, `Playfair Display` for
formal headings and `Cormorant Garamond` for body text. All of this
lives at the top of `css/style.css` as CSS custom properties, so you
can retheme the whole site by changing a handful of values there.

The florals, dove/ring emblem and dividers are drawn with CSS/SVG
rather than exported PNGs, so the site works immediately with zero
image assets. See `assets/images/README.txt` if you'd like to swap in
your real Canva artwork later.
