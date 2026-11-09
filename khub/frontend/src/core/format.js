export const formatDate = (value) => {
  if (!value) return '';
  const date = new Date(value);
  return Number.isNaN(date.getTime()) ? '' : date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
};

export const formatNumber = (value) => Number(value ?? 0).toLocaleString('en-US');

export const formatBytes = (bytes) => {
  if (!bytes && bytes !== 0) return '—';
  const units = ['B', 'KB', 'MB', 'GB', 'TB'];
  let size = bytes;
  let unit = 0;
  while (size >= 1024 && unit < units.length - 1) {
    size /= 1024;
    unit += 1;
  }
  return `${size.toFixed(unit === 0 ? 0 : 1)} ${units[unit]}`;
};

/**
 * Settings can hold absolute upload URLs minted on another host (the Laravel
 * app's APP_URL at upload time). Serve them from the current origin instead:
 * "http://old-host/hub-media/x.png" → "/hub-media/x.png".
 */
export const mediaUrl = (url) => {
  if (!url) return url;
  const match = String(url).match(/\/hub-media\/.+$/);
  return match ? match[0] : url;
};

/** "3 hours ago" style relative time for chat-like UIs. */
export const timeAgo = (value) => {
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return '';
  const seconds = Math.max(0, (Date.now() - date.getTime()) / 1000);
  const steps = [[31536000, 'year'], [2592000, 'month'], [604800, 'week'], [86400, 'day'], [3600, 'hour'], [60, 'minute']];
  for (const [span, unit] of steps) {
    if (seconds >= span) {
      const count = Math.floor(seconds / span);
      return `${count} ${unit}${count === 1 ? '' : 's'} ago`;
    }
  }
  return 'just now';
};
