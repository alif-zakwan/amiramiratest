<?php
/**
 * Renders <head> and opens <body>.
 * Expects $cfg (config array) and $guestName to already be set by index.php
 */
?>
<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?= htmlspecialchars($cfg['site_title']) ?></title>
<meta name="description" content="<?= htmlspecialchars($cfg['og_description']) ?>">

<!-- Open Graph so the link looks good when shared on WhatsApp / Telegram -->
<meta property="og:title" content="<?= htmlspecialchars($cfg['site_title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($cfg['og_description']) ?>">
<meta property="og:type" content="website">

<link rel="icon" href="data:,">

<!-- Fonts: Playfair Display (headings), Cormorant Garamond (body/labels),
     Mrs Saint Delafield (romantic script for the couple's names),
     Noto Naskh Arabic (for the Arabic greeting) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&family=Cormorant+Garamond:wght@400;500;600&family=Mrs+Saint+Delafield&family=Noto+Naskh+Arabic:wght@500;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/style.css">
</head>
<body data-guest="<?= htmlspecialchars($guestName) ?>">

<!-- Botanical corner-flourish sprite, defined once and reused via
     <use href="#corner-floral-symbol"> throughout the page (see
     includes/corner-floral.php) -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <symbol id="corner-floral-symbol" viewBox="0 0 100 100">
    <g fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round">
      <path d="M6 6c18 1 34 8 42 26" opacity=".8"/>
      <path d="M6 6c1 18 8 34 26 42" opacity=".8"/>
      <path d="M6 6c10 4 18 11 23 21" opacity=".55"/>
      <path d="M18 10c8 6 12 14 12 22" opacity=".45"/>
      <g fill="currentColor" stroke="none">
        <circle cx="9" cy="9" r="3.4" opacity=".9"/>
        <path d="M22 8c4-4 10-3 12 2-5 3-10 2-12-2z" opacity=".65"/>
        <path d="M22 8c1-5 6-8 11-6-2 5-6 8-11 6z" opacity=".65"/>
        <path d="M8 22c-4 4-3 10 2 12 3-5 2-10-2-12z" opacity=".65"/>
        <path d="M8 22c-5-1-8-6-6-11 5 2 8 6 6 11z" opacity=".65"/>
        <circle cx="30" cy="14" r="2" opacity=".5"/>
        <circle cx="14" cy="30" r="2" opacity=".5"/>
        <circle cx="38" cy="24" r="1.4" opacity=".4"/>
      </g>
    </g>
  </symbol>

  <!-- larger single flower + leaf sprite for the drifting floral
       accent (#8) — same white/gold line-art family as the corner
       flourish above, just a standalone blossom instead of a spray -->
  <symbol id="drift-sprite-symbol" viewBox="0 0 100 100">
    <g fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round">
      <path d="M50 58c0-16-6-28-6-28" opacity=".6"/>
      <path d="M44 44c-8-2-14-10-13-18 8 1 14 8 13 18z" opacity=".55"/>
      <g fill="currentColor" stroke="none">
        <circle cx="50" cy="26" r="7" opacity=".85"/>
        <ellipse cx="38" cy="30" rx="9" ry="6" transform="rotate(-35 38 30)" opacity=".7"/>
        <ellipse cx="62" cy="30" rx="9" ry="6" transform="rotate(35 62 30)" opacity=".7"/>
        <ellipse cx="42" cy="18" rx="6" ry="9" transform="rotate(-15 42 18)" opacity=".6"/>
        <ellipse cx="58" cy="18" rx="6" ry="9" transform="rotate(15 58 18)" opacity=".6"/>
        <circle cx="50" cy="24" r="3.4" opacity=".95"/>
      </g>
    </g>
  </symbol>
</svg>

<!-- floating petals layer, filled by js/main.js -->
<div class="petal-field" id="petalField" aria-hidden="true"></div>

<!-- background music control -->
<?php if (!empty($cfg['music_file'])): ?>
<audio id="bgMusic" src="<?= htmlspecialchars($cfg['music_file']) ?>" loop preload="none"></audio>
<button id="musicToggle" class="music-toggle" type="button" aria-label="Main / henti muzik" aria-pressed="false">
    <svg class="icon-note" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
        <path fill="currentColor" d="M9 18V5l12-2v13"/>
        <circle cx="6" cy="18" r="3" fill="currentColor"/>
        <circle cx="18" cy="16" r="3" fill="currentColor"/>
    </svg>
</button>
<?php endif; ?>
