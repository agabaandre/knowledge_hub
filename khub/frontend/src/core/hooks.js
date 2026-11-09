import { useEffect, useState } from 'react';
import { apiFetch } from './api.js';

/**
 * Fetch JSON whenever `path` or `query` changes. Aborts stale requests so
 * fast typing or paging never shows out-of-order results.
 */
export const useApi = (path, query, { enabled = true } = {}) => {
  const [state, setState] = useState({ data: null, error: null, loading: enabled });
  const key = JSON.stringify([path, query]);

  useEffect(() => {
    if (!enabled || !path) return undefined;
    const controller = new AbortController();
    setState((previous) => ({ ...previous, loading: true, error: null }));
    apiFetch(path, { query: JSON.parse(key)[1], signal: controller.signal })
      .then((data) => setState({ data, error: null, loading: false }))
      .catch((error) => {
        if (error.name !== 'AbortError') setState({ data: null, error, loading: false });
      });
    return () => controller.abort();
  }, [key, enabled, path]);

  return state;
};
