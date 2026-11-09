import { useEffect, useRef } from 'react';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const TOOLBAR = [
  [{ header: [2, 3, false] }],
  ['bold', 'italic', 'underline'],
  [{ list: 'ordered' }, { list: 'bullet' }],
  ['blockquote', 'link'],
  ['clean'],
];

const isEmpty = (html) => !html || html === '<p><br></p>' || html === '<p></p>';

/**
 * Reusable Quill rich-text editor (snow theme), styled to sit beside the
 * portal's form controls. Emits sanitized-on-server HTML via onChange; use it
 * anywhere a plain <textarea> is not enough (publication descriptions, etc.).
 */
export const RichTextEditor = ({ id, value, onChange, placeholder, invalid = false, minHeight = 160 }) => {
  const hostRef = useRef(null);
  const quillRef = useRef(null);
  const emittedRef = useRef(value ?? '');

  useEffect(() => {
    const quill = new Quill(hostRef.current, {
      theme: 'snow',
      placeholder,
      modules: { toolbar: TOOLBAR },
    });
    quillRef.current = quill;
    if (value) quill.clipboard.dangerouslyPasteHTML(value);
    quill.on('text-change', () => {
      const html = isEmpty(quill.root.innerHTML) ? '' : quill.root.innerHTML;
      emittedRef.current = html;
      onChange(html);
    });
    return () => { quillRef.current = null; };
  }, []); // eslint-disable-line react-hooks/exhaustive-deps

  // Follow external value changes (e.g. AI draft filling the field).
  useEffect(() => {
    const quill = quillRef.current;
    if (!quill) return;
    const next = value ?? '';
    if (next !== emittedRef.current && next !== quill.root.innerHTML) {
      emittedRef.current = next;
      quill.clipboard.dangerouslyPasteHTML(next);
    }
  }, [value]);

  return (
    <div id={id} className={`kh-richtext ${invalid ? 'is-invalid' : ''}`} style={{ '--kh-richtext-min': `${minHeight}px` }}>
      <div ref={hostRef} />
    </div>
  );
};
