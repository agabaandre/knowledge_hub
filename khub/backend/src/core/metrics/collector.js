import os from 'node:os';
import fs from 'node:fs/promises';
import { Registry, collectDefaultMetrics, Histogram, Gauge } from 'prom-client';

/**
 * Prometheus registry (process + HTTP metrics) and a JSON system snapshot
 * similar to the Laravel Storage Management "Overview" tab.
 */
export class MetricsCollector {
  constructor() {
    this.registry = new Registry();
    collectDefaultMetrics({ register: this.registry });

    this.httpDuration = new Histogram({
      name: 'khub_http_request_duration_seconds',
      help: 'HTTP request duration',
      labelNames: ['site', 'method', 'route', 'status'],
      buckets: [0.01, 0.05, 0.1, 0.25, 0.5, 1, 2.5, 5],
      registers: [this.registry],
    });

    this.dbUp = new Gauge({
      name: 'khub_database_up',
      help: '1 when the site database answers SELECT 1',
      labelNames: ['site'],
      registers: [this.registry],
    });
  }

  /** Express middleware timing every request. */
  httpMiddleware() {
    return (req, res, next) => {
      const end = this.httpDuration.startTimer();
      res.on('finish', () => {
        end({
          site: req.site?.id ?? 'unknown',
          method: req.method,
          route: req.route?.path ?? req.baseUrl ?? 'unmatched',
          status: String(res.statusCode),
        });
      });
      next();
    };
  }

  async databaseSnapshot(site) {
    const started = process.hrtime.bigint();
    try {
      await site.db.raw('select 1');
      this.dbUp.set({ site: site.id }, 1);
      const latencyMs = Number(process.hrtime.bigint() - started) / 1e6;
      const version = await this.#databaseVersion(site.db);
      return { status: 'ok', client: site.db.client.config.client, version, latencyMs: Math.round(latencyMs * 100) / 100 };
    } catch (error) {
      this.dbUp.set({ site: site.id }, 0);
      return { status: 'error', client: site.db.client.config.client, message: error.message };
    }
  }

  async #databaseVersion(db) {
    const row = await db.raw('select version() as version');
    const rows = Array.isArray(row) ? row[0] : row.rows;
    return rows?.[0]?.version ?? 'unknown';
  }

  async diskSnapshot(directory) {
    try {
      const stats = await fs.statfs(directory);
      const total = stats.blocks * stats.bsize;
      const free = stats.bavail * stats.bsize;
      const used = total - free;
      return { path: directory, totalBytes: total, freeBytes: free, usedBytes: used, usedPercent: total ? Math.round((used / total) * 1000) / 10 : 0 };
    } catch (error) {
      return { path: directory, error: error.message };
    }
  }

  runtimeSnapshot() {
    const memory = process.memoryUsage();
    return {
      node: process.version,
      platform: `${os.type()} ${os.release()}`,
      pid: process.pid,
      uptimeSeconds: Math.round(process.uptime()),
      processMemory: { rssBytes: memory.rss, heapUsedBytes: memory.heapUsed, heapTotalBytes: memory.heapTotal },
      systemMemory: { totalBytes: os.totalmem(), freeBytes: os.freemem(), usedPercent: Math.round(((os.totalmem() - os.freemem()) / os.totalmem()) * 1000) / 10 },
      cpu: { cores: os.cpus().length, loadAverage: os.loadavg().map((value) => Math.round(value * 100) / 100) },
    };
  }
}
