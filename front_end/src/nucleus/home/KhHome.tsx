"use client";

import { useEffect, useState } from "react";
import { khGet, type KhListItem } from "@/nucleus/api/client";
import { useKhI18n } from "@/nucleus/i18n/KhI18nProvider";
import KhRecordCard from "@/nucleus/molecules/KhRecordCard";
import KhSection from "@/nucleus/molecules/KhSection";
import type { KhSettings } from "@/nucleus/theme/types";

type HomeSection = {
  key: string;
  title: string;
  visible?: boolean;
  items?: KhListItem[];
};

export default function KhHome() {
  const { t, locale } = useKhI18n();
  const [settings, setSettings] = useState<KhSettings | null>(null);
  const [sections, setSections] = useState<HomeSection[]>([]);
  const [error, setError] = useState("");

  useEffect(() => {
    Promise.all([
      khGet<{ data: KhSettings }>("/lookup/settings"),
      khGet<{ data?: { sections?: HomeSection[] } }>("/home"),
    ])
      .then(([settingsRes, homeRes]) => {
        setSettings(settingsRes.data ?? null);
        setSections(homeRes.data?.sections ?? []);
      })
      .catch((err: Error) => setError(err.message));
  }, [locale]);

  const visible = sections.filter((section) => section.visible !== false && (section.items ?? []).length > 0);
  const title = settings?.site_name || t("ui_body.site_title", "Knowledge Hub");
  const tagline = settings?.slogan || settings?.title || t("ui_body.site_tagline", "");

  return (
    <>
      <section className="bd-hero-area section-space theme-bg">
        <div className="container">
          <div className="row">
            <div className="col-xl-8">
              <h1 className="bd-section-title white-text mb-20">{title}</h1>
              {tagline ? <p className="white-text">{tagline}</p> : null}
            </div>
          </div>
        </div>
      </section>
      {error ? (
        <div className="container section-space">
          <p>{error}</p>
        </div>
      ) : null}
      {visible.map((section) => (
        <KhSection key={section.key} title={t(`home_sections.${section.key}`, section.title)}>
          {(section.items ?? []).slice(0, 6).map((item, index) => (
            <div className="col-xl-4 col-md-6" key={String(item.id ?? index)}>
              <KhRecordCard item={item} hrefBase="/records/show/" />
            </div>
          ))}
        </KhSection>
      ))}
    </>
  );
}
