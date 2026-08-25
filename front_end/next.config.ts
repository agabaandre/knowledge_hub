import path from "node:path";
import type { NextConfig } from "next";

const basePath = process.env.NEXT_PUBLIC_BASE_PATH || "/knowledge_hub/front_end";

const nextConfig: NextConfig = {
  reactCompiler: true,
  basePath,
  assetPrefix: basePath,
  trailingSlash: true,
  turbopack: {
    root: path.join(__dirname),
  },
};

export default nextConfig;
