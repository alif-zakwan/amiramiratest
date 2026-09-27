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

return [

    // ---- Page / browser tab ------------------------------------
    'site_title'   => 'Amira & Arif | Walimatulurus',
    'og_description' => 'Anda dijemput hadir ke majlis perkahwinan Amira & Anwar, Sabtu 28 November 2026.',

    // ---- The couple ----------------------------------------------
    'bride_short'  => 'Amira Arezan',
    'bride_full'   => 'Nurin Amira Arezan',
    'bride_father' => 'Norizam bin Nisak',
    'bride_mother' => 'Norhaliza binti Ibrahim',
    'groom_short'  => 'Anwar Arif',
    'groom_full'   => 'Anwar Arif',

    'monogram'     => 'A &amp; A',
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
    // ISO format — used both for display and for the JS countdown.
    'wedding_datetime' => '2026-11-28 11:00:00',
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

    // ---- Contacts (name => phone, digits only for the tel: link) --
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

    // ---- Hashtag / share -----------------------------------------
    'hashtag' => '#AmiraArifBersatu',

    // ---- Background music -------------------------------------------
    // Put an mp3 in assets/audio/ then point this at it, e.g.
    // 'assets/audio/wedding-song.mp3'. Leave blank to hide the music button.
    'music_file'  => '',

];
