(() => {
  const isZoomShortcutKey = (event) => {
    if (!(event.ctrlKey || event.metaKey)) return false;
    const key = event.key;
    return key === '+' || key === '-' || key === '=' || key === '0';
  };

  window.addEventListener(
    'keydown',
    (event) => {
      if (isZoomShortcutKey(event)) {
        event.preventDefault();
      }
    },
    { capture: true }
  );

  window.addEventListener(
    'wheel',
    (event) => {
      if (event.ctrlKey) {
        event.preventDefault();
      }
    },
    { passive: false, capture: true }
  );

  const prevent = (event) => event.preventDefault();
  window.addEventListener('gesturestart', prevent, { passive: false, capture: true });
  window.addEventListener('gesturechange', prevent, { passive: false, capture: true });
  window.addEventListener('gestureend', prevent, { passive: false, capture: true });
})();
