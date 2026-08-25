"use client";

import type { KhSettings } from "@/nucleus/theme/types";

export default function KhPartnerBar({ settings }: { settings?: KhSettings | null }) {
  const items = (settings?.partner_logos ?? []).filter((row) => row.image || row.file);
  if (!items.length) {
    return null;
  }
  const maxHeight = Math.max(50, Math.min(200, Number(settings?.partner_logo_max_height ?? 100)));
  const showNames = Boolean(settings?.show_partner_names);

  return (
    <div className="kh-partner-bar" style={{ background: "#fff", padding: "1.5rem 0" }}>
      <div className="container">
        <div className="d-flex flex-wrap align-items-center justify-content-center gap-4">
          {items.map((item, index) => {
            const src = item.image || item.file || "";
            const inner = (
              // eslint-disable-next-line @next/next/no-img-element
              <img src={src} alt={item.name || "Partner"} style={{ maxHeight, width: "auto" }} />
            );
            return (
              <div key={`${src}-${index}`} className="text-center">
                {item.url ? (
                  <a href={item.url} target="_blank" rel="noopener noreferrer">
                    {inner}
                  </a>
                ) : (
                  inner
                )}
                {showNames && item.name ? <div className="small mt-1">{item.name}</div> : null}
              </div>
            );
          })}
        </div>
      </div>
    </div>
  );
}
