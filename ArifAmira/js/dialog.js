/**
 * Popups (native <dialog>, so focus stays inside and Esc closes them):
 * the guestbook form and the map-app chooser share this.
 */

/** Close with the ✕ button or a click on the dimmed backdrop; unlock page scroll on close. */
export function wireDialog(dialog, closeButton) {
  closeButton.addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
  dialog.addEventListener('close', () => document.documentElement.classList.remove('modal-open'));
}

export function openDialog(dialog) {
  document.documentElement.classList.add('modal-open');   // page behind doesn't scroll
  if (typeof dialog.showModal === 'function') dialog.showModal();
  else dialog.setAttribute('open', '');
}
