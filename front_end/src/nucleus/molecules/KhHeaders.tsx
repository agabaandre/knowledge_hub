"use client";

import Link from "next/link";
import KhNav from "@/nucleus/molecules/KhNav";
import { KH_HUB_LOGIN, KH_HUB_PUBLISH, type KhSettings } from "@/nucleus/theme/types";

function Brand({ settings }: { settings?: KhSettings | null }) {
  const name = settings?.site_name || "Knowledge Hub";
  const logo = settings?.logo;
  return (
    <Link href="/" className="bd-header-logo d-flex align-items-center gap-2">
      {logo ? (
        // eslint-disable-next-line @next/next/no-img-element
        <img src={logo} alt={name} style={{ maxHeight: 48, width: "auto" }} />
      ) : null}
      <span className="fw-semibold">{name}</span>
    </Link>
  );
}

export function UniversityHeader({ settings }: { settings?: KhSettings | null }) {
  return (
    <header>
      <div className="header-style-two">
        <div className="bd-header-top style-two">
          <div className="bd-header-top-left">
            <ul>
              {settings?.phone ? (
                <li>
                  <a href={`tel:${settings.phone}`}>
                    <span>
                      <i className="fa-solid fa-phone-volume"></i>
                    </span>
                    {settings.phone}
                  </a>
                </li>
              ) : null}
              {settings?.email ? (
                <li>
                  <a href={`mailto:${settings.email}`}>
                    <span>
                      <i className="fa-sharp fa-light fa-envelope"></i>
                    </span>
                    {settings.email}
                  </a>
                </li>
              ) : null}
            </ul>
          </div>
          <div className="bd-header-top-right text-md-end">
            {settings?.address ? <span>{settings.address}</span> : <span>{settings?.slogan || ""}</span>}
          </div>
        </div>
        <div className="bd-header-area">
          <div className="bd-header-inner">
            <div className="bd-header-left">
              <Brand settings={settings} />
            </div>
            <div className="bd-header-menu">
              <nav className="main-menu d-none d-xl-block">
                <KhNav />
              </nav>
            </div>
            <div className="bd-header-right">
              <div className="bd-header-sign-btn">
                <a className="bd-btn-text text-primary" href={KH_HUB_LOGIN}>
                  Login
                </a>
                <a className="bd-btn btn-outline-border-primary h-40px" href={KH_HUB_PUBLISH}>
                  Publish
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}

export function LanguageAcademyHeader({ settings }: { settings?: KhSettings | null }) {
  return (
    <header>
      <div className="bd-header-area header-style-one">
        <div className="bd-header-inner">
          <div className="bd-header-left">
            <Brand settings={settings} />
          </div>
          <div className="bd-header-menu">
            <nav className="main-menu d-none d-xl-block">
              <KhNav />
            </nav>
          </div>
          <div className="bd-header-right">
            <a className="bd-btn btn-primary" href={KH_HUB_LOGIN}>
              Login
            </a>
          </div>
        </div>
      </div>
    </header>
  );
}

export function OnlineCourseHeader({ settings }: { settings?: KhSettings | null }) {
  return (
    <header>
      <div className="bd-header-transparent-two">
        <div className="bd-header-top style-three">
          <div className="bd-header-top-left">
            <ul>
              {settings?.email ? (
                <li>
                  <a href={`mailto:${settings.email}`}>{settings.email}</a>
                </li>
              ) : null}
            </ul>
          </div>
        </div>
        <div className="bd-header-area">
          <div className="bd-header-inner">
            <div className="bd-header-left">
              <Brand settings={settings} />
            </div>
            <div className="bd-header-menu">
              <nav className="main-menu d-none d-xl-block">
                <KhNav />
              </nav>
            </div>
            <div className="bd-header-right">
              <a className="bd-btn btn-primary" href={KH_HUB_LOGIN}>
                Login
              </a>
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}
