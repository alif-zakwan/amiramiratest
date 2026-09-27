<?php
/**
 * Botanical corner flourish — a small hand-drawn spray of stems,
 * leaves and blossoms in the corner, gold line-art (currentColor),
 * echoing the dried-floral framing on the reference card without
 * needing any image assets.
 *
 * The <symbol> definition lives once in includes/header.php (an SVG
 * sprite is only ever defined once per page, referenced many times
 * via <use> — duplicating the id would be invalid HTML). This file
 * just drops the four corner instances; include it inside any element
 * that has position:relative.
 */
?>
<div class="corner-floral-set" aria-hidden="true">
  <svg class="corner-floral corner-floral--tl"><use href="#corner-floral-symbol"/></svg>
  <svg class="corner-floral corner-floral--tr"><use href="#corner-floral-symbol"/></svg>
  <svg class="corner-floral corner-floral--bl"><use href="#corner-floral-symbol"/></svg>
  <svg class="corner-floral corner-floral--br"><use href="#corner-floral-symbol"/></svg>
</div>
