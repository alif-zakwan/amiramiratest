<?php
/**
 * Main invitation body. Expects $cfg and $guestName.
 */
?>
<?php
/**
 * Ticket-stub date block (#5): splits the existing config-driven wording
 * "28 November 2026" into day-number / month / year without touching
 * config.php itself, so the day-number can be rendered large and the
 * month/year small either side of it. Falls back to the plain string
 * unchanged if the format ever doesn't split into exactly 3 parts.
 */
$ticketDateParts = explode(' ', trim($cfg['wedding_date_my']), 3);
$ticketDateOk = count($ticketDateParts) === 3;
?>
<main class="invitation" id="invitation">

    <!-- ============ HERO ============ -->
    <section class="panel hero reveal">
        <?php include __DIR__ . '/corner-floral.php'; ?>
        <div class="ghost-watermark" aria-hidden="true">
            <img src="assets/images/emblem-doves.png" alt="" draggable="false">
        </div>
        <div class="panel__frame">
            <p class="arabic"><?= $cfg['greeting_arabic'] ?></p>
            <p class="tagline"><?= htmlspecialchars($cfg['tagline']) ?></p>

            <div class="monogram-frame" id="monogramFrame">
                <svg class="monogram-frame__oval" viewBox="0 0 160 200" aria-hidden="true">
                    <ellipse class="monogram-frame__oval-main" cx="80" cy="100" rx="70" ry="92"/>
                    <ellipse class="monogram-frame__oval-dotted" cx="80" cy="100" rx="60" ry="82"/>
                    <!-- top flourish -->
                    <path class="monogram-frame__flourish" d="M80 8c-9 0-15 5-15 5s6-2 15-2 15 2 15 2-6-5-15-5z"/>
                    <path class="monogram-frame__flourish" d="M50 14c6-6 18-9 30-9s24 3 30 9c-8-3-19-5-30-5s-22 2-30 5z"/>
                    <circle class="monogram-frame__dot" cx="80" cy="6" r="1.6"/>
                    <!-- bottom flourish -->
                    <path class="monogram-frame__flourish" d="M80 192c-9 0-15-5-15-5s6 2 15 2 15-2 15-2-6 5-15 5z"/>
                    <path class="monogram-frame__flourish" d="M50 186c6 6 18 9 30 9s24-3 30-9c-8 3-19 5-30 5s-22-2-30-5z"/>
                    <circle class="monogram-frame__dot" cx="80" cy="194" r="1.6"/>
                </svg>
                <div class="monogram" aria-hidden="true">
                    <?php include __DIR__ . '/doves.php'; ?>
                    <span class="monogram__amp">&amp;</span>
                </div>
                <span class="spark-fx" id="monogramSpark" aria-hidden="true">
                    <span class="spark-fx__ray spark-fx__ray--1"></span>
                    <span class="spark-fx__ray spark-fx__ray--2"></span>
                    <span class="spark-fx__ray spark-fx__ray--3"></span>
                    <span class="spark-fx__ray spark-fx__ray--4"></span>
                    <span class="spark-fx__core"></span>
                </span>
            </div>

            <h1 class="couple-names">
                <span class="couple-names__name couple-names__name--bride"><?= htmlspecialchars($cfg['bride_short']) ?></span>
                <span class="couple-names__amp">&amp;</span>
                <span class="couple-names__name couple-names__name--groom"><?= htmlspecialchars($cfg['groom_short']) ?></span>
            </h1>

            <div class="divider" aria-hidden="true"><span></span><i>❧</i><span></span></div>

            <div class="ticket-date">
                <span class="ticket-date__day-name"><?= htmlspecialchars($cfg['wedding_day_my']) ?></span>
                <?php if ($ticketDateOk): ?>
                <div class="ticket-date__stub">
                    <span class="ticket-date__rule" aria-hidden="true"></span>
                    <span class="ticket-date__num"><?= htmlspecialchars($ticketDateParts[0]) ?></span>
                    <span class="ticket-date__rule" aria-hidden="true"></span>
                    <span class="ticket-date__my">
                        <span class="ticket-date__month"><?= htmlspecialchars($ticketDateParts[1]) ?></span>
                        <span class="ticket-date__year"><?= htmlspecialchars($ticketDateParts[2]) ?></span>
                    </span>
                </div>
                <?php else: ?>
                <p class="hero-date"><?= htmlspecialchars($cfg['wedding_date_my']) ?></p>
                <?php endif; ?>
            </div>

            <div class="divider" aria-hidden="true"><span></span><i>❧</i><span></span></div>

            <blockquote class="quote">
                <?= htmlspecialchars($cfg['quote_text']) ?>
                <cite><?= htmlspecialchars($cfg['quote_source']) ?></cite>
            </blockquote>
        </div>
    </section>

    <!-- ============ COUNTDOWN ============ -->
    <section class="panel countdown-panel reveal">
        <p class="section-label">Menghitung hari ke majlis kami</p>
        <div class="countdown" id="countdown" data-target="<?= htmlspecialchars($cfg['wedding_datetime']) ?>">
            <div class="countdown__unit"><span class="countdown__num" id="cd-days">00</span><span class="countdown__label">Hari</span></div>
            <div class="countdown__unit"><span class="countdown__num" id="cd-hours">00</span><span class="countdown__label">Jam</span></div>
            <div class="countdown__unit"><span class="countdown__num" id="cd-mins">00</span><span class="countdown__label">Minit</span></div>
            <div class="countdown__unit"><span class="countdown__num" id="cd-secs">00</span><span class="countdown__label">Saat</span></div>
        </div>
        <p class="countdown__done" id="countdownDone" hidden>Tibanya hari yang dinanti-nantikan! 💍</p>
    </section>

    <!-- ============ FORMAL INVITATION TEXT ============ -->
    <section class="panel formal reveal">
        <?php include __DIR__ . '/corner-floral.php'; ?>
        <div class="ghost-watermark" aria-hidden="true">
            <img src="assets/images/emblem-doves.png" alt="" draggable="false">
        </div>
        <span class="drift-sprite" aria-hidden="true">
            <svg viewBox="0 0 100 100"><use href="#drift-sprite-symbol"/></svg>
        </span>
        <div class="panel__frame">
            <p class="formal__intro"><?= htmlspecialchars($cfg['invite_intro']) ?></p>

            <p class="formal__parents">
                <?= htmlspecialchars($cfg['bride_father']) ?>
                <span class="amp-small">&amp;</span>
                <?= htmlspecialchars($cfg['bride_mother']) ?>
            </p>

            <p class="formal__persilakan"><?= htmlspecialchars($cfg['invite_persilakan']) ?></p>

            <div class="divider divider--sm" aria-hidden="true"><span></span><i>◆</i><span></span></div>

            <p class="formal__bride"><?= htmlspecialchars($cfg['bride_full']) ?></p>
            <p class="formal__relation"><?= htmlspecialchars($cfg['bride_relation_line']) ?></p>
            <p class="formal__groom"><?= htmlspecialchars($cfg['groom_full']) ?></p>

            <div class="divider divider--sm" aria-hidden="true"><span></span><i>◆</i><span></span></div>
        </div>
    </section>

    <!-- ============ EVENT DETAILS ============ -->
    <section class="panel details reveal">
        <div class="detail-card">
            <svg viewBox="0 0 24 24" class="detail-card__icon" aria-hidden="true"><path fill="currentColor" d="M7 2v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zm12 8v10H5V10h14z"/></svg>
            <p class="detail-card__label">Pada</p>
            <div class="ticket-date ticket-date--sm">
                <span class="ticket-date__day-name"><?= htmlspecialchars($cfg['wedding_day_my']) ?></span>
                <?php if ($ticketDateOk): ?>
                <div class="ticket-date__stub">
                    <span class="ticket-date__rule" aria-hidden="true"></span>
                    <span class="ticket-date__num"><?= htmlspecialchars($ticketDateParts[0]) ?></span>
                    <span class="ticket-date__rule" aria-hidden="true"></span>
                    <span class="ticket-date__my">
                        <span class="ticket-date__month"><?= htmlspecialchars($ticketDateParts[1]) ?></span>
                        <span class="ticket-date__year"><?= htmlspecialchars($ticketDateParts[2]) ?></span>
                    </span>
                </div>
                <?php else: ?>
                <p class="detail-card__value"><?= htmlspecialchars($cfg['wedding_date_my']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="detail-card">
            <svg viewBox="0 0 24 24" class="detail-card__icon" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 5v5.4l4.2 2.5-.75 1.3L11 13V7h2z"/></svg>
            <p class="detail-card__label">Aturcara Majlis</p>
            <p class="detail-card__value"><?= htmlspecialchars($cfg['event_start']) ?> &ndash; <?= htmlspecialchars($cfg['event_end']) ?><br><span class="detail-card__sub">Ketibaan pengantin <?= htmlspecialchars($cfg['pengantin_tiba']) ?></span></p>
        </div>
        <div class="detail-card">
            <svg viewBox="0 0 24 24" class="detail-card__icon" aria-hidden="true"><path fill="currentColor" d="M12 2c-4.4 0-8 3.6-8 8 0 5.6 8 12 8 12s8-6.4 8-12c0-4.4-3.6-8-8-8zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg>
            <p class="detail-card__label">Bertempat di</p>
            <p class="detail-card__value"><?= htmlspecialchars($cfg['venue_name']) ?></p>
            <a class="detail-card__link" href="<?= htmlspecialchars($cfg['map_link']) ?>" target="_blank" rel="noopener">Buka di Google Maps &rarr;</a>
        </div>
    </section>

    <!-- ============ MAP ============ -->
    <section class="panel map-panel reveal">
        <p class="section-label">Lokasi Majlis</p>
        <div class="map-frame">
            <iframe src="<?= htmlspecialchars($cfg['map_embed_src']) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi majlis"></iframe>
        </div>
        <p class="map-address"><?= htmlspecialchars($cfg['venue_address']) ?></p>
    </section>

    <!-- ============ CONTACTS ============ -->
    <section class="panel contacts reveal">
        <p class="section-label">Hubungi Kami</p>
        <ul class="contact-list">
            <?php foreach ($cfg['contacts'] as $c): ?>
            <li>
                <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $c['phone'])) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1L6.6 10.8z"/></svg>
                    <?= htmlspecialchars($c['name']) ?> &middot; <?= htmlspecialchars($c['phone']) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <?php if (!empty($cfg['show_rsvp'])): ?>
    <!-- ============ RSVP ============ -->
    <section class="panel rsvp reveal">
        <p class="section-label">RSVP</p>
        <p class="rsvp__note">Sila sahkan kehadiran anda sebelum <?= htmlspecialchars($cfg['rsvp_deadline']) ?>.</p>
        <form class="rsvp-form" id="rsvpForm">
            <label for="rsvpName">Nama
                <input type="text" id="rsvpName" name="name" maxlength="80" required>
            </label>
            <label for="rsvpAttending">Kehadiran
                <select id="rsvpAttending" name="attending" required>
                    <option value="" disabled selected>Pilih satu</option>
                    <option value="hadir">Akan hadir</option>
                    <option value="tidak_hadir">Tidak dapat hadir</option>
                </select>
            </label>
            <label for="rsvpPax">Bilangan tetamu
                <select id="rsvpPax" name="pax" required>
                    <?php for ($p = 1; $p <= 6; $p++): ?>
                    <option value="<?= $p ?>"<?= $p === 1 ? ' selected' : '' ?>><?= $p ?> orang</option>
                    <?php endfor; ?>
                </select>
            </label>
            <label for="rsvpMessage">Ucapan &amp; doa restu <span style="text-transform:none;letter-spacing:0;">(pilihan)</span>
                <textarea id="rsvpMessage" name="message" rows="3" maxlength="240"></textarea>
            </label>
            <p class="rsvp-form__status" id="rsvpStatus" role="status"></p>
            <button type="submit" class="btn btn--primary" id="rsvpSubmit">Hantar</button>
        </form>
    </section>

    <!-- ============ WISHES WALL ============ -->
    <section class="panel wishes reveal">
        <p class="section-label">Ucapan Tetamu</p>
        <ul class="wishes-list" id="wishesList">
            <li class="wishes-list__empty" id="wishesEmpty">Jadilah tetamu pertama menghantar ucapan!</li>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (!empty($cfg['enable_salam_kaut'])): ?>
    <!-- ============ GIFT ============ -->
    <section class="panel gift reveal">
        <p class="section-label">Salam Kaut</p>
        <p class="gift__note"><?= htmlspecialchars($cfg['gift_note']) ?></p>
        <div class="gift-card">
            <p class="gift-card__bank"><?= htmlspecialchars($cfg['gift_bank']) ?></p>
            <p class="gift-card__acc" id="giftAccNo"><?= htmlspecialchars($cfg['gift_account_no']) ?></p>
            <p class="gift-card__name"><?= htmlspecialchars($cfg['gift_account_name']) ?></p>
            <button type="button" class="btn btn--ghost" id="copyAccBtn">Salin Nombor Akaun</button>
        </div>
    </section>
    <?php endif; ?>

    <!-- ============ FOOTER ============ -->
    <footer class="site-footer reveal">
        <div class="monogram monogram--small" aria-hidden="true">
            <?php include __DIR__ . '/doves.php'; ?>
            <span class="monogram__amp">&amp;</span>
        </div>
        <p class="site-footer__thanks">Terima kasih kerana menjadi sebahagian daripada kegembiraan kami.</p>
        <p class="site-footer__names"><?= htmlspecialchars($cfg['bride_short']) ?> &amp; <?= htmlspecialchars($cfg['groom_short']) ?></p>
        <button class="btn btn--ghost btn--share" id="shareBtn" type="button">Kongsi Jemputan Ini</button>
        <p class="site-footer__hashtag"><?= htmlspecialchars($cfg['hashtag']) ?></p>
    </footer>

</main>
