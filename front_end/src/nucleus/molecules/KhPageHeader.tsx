"use client";

import Link from "next/link";
import { useKhI18n } from "@/nucleus/i18n/KhI18nProvider";
import { KH_NAV } from "@/nucleus/theme/types";

export default function KhPageHeader({ title, intro }: { title: string; intro?: string }) {
  const { t } = useKhI18n();
  return (
    <section className="bd-breadcrumb-area p-relative fix">
      <div className="container">
        <div className="row">
          <div className="col-12">
            <div className="bd-breadcrumb-content">
              <h1 className="bd-breadcrumb-title">{title}</h1>
              {intro ? <p className="mt-15">{intro}</p> : null}
              <div className="bd-breadcrumb-list mt-15">
                {KH_NAV.map((link, index) => (
                  <span key={link.href}>
                    {index > 0 ? " / " : null}
                    <Link href={link.href}>{t(link.labelKey)}</Link>
                  </span>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
