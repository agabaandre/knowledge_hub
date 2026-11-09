/**
 * Thin fetch wrapper. Sends cookies (session) and throws ApiError with the
 * backend's message so pages can show it directly.
 */
export class ApiError extends Error {
  constructor(status, message, details) {
    super(message);
    this.status = status;
    this.details = details;
  }
}

export const apiFetch = async (path, { method = 'GET', body, query, signal } = {}) => {
  const url = new URL(path, window.location.origin);
  for (const [key, value] of Object.entries(query ?? {})) {
    if (value === undefined || value === null || value === '') continue;
    if (Array.isArray(value)) value.forEach((item) => url.searchParams.append(key, String(item)));
    else url.searchParams.set(key, String(value));
  }

  const response = await fetch(url, {
    method,
    credentials: 'same-origin',
    signal,
    headers: body ? { 'Content-Type': 'application/json', Accept: 'application/json' } : { Accept: 'application/json' },
    body: body ? JSON.stringify(body) : undefined,
  });

  if (response.status === 204) return null;
  const payload = await response.json().catch(() => ({}));
  if (!response.ok) throw new ApiError(response.status, payload.error ?? `Request failed (${response.status})`, payload.details);
  return payload;
};
