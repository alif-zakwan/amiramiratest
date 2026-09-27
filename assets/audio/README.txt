Optional: drop a background-music MP3 in this folder and point
config/config.php -> 'music_file' at it, e.g.:

  'music_file' => 'assets/audio/wedding-song.mp3',

The music button in the top-right corner only appears when a
music_file is set. Browsers block autoplay with sound, so the
button always starts paused — guests tap it to play.

To disable music entirely, set:
  'music_file' => '',
