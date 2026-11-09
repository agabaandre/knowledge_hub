import { useRef, useState } from 'react';
import { SearchSelect } from '../../core/SearchSelect.jsx';
import { RichTextEditor } from '../../core/RichTextEditor.jsx';

/** Renders one wizard field from the shared PUBLISH_WIZARD definition. */
export const WizardField = ({ field, value, error, required, options, onChange, assist }) => {
  const fileRef = useRef(null);
  const [fileName, setFileName] = useState('');
  const id = `pw-${field.name}`;
  const label = (
    <div className="d-flex align-items-center justify-content-between">
      <label htmlFor={id} className="ft-medium">
        {field.label} {required && <span className="text-danger">*</span>}
      </label>
      {assist}
    </div>
  );
  const hint = field.hint && <small className="text-muted d-block">{field.hint}</small>;
  const invalid = error ? { borderColor: '#dc3545' } : undefined;

  switch (field.type) {
    case 'text':
    case 'url':
      return (
        <div className="form-group">
          {label}
          <input id={id} type={field.type === 'url' ? 'url' : 'text'} className="form-control" style={invalid}
            maxLength={field.max} value={value ?? ''} placeholder={field.label}
            onChange={(event) => onChange(field.name, event.target.value)} />
          {hint}
          {error && <small className="text-danger d-block">{error}</small>}
        </div>
      );
    case 'year': {
      const years = Array.from({ length: 60 }, (_, index) => new Date().getFullYear() - index).map((year) => ({ id: year, name: String(year) }));
      return (
        <div className="form-group">
          {label}
          <SearchSelect id={id} options={years} value={value ?? ''} invalid={Boolean(error)}
            placeholder="Year published" onChange={(next) => onChange(field.name, next)} />
          {error && <small className="text-danger d-block">{error}</small>}
        </div>
      );
    }
    case 'select':
      return (
        <div className="form-group">
          {label}
          <SearchSelect id={id} options={options} value={value ?? ''} invalid={Boolean(error)}
            placeholder={`Select ${field.label}`} onChange={(next) => onChange(field.name, next)} />
          {hint}
          {error && <small className="text-danger d-block">{error}</small>}
        </div>
      );
    case 'multiselect':
    case 'tags':
      return (
        <div className="form-group">
          {label}
          <SearchSelect id={id} multiple options={options} value={Array.isArray(value) ? value : []} invalid={Boolean(error)}
            placeholder={`Select ${field.label}`} onChange={(next) => onChange(field.name, next)} />
          {hint}
          {error && <small className="text-danger d-block">{error}</small>}
        </div>
      );
    case 'richtext': {
      const plain = String(value ?? '').replace(/<[^>]*>/g, ' ');
      const words = plain.trim().split(/\s+/).filter(Boolean).length;
      return (
        <div className="form-group">
          {label}
          <RichTextEditor id={id} value={value ?? ''} invalid={Boolean(error)}
            placeholder={field.hint} onChange={(next) => onChange(field.name, next)} />
          {hint}
          <small className="text-muted d-block">{words} word{words === 1 ? '' : 's'}</small>
          {error && <small className="text-danger d-block">{error}</small>}
        </div>
      );
    }
    case 'textarea': {
      return (
        <div className="form-group">
          {label}
          <textarea id={id} className="form-control" rows={4} style={invalid}
            value={value ?? ''} onChange={(event) => onChange(field.name, event.target.value)} />
          {hint}
          {error && <small className="text-danger d-block">{error}</small>}
        </div>
      );
    }
    case 'file':
      return (
        <div className="form-group">
          {label}
          <div className="rounded border p-3 text-center" style={{ background: '#f8fafc', borderStyle: 'dashed', ...(invalid ?? {}) }}>
            <input ref={fileRef} type="file" hidden accept={field.accept}
              onChange={(event) => {
                const file = event.target.files?.[0] ?? null;
                setFileName(file?.name ?? '');
                onChange(field.name, file);
              }} />
            <button type="button" className="btn btn-sm btn-outline-success bg-white" onClick={() => fileRef.current?.click()}>
              <i className="fa fa-file-arrow-up mr-1" aria-hidden="true" /> Choose file
            </button>
            <p className="text-muted mb-0 mt-2" style={{ fontSize: '0.8rem' }}>{fileName || field.hint}</p>
          </div>
          {error && <small className="text-danger d-block">{error}</small>}
        </div>
      );
    case 'toggle':
      return (
        <label className="mb-0 d-inline-flex align-items-center ft-medium" style={{ gap: 6, cursor: 'pointer' }}>
          <input type="checkbox" checked={Boolean(value)} onChange={(event) => onChange(field.name, event.target.checked)} />
          {field.label}
        </label>
      );
    default:
      return null;
  }
};
