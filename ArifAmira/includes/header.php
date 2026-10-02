<?php
/**
 * Renders <head>, the shared SVG ornament sprite, the paper background
 * and the fixed controls (music, mode switch). Expects bootstrap.php.
 */
$fontsUrl = 'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap';
// Amiri is only used for the Arabic greeting, so request just those glyphs
$arabicFontUrl = 'https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap&text=' . rawurlencode($cfg['greeting_arabic']);
?>
<!DOCTYPE html>
<html lang="ms" class="no-js" data-mode="<?= e($mode) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#4a0707">
<title><?= e($cfg['site_title']) ?></title>
<meta name="description" content="<?= e($cfg['og_description']) ?>">

<!-- Open Graph so the link looks good when shared on WhatsApp / Telegram -->
<meta property="og:title" content="<?= e($cfg['site_title']) ?>">
<meta property="og:description" content="<?= e($cfg['og_description']) ?>">
<meta property="og:type" content="website">

<link rel="icon" href="data:,">
<script>document.documentElement.classList.replace('no-js', 'js');</script>

<!-- Fonts: Pinyon Script (couple's names) and Amiri (Arabic greeting) are
     downloaded; all other text uses Arial (a system font, see css/tokens.css) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="<?= e($fontsUrl) ?>">
<link rel="stylesheet" href="<?= e($arabicFontUrl) ?>">

<link rel="preload" as="image" href="assets/images/emblem-doves.webp">
<link rel="stylesheet" href="css/tokens.css">
<link rel="stylesheet" href="css/base.css">
<link rel="stylesheet" href="css/components.css">
<link rel="stylesheet" href="css/sections.css">
<link rel="stylesheet" href="css/opening.css">
<link rel="stylesheet" href="css/modes.css">
</head>
<body>

<!-- Shared ornaments, defined once and reused via <use href="#…"> -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
  <!-- line · diamonds · central flourish · diamonds · line (card divider) -->
  <symbol id="ornament-divider" viewBox="0 0 240 16">
    <g fill="none" stroke="currentColor" stroke-linecap="round">
      <path d="M10 8h76M154 8h76" stroke-width=".8"/>
      <path d="M120 8c-2.5-4.5-9-5-10.5-.6-1.2 3.6 3.6 5.4 5.6 2.6" stroke-width="1"/>
      <path d="M120 8c2.5-4.5 9-5 10.5-.6 1.2 3.6-3.6 5.4-5.6 2.6" stroke-width="1"/>
      <path d="M120 8c-2.5 4.5-9 5-10.5.6M120 8c2.5 4.5 9 5 10.5.6" stroke-width=".7" opacity=".7"/>
    </g>
    <g fill="currentColor">
      <path d="M3 8l3.2-2.3L9.4 8l-3.2 2.3z"/>
      <path d="M230.6 8l3.2-2.3L237 8l-3.2 2.3z"/>
      <path d="M86 8l4-2.8L94 8l-4 2.8z"/>
      <path d="M95.5 8l2.6-1.9 2.6 1.9-2.6 1.9z"/>
      <path d="M146 8l4-2.8 4 2.8-4 2.8z"/>
      <path d="M139.3 8l2.6-1.9 2.6 1.9-2.6 1.9z"/>
      <circle cx="120" cy="8" r="1.5"/>
    </g>
  </symbol>

  <!-- scroll curl that sits in each scooped corner of a card -->
  <symbol id="ornament-corner" viewBox="0 0 64 64">
    <g fill="none" stroke="currentColor" stroke-linecap="round">
      <path d="M8 44C8 24 24 8 44 8" stroke-width="1.2"/>
      <path d="M44 8c7 0 11 4.5 8.5 8.4-2 3-6.6 2-6-1.4" stroke-width="1.2"/>
      <path d="M8 44c0 7 4.5 11 8.4 8.5 3-2 2-6.6-1.4-6" stroke-width="1.2"/>
      <path d="M16 40c0-13 11-24 24-24" stroke-width=".7" opacity=".55"/>
    </g>
  </symbol>

  <!-- four-point sparkle, as on the card's frame -->
  <symbol id="ornament-star" viewBox="-6 -6 12 12">
    <path fill="currentColor" d="M0-6 1.1-1.1 6 0 1.1 1.1 0 6-1.1 1.1-6 0-1.1-1.1z"/>
  </symbol>

  <!-- ornate double oval that frames the emblem during the opening -->
  <symbol id="ornament-oval" viewBox="0 0 160 200">
    <g fill="none" stroke="currentColor">
      <ellipse cx="80" cy="100" rx="70" ry="92" stroke-width="1"/>
      <ellipse cx="80" cy="100" rx="64" ry="86" stroke-width=".6" stroke-dasharray="1 3.5" opacity=".8"/>
    </g>
    <g fill="currentColor" opacity=".9">
      <path d="M80 8c-9 0-15 5-15 5s6-2 15-2 15 2 15 2-6-5-15-5z"/>
      <path d="M50 14c6-6 18-9 30-9s24 3 30 9c-8-3-19-5-30-5s-22 2-30 5z"/>
      <circle cx="80" cy="3.5" r="1.6"/>
      <path d="M80 192c-9 0-15-5-15-5s6 2 15 2 15-2 15-2-6 5-15 5z"/>
      <path d="M50 186c6 6 18 9 30 9s24-3 30-9c-8 3-19 5-30 5s-22-2-30-5z"/>
      <circle cx="80" cy="196.5" r="1.6"/>
    </g>
  </symbol>
</svg>

<div class="paper-backdrop" aria-hidden="true"></div>

<?php if (!empty($cfg['music_file'])): ?>
<audio id="bgMusic" src="<?= e($cfg['music_file']) ?>" loop preload="none"></audio>
<button id="musicToggle" class="music-toggle" type="button" aria-label="<?= t('music_aria') ?>" aria-pressed="false">
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
        <path fill="currentColor" d="M9 18V5l12-2v13"/>
        <circle cx="6" cy="18" r="3" fill="currentColor"/>
        <circle cx="18" cy="16" r="3" fill="currentColor"/>
    </svg>
</button>
<?php endif; ?>

<?php if ($showModeSwitcher): ?>
<nav class="mode-switch" aria-label="<?= t('mode_label') ?>">
    <a href="<?= e(mode_url('scroll')) ?>"<?= $mode === 'scroll' ? ' aria-current="page"' : '' ?>><?= t('mode_scroll') ?></a>
    <a href="<?= e(mode_url('page')) ?>"<?= $mode === 'page' ? ' aria-current="page"' : '' ?>><?= t('mode_page') ?></a>
</nav>
<?php endif; ?>
