<?php
/**
 * ArifAmira — Wedding Invitation
 * Entry point. Run locally with e.g.:
 *   php -S localhost:8000
 * then open http://localhost:8000/
 *
 * A guest's name can be personalised on the envelope via a link like:
 *   index.php?to=Puan+Salmah
 */

$cfg = require __DIR__ . '/config/config.php';

$guestName = '';
if (isset($_GET['to'])) {
    // Keep it short and strip anything unexpected; htmlspecialchars()
    // happens again at print time in the includes, this is just a sanity trim.
    $guestName = trim(substr($_GET['to'], 0, 60));
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/envelope.php';
include __DIR__ . '/includes/invitation.php';
include __DIR__ . '/includes/footer.php';
