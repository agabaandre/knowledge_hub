"use client";

import { useSearchParams } from "next/navigation";
import { useEffect, useState } from "react";
import { khGet, khItemCover, khItemSummary, khItemTitle, type KhListItem } from "@/nucleus/api/client";
import { useKhI18n } from "@/nucleus/i18n/KhI18nProvider";
import KhPageHeader from "@/nucleus/molecules/KhPageHeader";

export default function KhShowPage({
  titleKey,
  endpointPrefix,
}: {
  titleKey: string;
  endpointPrefix: string;
}) {
  const { t } = useKhI18n();
  const params = useSearchParams();
  const id = params.get("id") || "";
  const [item, setItem] = useState<KhListItem | null>(null);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!id) {
      setError(t("frontend_nav.missing_item"));
      return;
    }
    khGet<{ data?: KhListItem } | KhListItem>(`${endpointPrefix}${id}`)
      .then((payload) => {
        const row = (payload as { data?: KhListItem }).data ?? (payload as KhListItem);
        setItem(row);
      })
      .catch((err: Error) => setError(err.message));
  }, [id, endpointPrefix, t]);

  const cover = item ? khItemCover(item) : "";
  const title = item ? khItemTitle(item, t("frontend_nav.untitled")) : t(titleKey);

  return (
    <>
      <KhPageHeader title={title} />
      <section className="section-space">
        <div className="container">
          {error ? <p>{error}</p> : null}
          {cover ? (
            <div className="mb-30">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={cover} alt={title} style={{ maxWidth: "100%", height: "auto" }} />
            </div>
          ) : null}
          {item ? <div dangerouslySetInnerHTML={{ __html: String(item.description ?? item.summary ?? item.excerpt ?? khItemSummary(item)) }} /> : null}
        </div>
      </section>
    </>
  );
}
