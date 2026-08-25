"use client";

import Link from "next/link";
import {
  khItemAuthor,
  khItemCover,
  khItemDate,
  khItemId,
  khItemSummary,
  khItemTitle,
  type KhListItem,
} from "@/nucleus/api/client";
import { useKhI18n } from "@/nucleus/i18n/KhI18nProvider";

export default function KhRecordCard({
  item,
  hrefBase,
}: {
  item: KhListItem;
  hrefBase: string;
}) {
  const { t } = useKhI18n();
  const id = khItemId(item);
  const href = id ? `${hrefBase}?id=${encodeURIComponent(id)}` : hrefBase;
  const title = khItemTitle(item, t("frontend_nav.untitled", "Untitled"));
  const summary = khItemSummary(item);
  const cover = khItemCover(item);
  const author = khItemAuthor(item);
  const date = khItemDate(item);

  return (
    <article className="bd-blog-wrapper style-four">
      {cover ? (
        <div className="bd-blog-thumb">
          <Link href={href}>
            {/* Remote Knowledge Hub covers are not in the Next image pipeline. */}
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img src={cover} alt={title} />
          </Link>
        </div>
      ) : null}
      <div className="bd-blog-content">
        {author || date ? (
          <div className="bd-blog-meta-list">
            {author ? (
              <div className="bd-blog-meta-item has-separator-black">
                <span className="meta-icon">
                  <i className="fa-solid fa-user"></i>
                </span>
                <span className="meta-text">{author}</span>
              </div>
            ) : null}
            {date ? (
              <div className="bd-blog-meta-item">
                <span className="meta-icon">
                  <i className="fa-sharp fa-light fa-calendar-days"></i>
                </span>
                <span className="meta-text">{date}</span>
              </div>
            ) : null}
          </div>
        ) : null}
        <h5 className="title underline">
          <Link href={href}>{title}</Link>
        </h5>
        {summary ? <p>{summary}</p> : null}
        <div className="icon-text-btn p-relative">
          <Link href={href}>
            <span>{t("frontend_nav.read_more", "Read more")}</span>
          </Link>
        </div>
      </div>
    </article>
  );
}
