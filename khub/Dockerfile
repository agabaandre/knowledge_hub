# Knowledge Hub — single image: builds the React app, then serves it from the Express API.
FROM node:22-alpine AS build
WORKDIR /app
COPY package.json package-lock.json ./
COPY shared/package.json shared/
COPY backend/package.json backend/
COPY frontend/package.json frontend/
RUN npm ci --no-audit --no-fund
COPY . .
RUN npm run build --workspace=frontend && npm prune --omit=dev

FROM node:22-alpine AS runtime
ENV NODE_ENV=production \
    BACKEND_PORT=4000 \
    SITES_DIR=sites \
    FRONTEND_DIST=/app/frontend/dist \
    HUB_DATA_ROOT=/var/khubdata
WORKDIR /app
COPY --from=build /app /app
RUN mkdir -p /var/khubdata && chown -R node:node /var/khubdata
USER node
EXPOSE 4000
HEALTHCHECK --interval=10s --timeout=3s CMD wget -qO- http://127.0.0.1:4000/health || exit 1
CMD ["sh", "-c", "node backend/scripts/migrate.js && node backend/src/server.js"]
