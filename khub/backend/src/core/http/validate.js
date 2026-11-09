import { HttpError } from './errors.js';

/**
 * Validate `req.body`, `req.query` or `req.params` against a zod schema.
 * The parsed value replaces the raw one so handlers only see clean data.
 */
export const validate =
  (schema, source = 'body') =>
  (req, res, next) => {
    const result = schema.safeParse(req[source]);
    if (!result.success) {
      const details = result.error.issues.map((issue) => ({ path: issue.path.join('.'), message: issue.message }));
      return next(new HttpError(422, 'Validation failed', details));
    }
    if (source === 'query') req.validatedQuery = result.data;
    else req[source] = result.data;
    return next();
  };
