"use client";

import Link from "next/link";
import KhPartnerBar from "@/nucleus/molecules/KhPartnerBar";
import { useKhI18n } from "@/nucleus/i18n/KhI18nProvider";
import { KH_NAV, type KhSettings } from "@/nucleus/theme/types";

function KhFooterLinks() {
  const { t } = useKhI18n();
  return (
    <div className="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
      <div className="bd-footer-widget">
        <h6 className="bd-footer-widget-title">{t("home_sections.footer_explore", "Explore")}</h6>
        <div className="bd-footer-widget-links list-none">
          <ul>
            {KH_NAV.map((link) => (
              <li className="underline" key={link.href}>
                <Link href={link.href}>{t(link.labelKey)}</Link>
              </li>
            ))}
          </ul>
        </div>
      </div>
    </div>
  );
}

export default function KhPortalFooter({
  variant,
  settings,
}: {
  variant: "university" | "language-academy" | "online-course";
  settings?: KhSettings | null;
}) {
  const { t } = useKhI18n();
  const name = settings?.site_name || t("ui_body.site_title", "Knowledge Hub");
  const tagline = settings?.slogan || settings?.title || t("ui_body.site_tagline", "");
  const year = new Date().getFullYear();
  const copyright = t("ui_body.footer_copyright", "© :year Africa CDC. All rights reserved.").replace(
    ":year",
    String(year)
  );

  return (
    <>
      <KhPartnerBar settings={settings} />
      <footer className={`bd-footer-area ${variant === "online-course" ? "style-two theme-bg-2" : "fix"}`}>
        <div className={`bd-footer-area-main-content ${variant === "online-course" ? "" : "section-space theme-bg-05"}`}>
          <div className="container">
            <div className={`row gy-30 ${variant === "online-course" ? "bd-footer-top section-space-small" : "justify-content-between"}`}>
              <div className="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                <div className="bd-footer-widget">
                  <h4 className={variant === "online-course" ? "white-text" : "bd-footer-widget-title"}>{name}</h4>
                  {tagline ? <p className={variant === "online-course" ? "white-text" : undefined}>{tagline}</p> : null}
                </div>
              </div>
              <KhFooterLinks />
            </div>
          </div>
        </div>
        <div className="bd-footer-copyright-area pt-25 pb-20">
          <div className="container">
            <p className="mb-0">{copyright}</p>
          </div>
        </div>
      </footer>
    </>
  );
}
