export class HttpError extends Error {
  constructor(status, message, details = undefined) {
    super(message);
    this.status = status;
    this.details = details;
  }
}

export const notFound = (message = 'Not found') => new HttpError(404, message);

/** Wrap an async route so rejections reach the error handler. */
export const asyncHandler = (handler) => (req, res, next) => Promise.resolve(handler(req, res, next)).catch(next);

/**
 * Last middleware: log the full error, answer with a safe message. Internal
 * details never reach the client in production.
 */
export const errorHandler =
  ({ logger, isProduction }) =>
  (error, req, res, next) => {
    const status = error.status ?? (error.type === 'entity.parse.failed' ? 400 : 500);
    if (status >= 500) logger.error({ err: error, url: req.originalUrl }, 'request failed');

    res.status(status).json({
      error: status >= 500 && isProduction ? 'Internal server error' : error.message,
      ...(error.details ? { details: error.details } : {}),
    });
  };
