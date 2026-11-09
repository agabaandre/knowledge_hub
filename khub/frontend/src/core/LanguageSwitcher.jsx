import { useEffect, useRef, useState } from 'react';
import { useI18n } from './i18n.jsx';

/** Brand-bar language dropdown, same shape as the Laravel selector (flag + code). */
export const LanguageSwitcher = () => {
  const { locale, languages, setLocale } = useI18n();
  const [open, setOpen] = useState(false);
  const [busy, setBusy] = useState(false);
  const ref = useRef(null);
  const current = languages.find((lang) => lang.code === locale) ?? languages[0];

  useEffect(() => {
    const onClick = (event) => {
      if (ref.current && !ref.current.contains(event.target)) setOpen(false);
    };
    document.addEventListener('click', onClick);
    return () => document.removeEventListener('click', onClick);
  }, []);

  if (languages.length < 2 || !current) return null;

  const choose = async (code) => {
    setBusy(true);
    try {
      await setLocale(code);
      setOpen(false);
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className="position-relative" ref={ref}>
      <button
        type="button"
        className="btn btn-sm bg-white border d-flex align-items-center"
        style={{ gap: 6 }}
        aria-haspopup="listbox"
        aria-expanded={open}
        aria-label="Change language"
        disabled={busy}
        onClick={() => setOpen((value) => !value)}
      >
        <span aria-hidden="true">{current.flag}</span>
        <span className="ft-medium">{current.code.toUpperCase()}</span>
        <i className={`fa fa-chevron-${open ? 'up' : 'down'}`} style={{ fontSize: 10 }} aria-hidden="true" />
      </button>
      {open && (
        <ul
          role="listbox"
          className="list-unstyled bg-white border rounded position-absolute mb-0"
          style={{ right: 0, top: '110%', minWidth: 170, zIndex: 1060, boxShadow: '0 8px 24px rgba(15,23,42,0.14)', padding: 6 }}
        >
          {languages.map((lang) => (
            <li key={lang.code} role="option" aria-selected={lang.code === locale}>
              <button
                type="button"
                className="btn btn-sm w-100 text-left d-flex align-items-center"
                style={{ gap: 8, background: lang.code === locale ? 'rgba(0,98,57,0.08)' : 'transparent' }}
                onClick={() => choose(lang.code)}
              >
                <span aria-hidden="true">{lang.flag}</span>
                <span>{lang.name}</span>
                {lang.code === locale && <i className="fa fa-check ml-auto theme-cl" aria-hidden="true" />}
              </button>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};
