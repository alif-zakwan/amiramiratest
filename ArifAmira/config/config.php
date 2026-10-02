<?php
/**
 * ArifAmira — Wedding Invitation
 * ---------------------------------------------------------------
 * CONFIGURATION FILE
 * Every piece of text, every date and every contact number on the
 * site is pulled from this one file. Edit the values below — you
 * do NOT need to touch any other file to update the invitation.
 * ---------------------------------------------------------------
 */

$config = [

    // ---- Page / browser tab ------------------------------------
    // site_title + og_description are what WhatsApp/Telegram show
    // when the link is shared.
    'site_title'   => 'Amira & Arif | Walimatulurus',
    'og_description' => 'Anda dijemput hadir ke majlis perkahwinan Amira & Arif, Sabtu 28 November 2026.',

    // ---- The couple ----------------------------------------------
    'bride_short'  => 'Amira Arezan',
    'bride_full'   => 'Nurin Amira Arezan',
    'bride_father' => 'Norizam bin Nisak',
    'bride_mother' => 'Norhaliza binti Ibrahim',
    'groom_short'  => 'Anwar Arif',
    'groom_full'   => 'Anwar Arif',

    // Initials pressed into the wax seal on the opening envelope.
    'wax_seal_letter' => 'AA',

    'tagline'      => 'Pertemuan Kejora dan Purnama',
    'greeting_arabic' => 'اَلسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللهِ وَبَرَكَاتُهُ',

    // ---- Qur'an / doa quote -----------------------------------
    'quote_text'   => '“Dan Kami telah menciptakan kamu berpasang-pasangan”',
    'quote_source' => "Surah An-Naba' : 78:8",

    // ---- Opening paragraph (formal invitation wording) ----------
    'invite_intro' => 'Dengan penuh rasa kesyukuran ke hadrat Ilahi, kami',
    'invite_persilakan' => "Dengan segala hormatnya mempersilakan Dato'/Datin/Tuan/Puan/Encik/Cik untuk hadir ke majlis perkahwinan puteri kami",
    'bride_relation_line' => 'dengan pilihan hatinya',

    // ---- Date & time ---------------------------------------------
    // Local time at the venue — used for the countdown, together
    // with 'timezone' below, so guests abroad count down correctly.
    'wedding_datetime' => '2026-11-28 11:00:00',
    'timezone'         => 'Asia/Kuala_Lumpur',
    'wedding_day_my'   => 'Sabtu',
    'wedding_date_my'  => '28 November 2026',

    'event_start'      => '11.00 pagi',
    'event_end'        => '4.30 petang',
    'pengantin_tiba'   => '12.30 tengahari',

    // ---- Venue -----------------------------------------------------
    'venue_name'    => 'Dewan Orang Ramai Renggam',
    'venue_address' => 'Dewan Orang Ramai Renggam, 86000 Renggam, Johor',
    // Paste a Google Maps "share/embed" link's src URL here.
    'map_embed_src' => 'https://www.google.com/maps?q=Dewan+Orang+Ramai+Renggam+Johor&output=embed',
    'map_link'      => 'https://www.google.com/maps/search/?api=1&query=Dewan+Orang+Ramai+Renggam+Johor',

    // ---- Contacts (name => phone) --------------------------------
    // Each contact gets a Call and a WhatsApp button. WhatsApp needs the
    // country code, so a number starting with 0 is turned into 60... (Malaysia).
    'whatsapp_country_code' => '60',
    'contacts' => [
        ['name' => 'Norizam', 'phone' => '011-55059882'],
        ['name' => 'Liza',    'phone' => '012-7240005'],
        ['name' => 'Aiman',   'phone' => '013-4614809'],
    ],

    // ---- Optional: money gift / salam kaut section -----------------
    // KIV (Keep In View) — flip to true whenever this is ready to publish.
    'enable_salam_kaut' => false,
    'gift_note'   => 'Kehadiran anda adalah keutamaan kami. Namun jika ingin memberi salam kaut, boleh salurkan ke:',
    'gift_bank'   => 'Maybank',
    'gift_account_name' => 'Nurin Amira Arezan',
    'gift_account_no'   => '1234 5678 9012',

    // ---- RSVP -------------------------------------------------------
    'show_rsvp'   => true,
    'rsvp_deadline' => '14 November 2026',

    // Where RSVPs are stored. Leave 'url' empty to keep them in data/rsvp.json
    // (fine for XAMPP testing). To save them in a Google Sheet instead, follow
    // docs/google-sheets-rsvp/SETUP.md and paste the web-app URL + secret here.
    // On Render, prefer the RSVP_SHEET_URL / RSVP_SHEET_SECRET environment
    // variables so the secret never goes into git.
    'rsvp_sheet' => [
        'url'    => '',
        'secret' => '',
    ],

    // ---- Background music -------------------------------------------
    // Put an mp3 in assets/audio/ then point this at it, e.g.
    // 'assets/audio/wedding-song.mp3'. Leave blank to hide the music button.
    // Music starts when the guest taps the wax seal (browsers block
    // autoplay with sound before a tap).
    'music_file'  => 'assets/audio/music_file.mp3',
    // Second of the song where playback begins (and where it restarts when it
    // loops). 30 = start at 0:30. Use 0 to play from the beginning.
    'music_start' => 0,

    // ---- Experience ------------------------------------------------
    // 'scroll' = one long scrolling card (the client's choice)
    // 'page'   = one card at a time, swipe / tap to turn the page
    //            (built, but switched off)
    // Only modes listed in 'allowed_modes' can be used; a link with
    // ?mode=page is ignored unless 'page' is listed. To bring page mode
    // back for a demo: allowed_modes => ['scroll', 'page'] and
    // show_mode_switcher => true.
    'experience' => [
        'default_mode'       => 'scroll',
        'allowed_modes'      => ['scroll'],
        'show_mode_switcher' => false,
    ],

    // ---- Interface wording --------------------------------------------
    // Every fixed label on the page, in one place.
    'labels' => [
        'open_aria'        => 'Buka jemputan',
        'skip'             => 'Langkau',

        'countdown_title'  => 'Menghitung Hari',
        'countdown_days'   => 'Hari',
        'countdown_hours'  => 'Jam',
        'countdown_mins'   => 'Minit',
        'countdown_secs'   => 'Saat',
        'countdown_done'   => 'Tibanya hari yang dinanti-nantikan!',

        'on'               => 'Pada',
        'venue'            => 'Bertempat di',
        'schedule'         => 'Aturcara Majlis',
        'arrival'          => 'Ketibaan Pengantin',
        'open_map'         => 'Buka di Google Maps',
        'location_title'   => 'Lokasi Majlis',
        'map_title'        => 'Peta lokasi majlis',
        'contacts_title'   => 'Hubungi',
        'contact_call'     => 'Panggil',
        'contact_whatsapp' => 'WhatsApp',

        'rsvp_title'       => 'RSVP',
        'rsvp_note'        => 'Sila sahkan kehadiran anda sebelum',
        'rsvp_name'        => 'Nama',
        'rsvp_attending'   => 'Kehadiran',
        'rsvp_choose'      => 'Pilih satu',
        'rsvp_yes'         => 'Akan hadir',
        'rsvp_no'          => 'Tidak dapat hadir',
        'rsvp_pax'         => 'Bilangan tetamu',
        'rsvp_pax_unit'    => 'orang',
        'rsvp_submit'      => 'Hantar',
        'rsvp_sending'     => 'Menghantar…',
        'rsvp_ok'          => 'Terima kasih! RSVP anda telah diterima.',
        'rsvp_required'    => 'Sila lengkapkan nama dan kehadiran.',
        'rsvp_failed'      => 'Maaf, sesuatu tidak kena. Cuba lagi.',
        'rsvp_offline'     => 'Tidak dapat menghubungi pelayan. Cuba lagi sebentar.',

        // Ucapan Tetamu (guestbook)
        'gb_title'            => 'Ucapan Tetamu',
        'gb_intro'            => 'Titipkan doa, ucapan dan pesanan buat kedua mempelai.',
        'gb_name'             => 'Nama',
        'gb_name_ph'          => 'Nama anda',
        'gb_message'          => 'Ucapan',
        'gb_message_ph'       => 'Tuliskan ucapan, doa atau pesanan anda...',
        'gb_submit'           => 'Hantar Ucapan',
        'gb_sending'          => 'Menghantar…',
        'gb_ok'               => 'Terima kasih atas ucapan anda 🤍',
        'gb_name_required'    => 'Sila isi nama anda.',
        'gb_message_required' => 'Sila tulis ucapan anda.',
        'gb_failed'           => 'Maaf, ucapan tidak dapat dihantar. Cuba lagi.',
        'gb_offline'          => 'Tidak dapat menghubungi pelayan. Cuba lagi sebentar.',
        'gb_empty'            => 'Jadilah yang pertama meninggalkan ucapan 🤍',
        'gb_list_aria'        => 'Ucapan daripada tetamu',
        'gb_prev'             => 'Ucapan sebelum',
        'gb_next'             => 'Ucapan seterusnya',

        'gift_title'       => 'Salam Kaut',
        'gift_copy'        => 'Salin Nombor Akaun',
        'gift_copied'      => 'Disalin!',

        'thanks'           => 'Terima kasih kerana menjadi sebahagian daripada kegembiraan kami.',
        'share'            => 'Kongsi Jemputan Ini',
        'share_copied'     => 'Pautan disalin!',

        'music_aria'       => 'Main / henti muzik',
        'mode_label'       => 'Paparan',
        'mode_scroll'      => 'Skrol',
        'mode_page'        => 'Halaman',
        'page_prev'        => 'Halaman sebelum',
        'page_next'        => 'Halaman seterusnya',
    ],

];

// Private settings (the Google Sheet URL + secret) live in config.local.php,
// which is NOT committed to git. Copy config.local.php.example to
// config.local.php and fill it in. Anything set there overrides the above.
$localFile = __DIR__ . '/config.local.php';
if (is_file($localFile)) {
    $config = array_replace_recursive($config, require $localFile);
}

return $config;
