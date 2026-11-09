import { useEffect, useState } from 'react';
import { apiFetch } from '../../core/api.js';
import { useModule } from '../../core/site-context.jsx';

/**
 * AI search insights panel (the Laravel search page's AI assistant block):
 * a grounded summary of the top matches for the current term. Renders nothing
 * when the module is off, no provider is configured, or the term is short.
 */
export const AiInsights = ({ term }) => {
  const enabled = useModule('ai-search');
  const [state, setState] = useState({ status: 'idle' });
  const clean = String(term ?? '').trim();

  useEffect(() => {
    if (!enabled || clean.length < 2) {
      setState({ status: 'idle' });
      return undefined;
    }
    let cancelled = false;
    setState({ status: 'loading' });
    apiFetch('/api/ai-search/insights', { method: 'POST', body: { term: clean } })
      .then((payload) => !cancelled && setState({ status: 'ready', ...payload }))
      .catch(() => !cancelled && setState({ status: 'unavailable' }));
    return () => { cancelled = true; };
  }, [enabled, clean]);

  if (!enabled || clean.length < 2 || state.status === 'idle' || state.status === 'unavailable') return null;

  return (
    <div className="bg-white rounded border p-3 mb-3" style={{ borderLeft: '4px solid var(--theme-color-primary, #006239)' }} aria-live="polite">
      <p className="ft-medium mb-1" style={{ color: 'var(--theme-color-primary, #006239)' }}>
        <i className="fa fa-wand-magic-sparkles mr-1" aria-hidden="true" /> AI insights
      </p>
      {state.status === 'loading' && <p className="mb-0 text-muted medium">Summarising the top matches…</p>}
      {state.status === 'ready' && (
        <>
          <p className="mb-1" style={{ whiteSpace: 'pre-wrap' }}>{state.summary}</p>
          <p className="text-muted mb-0" style={{ fontSize: '0.75rem' }}>
            AI-generated from the top matching resources (via {state.provider}). Verify against the sources below.
          </p>
        </>
      )}
    </div>
  );
};
