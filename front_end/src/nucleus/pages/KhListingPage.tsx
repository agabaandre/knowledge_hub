"use client";

import { useEffect, useState } from "react";
import { khGet, type KhListItem } from "@/nucleus/api/client";
import KhPageHeader from "@/nucleus/molecules/KhPageHeader";
import KhRecordCard from "@/nucleus/molecules/KhRecordCard";

export default function KhListingPage({
  title,
  intro,
  endpoint,
  hrefBase,
}: {
  title: string;
  intro: string;
  endpoint: string;
  hrefBase: string;
}) {
  const [items, setItems] = useState<KhListItem[]>([]);
  const [error, setError] = useState("");

  useEffect(() => {
    khGet<Record<string, unknown>>(endpoint)
      .then((payload) => {
        const nested = payload.data as unknown;
        let rows: KhListItem[] = [];
        if (Array.isArray(nested)) {
          rows = nested;
        } else if (nested && typeof nested === "object") {
          const obj = nested as Record<string, unknown>;
          if (Array.isArray(obj.topics)) {
            rows = obj.topics as KhListItem[];
          } else if (Array.isArray(obj.data)) {
            rows = obj.data as KhListItem[];
          } else if (Array.isArray(obj.items)) {
            rows = obj.items as KhListItem[];
          }
        }
        setItems(Array.isArray(rows) ? rows : []);
      })
      .catch((err: Error) => setError(err.message));
  }, [endpoint]);

  return (
    <>
      <KhPageHeader title={title} intro={intro} />
      <section className="section-space">
        <div className="container">
          {error ? <p>{error}</p> : null}
          <div className="row gy-30">
            {items.map((item, index) => (
              <div className="col-xl-4 col-md-6" key={String(item.id ?? index)}>
                <KhRecordCard item={item} hrefBase={hrefBase} />
              </div>
            ))}
            {!error && items.length === 0 ? <p>No items yet.</p> : null}
          </div>
        </div>
      </section>
    </>
  );
}
