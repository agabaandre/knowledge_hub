"use client";

import { useEffect, useState } from "react";
import { khGet, type KhListItem } from "@/nucleus/api/client";
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
  }, []);

  const visible = sections.filter((section) => section.visible !== false && (section.items ?? []).length > 0);

  return (
    <>
      <section className="bd-hero-area section-space theme-bg">
        <div className="container">
          <div className="row">
            <div className="col-xl-8">
              <h1 className="bd-section-title white-text mb-20">{settings?.site_name || "Knowledge Hub"}</h1>
              <p className="white-text">{settings?.slogan || settings?.title || "Public health knowledge for Africa."}</p>
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
        <KhSection key={section.key} title={section.title}>
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
