// jsdom has no matchMedia; the Layout uses it for the portrait/landscape nav.
if (typeof window !== 'undefined' && !window.matchMedia) {
  window.matchMedia = (query) => ({
    matches: false,
    media: query,
    addEventListener: () => {},
    removeEventListener: () => {},
    addListener: () => {},
    removeListener: () => {},
    dispatchEvent: () => false,
  });
}
if (typeof window !== 'undefined' && !window.scrollTo) {
  window.scrollTo = () => {};
}
