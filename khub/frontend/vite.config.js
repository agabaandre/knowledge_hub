import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

const backend = process.env.BACKEND_URL ?? 'http://localhost:4000';

// Server-side paths the backend owns; everything else is the SPA.
const proxied = ['/api', '/hub-media', '/metrics', '/health', '/sitemap.xml', '/sitemaps', '/robots.txt', '/llms.txt'];

export default defineConfig({
  plugins: [react()],
  server: {
    proxy: Object.fromEntries(proxied.map((path) => [path, { target: backend, changeOrigin: true }])),
  },
  build: { outDir: 'dist', sourcemap: true },
  test: { environment: 'jsdom', setupFiles: ['tests/setup.js'] },
});
