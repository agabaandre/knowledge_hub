/**
 * Attach the resolved site to the request and strip its path prefix so every
 * module sees site-relative URLs (`/nigeria/api/forums` → `/api/forums`).
 */
export const siteMiddleware = (registry) => (req, res, next) => {
  const { site, pathPrefix } = registry.resolve({ hostname: req.hostname, path: req.path });
  req.site = site;
  req.db = site.db;
  req.sitePrefix = pathPrefix;

  if (pathPrefix && req.url.startsWith(pathPrefix)) {
    req.url = req.url.slice(pathPrefix.length) || '/';
  }

  res.setHeader('X-Hub-Site', site.id);
  next();
};
