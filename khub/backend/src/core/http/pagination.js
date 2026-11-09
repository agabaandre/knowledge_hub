import { z } from 'zod';

export const MAX_PAGE_SIZE = 50;
export const DEFAULT_PAGE_SIZE = 12;

export const paginationSchema = z.object({
  page: z.coerce.number().int().min(1).default(1),
  per_page: z.coerce.number().int().min(1).max(MAX_PAGE_SIZE).default(DEFAULT_PAGE_SIZE),
});

export const paginate = (items, total, { page, per_page: perPage }) => ({
  data: items,
  meta: {
    page,
    perPage,
    total,
    lastPage: Math.max(1, Math.ceil(total / perPage)),
  },
});
