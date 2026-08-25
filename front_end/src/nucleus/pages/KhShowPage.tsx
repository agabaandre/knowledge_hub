"use client";

import { useSearchParams } from "next/navigation";
import { useEffect, useState } from "react";
import { khGet, khItemSummary, khItemTitle, type KhListItem } from "@/nucleus/api/client";
import KhPageHeader from "@/nucleus/molecules/KhPageHeader";

export default function KhShowPage({
  title,
  endpointPrefix,
}: {
  title: string;
  endpointPrefix: string;
}) {
  const params = useSearchParams();
  const id = params.get("id") || "";
  const [item, setItem] = useState<KhListItem | null>(null);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!id) {
      setError("Missing item id.");
      return;
    }
    khGet<{ data?: KhListItem } | KhListItem>(`${endpointPrefix}${id}`)
      .then((payload) => {
        const row = (payload as { data?: KhListItem }).data ?? (payload as KhListItem);
        setItem(row);
      })
      .catch((err: Error) => setError(err.message));
  }, [id, endpointPrefix]);

  return (
    <>
      <KhPageHeader title={item ? khItemTitle(item) : title} />
      <section className="section-space">
        <div className="container">
          {error ? <p>{error}</p> : null}
          {item ? <div dangerouslySetInnerHTML={{ __html: khItemSummary(item) }} /> : null}
        </div>
      </section>
    </>
  );
}
