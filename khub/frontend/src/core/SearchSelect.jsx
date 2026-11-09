import { useEffect, useMemo, useRef, useState } from 'react';

/**
 * Select2-style searchable select, single or multi (the pattern the Laravel
 * portal uses everywhere). Options: [{ id, name }]. Values are strings/numbers
 * (single) or arrays (multi). The search box appears automatically on long
 * lists (always in multi mode).
 */
const SEARCH_THRESHOLD = 8;

export const SearchSelect = ({
  id,
  options = [],
  value,
  onChange,
  multiple = false,
  placeholder = 'Select…',
  disabled = false,
  invalid = false,
  searchThreshold = SEARCH_THRESHOLD,
}) => {
  const [open, setOpen] = useState(false);
  const [term, setTerm] = useState('');
  const [highlight, setHighlight] = useState(0);
  const rootRef = useRef(null);
  const searchRef = useRef(null);
  const listRef = useRef(null);

  const selectedIds = useMemo(() => (multiple ? (Array.isArray(value) ? value : []) : value !== undefined && value !== null && value !== '' ? [value] : []).map(String), [value, multiple]);
  const selected = selectedIds.map((selectedId) => options.find((option) => String(option.id) === selectedId)).filter(Boolean);
  const searchable = multiple || options.length >= searchThreshold;

  const filtered = useMemo(() => {
    const clean = term.trim().toLowerCase();
    if (!clean) return options;
    return options.filter((option) => String(option.name).toLowerCase().includes(clean));
  }, [options, term]);

  useEffect(() => {
    const onDocClick = (event) => {
      if (rootRef.current && !rootRef.current.contains(event.target)) setOpen(false);
    };
    document.addEventListener('mousedown', onDocClick);
    return () => document.removeEventListener('mousedown', onDocClick);
  }, []);

  useEffect(() => {
    if (open) {
      setTerm('');
      setHighlight(0);
      setTimeout(() => searchRef.current?.focus(), 0);
    }
  }, [open]);

  useEffect(() => {
    listRef.current?.children[highlight]?.scrollIntoView({ block: 'nearest' });
  }, [highlight]);

  const pick = (option) => {
    if (multiple) {
      const next = selectedIds.includes(String(option.id))
        ? selectedIds.filter((selectedId) => selectedId !== String(option.id))
        : [...selectedIds, String(option.id)];
      onChange(next);
    } else {
      onChange(String(option.id));
      setOpen(false);
    }
  };

  const onKeyDown = (event) => {
    if (event.key === 'Escape') return setOpen(false);
    if (!open && ['Enter', 'ArrowDown', ' '].includes(event.key)) {
      event.preventDefault();
      return setOpen(true);
    }
    if (!open) return undefined;
    if (event.key === 'ArrowDown') {
      event.preventDefault();
      setHighlight((index) => Math.min(index + 1, filtered.length - 1));
    } else if (event.key === 'ArrowUp') {
      event.preventDefault();
      setHighlight((index) => Math.max(index - 1, 0));
    } else if (event.key === 'Enter') {
      event.preventDefault();
      if (filtered[highlight]) pick(filtered[highlight]);
    }
    return undefined;
  };

  return (
    <div className="kh-select" ref={rootRef} onKeyDown={onKeyDown}>
      <button
        type="button"
        id={id}
        className={`kh-select-control ${invalid ? 'is-invalid' : ''} ${disabled ? 'is-disabled' : ''}`}
        aria-haspopup="listbox"
        aria-expanded={open}
        disabled={disabled}
        onClick={() => setOpen((state) => !state)}
      >
        {multiple ? (
          selected.length === 0 ? (
            <span className="kh-select-placeholder">{placeholder}</span>
          ) : (
            <span className="kh-select-chips">
              {selected.map((option) => (
                <span className="kh-select-chip" key={option.id}>
                  {option.name}
                  <span
                    role="button"
                    tabIndex={-1}
                    aria-label={`Remove ${option.name}`}
                    onClick={(event) => {
                      event.stopPropagation();
                      pick(option);
                    }}
                  >
                    ×
                  </span>
                </span>
              ))}
            </span>
          )
        ) : selected[0] ? (
          <span>{selected[0].name}</span>
        ) : (
          <span className="kh-select-placeholder">{placeholder}</span>
        )}
        <i className={`fa fa-chevron-${open ? 'up' : 'down'} kh-select-caret`} aria-hidden="true" />
      </button>

      {open && (
        <div className="kh-select-panel">
          {searchable && (
            <div className="kh-select-search">
              <i className="fa fa-magnifying-glass" aria-hidden="true" />
              <input
                ref={searchRef}
                type="search"
                value={term}
                placeholder="Type to search…"
                aria-label="Search options"
                onChange={(event) => {
                  setTerm(event.target.value);
                  setHighlight(0);
                }}
              />
            </div>
          )}
          <ul role="listbox" aria-multiselectable={multiple} ref={listRef} className="kh-select-list">
            {filtered.length === 0 && <li className="kh-select-empty">No matches for “{term}”.</li>}
            {filtered.map((option, index) => {
              const isSelected = selectedIds.includes(String(option.id));
              return (
                <li
                  key={option.id}
                  role="option"
                  aria-selected={isSelected}
                  className={`kh-select-option ${isSelected ? 'is-selected' : ''} ${index === highlight ? 'is-highlighted' : ''}`}
                  onMouseEnter={() => setHighlight(index)}
                  onClick={() => pick(option)}
                >
                  {multiple && <i className={`fa ${isSelected ? 'fa-square-check' : 'fa-square'} mr-1`} aria-hidden="true" />}
                  <span>{option.name}</span>
                  {!multiple && isSelected && <i className="fa fa-check ml-auto" aria-hidden="true" />}
                </li>
              );
            })}
          </ul>
          {multiple && selected.length > 0 && (
            <div className="kh-select-footer">
              <span>{selected.length} selected</span>
              <button type="button" onClick={() => onChange([])}>Clear all</button>
            </div>
          )}
        </div>
      )}
    </div>
  );
};
