import { Router } from 'express';
import { asyncHandler } from '../../core/http/errors.js';

export default {
  name: 'faqs',
  router: () => {
    const router = Router();
    router.get(
      '/',
      asyncHandler(async (req, res) => {
        const rows = await req.db('faqs').select('id', 'question', 'answer').orderBy('id');
        res.json({ data: rows });
      }),
    );
    return router;
  },
};
