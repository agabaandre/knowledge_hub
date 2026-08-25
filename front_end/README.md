This is a [Next.js](https://nextjs.org) project bootstrapped with [`create-next-app`](https://nextjs.org/docs/app/api-reference/cli/create-next-app).

## Public URL

This app is served under the Knowledge Hub subdirectory, the same way APM exposes the staff portal at `/staff/staff-portal/`.

Local URL: [http://localhost/knowledge_hub/front_end](http://localhost/knowledge_hub/front_end)

## Production (Apache, no Node)

Same pattern as `staff-portal/setup-production.sh`: build a static export, publish it, Apache serves the files.

```bash
cd front_end
./setup-production.sh
```

That runs `npm ci`/`npm install` (with `--legacy-peer-deps` and a local `.npm-cache`), `npm run build`, then `scripts/publish-static.sh` copies `out/` to `public-spa/`. `front-end-proxy.php` serves those files first.

Re-deploy after `git pull`:

```bash
./setup-production.sh
```

Skip a rebuild if `out/` is already current:

```bash
./setup-production.sh --skip-build
```

Optional Apache snippet (`apache-front-end.conf`): websocket HMR for `npm run dev`, plus `Alias /assets` because the theme uses root-relative `/assets/...`. Do **not** `ProxyPass` the whole `/knowledge_hub/front_end` path — that hides the published files and 502s when Node is down. Include the file from `httpd.conf` after enabling `mod_proxy`, `mod_proxy_http`, and `mod_proxy_wstunnel`.

## Themes (nucleus / molecules)

The public portal at `/knowledge_hub/front_end/` uses a shared **nucleus** (`src/nucleus/`) plus three built-in chrome kits:

- University — `/university/` remains a design preview
- Language Academy — `/language-academy/`
- Online Course — `/online-course/`

Admin **Configure → Frontend** picks the default template (`frontend_theme`). The shell reads `/api/lookup/frontend-theme`. Knowledge Hub records, forums, communities, health topics, and FAQs load from `/api`.

To spin up a new pack, copy `src/themes/_starter/`, set `theme.json` `extends` to one of the three bases, zip it, and upload it on Configure. Packs overlay CSS tokens; they reuse nucleus molecules (nav, cards, partner bar).

## Development

To use live reload, remove the published tree so PHP proxies to Next on port 3001:

```bash
cd front_end
rm -rf public-spa
npm install --legacy-peer-deps
npm run dev
```

You can start editing the page by modifying `src/app/page.tsx`. The page auto-updates as you edit the file.

This project uses [`next/font`](https://nextjs.org/docs/app/building-your-application/optimizing/fonts) to automatically optimize and load Google fonts at build time.

## Learn More

To learn more about Next.js, take a look at the following resources:

- [Next.js Documentation](https://nextjs.org/docs) - learn about Next.js features and API.
- [Learn Next.js](https://nextjs.org/learn) - an interactive Next.js tutorial.
