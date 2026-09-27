Drop any real photos or transparent flower/corner PNGs exported from your
Canva design in this folder (e.g. corner-flowers.png, couple-photo.jpg).

The current design draws its florals, dividers and the dove monogram with
plain CSS/SVG so the project works immediately with no images required.

If you'd like to swap in your own artwork later, the easiest spots are:
  - css/style.css  -> .hero::before / .hero::after  (corner ornaments)
  - css/style.css  -> .monogram .doves               (the dove/ring emblem)
  - includes/invitation.php -> add an <img> wherever you'd like a photo
