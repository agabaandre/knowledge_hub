/**
 * Minimal HTML sanitizer for rich-text fields (Quill output). Removes script
 * vectors while keeping formatting tags. Not a full parser — the editor only
 * produces a small tag set, and the public pages render this with innerHTML,
 * so strip anything executable.
 */
export const sanitizeRichText = (html) => {
  if (!html) return html;
  let clean = String(html);
  // whole dangerous elements, with their content
  clean = clean.replace(/<(script|style|iframe|object|embed|form)\b[\s\S]*?<\/\1>/gi, '');
  clean = clean.replace(/<(script|style|iframe|object|embed|form|link|meta)\b[^>]*\/?>/gi, '');
  // event handlers and javascript: / data: urls inside attributes
  clean = clean.replace(/\son\w+\s*=\s*("[^"]*"|'[^']*'|[^\s>]+)/gi, '');
  clean = clean.replace(/\s(href|src)\s*=\s*(["']?)\s*(javascript|vbscript|data):[^"'\s>]*\2/gi, '');
  return clean;
};
