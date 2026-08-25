"use client";

import Link from "next/link";
import { khItemId, khItemSummary, khItemTitle, type KhListItem } from "@/nucleus/api/client";

export default function KhRecordCard({
  item,
  hrefBase,
}: {
  item: KhListItem;
  hrefBase: string;
}) {
  const id = khItemId(item);
  const href = id ? `${hrefBase}?id=${encodeURIComponent(id)}` : hrefBase;
  const title = khItemTitle(item);
  const summary = khItemSummary(item);

  return (
    <article className="bd-blog-wrapper style-two mb-30">
      <div className="bd-blog-content">
        <h4 className="bd-blog-title underline mb-10">
          <Link href={href}>{title}</Link>
        </h4>
        {summary ? <p>{summary}</p> : null}
      </div>
    </article>
  );
}
