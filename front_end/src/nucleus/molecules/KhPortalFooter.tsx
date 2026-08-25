"use client";

import Link from "next/link";
import MainFooter from "@/layout/footer/MainFooter";
import KhPartnerBar from "@/nucleus/molecules/KhPartnerBar";
import { KH_NAV, type KhSettings } from "@/nucleus/theme/types";

function KhFooterLinks() {
  return (
    <div className="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
      <div className="bd-footer-widget">
        <h6 className="bd-footer-widget-title">Explore</h6>
        <div className="bd-footer-widget-links list-none">
          <ul>
            {KH_NAV.map((link) => (
              <li className="underline" key={link.href}>
                <Link href={link.href}>{link.label}</Link>
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
  const name = settings?.site_name || "Knowledge Hub";

  if (variant === "online-course") {
    return (
      <>
        <KhPartnerBar settings={settings} />
        <footer className="bd-footer-area style-two theme-bg-2">
          <div className="container">
            <div className="bd-footer-top section-space-small">
              <div className="row gy-30">
                <div className="col-lg-4">
                  <h4 className="white-text">{name}</h4>
                  <p className="white-text">{settings?.slogan || settings?.title || ""}</p>
                </div>
                <KhFooterLinks />
              </div>
            </div>
          </div>
        </footer>
      </>
    );
  }

  return (
    <>
      <KhPartnerBar settings={settings} />
      <MainFooter>
        <div className="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
          <div className="bd-footer-widget">
            <h6 className="bd-footer-widget-title">{name}</h6>
            <p>{settings?.slogan || settings?.title || "Africa CDC Knowledge Hub"}</p>
          </div>
        </div>
        <KhFooterLinks />
      </MainFooter>
    </>
  );
}
