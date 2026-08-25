"use client";

import BacktoTop from "@/utils/BacktoTop";
import { KhI18nProvider } from "@/nucleus/i18n/KhI18nProvider";
import { khGet } from "@/nucleus/api/client";
import { applyThemeTokens, themeChrome } from "@/nucleus/theme/registry";
import type { KhResolvedTheme, KhSettings, KhThemeId } from "@/nucleus/theme/types";
import { useEffect, useState } from "react";

const FALLBACK: KhResolvedTheme = {
  id: "university",
  name: "University",
  extends: "university",
  source: "builtin",
};

function ThemeChrome({
  children,
  forceTheme,
}: {
  children: React.ReactNode;
  forceTheme?: KhThemeId;
}) {
  const [theme, setTheme] = useState<KhResolvedTheme>(
    forceTheme ? { ...FALLBACK, id: forceTheme, extends: forceTheme } : FALLBACK
  );
  const [settings, setSettings] = useState<KhSettings | null>(null);

  useEffect(() => {
    let cancelled = false;
    Promise.all([
      khGet<{ data: KhResolvedTheme }>("/lookup/frontend-theme").catch(() => ({ data: FALLBACK })),
      khGet<{ data: KhSettings }>("/lookup/settings").catch(() => ({ data: {} })),
    ]).then(([themeRes, settingsRes]) => {
      if (cancelled) {
        return;
      }
      const resolved = themeRes.data ?? FALLBACK;
      setTheme(
        forceTheme
          ? { ...resolved, id: forceTheme, extends: forceTheme, source: "builtin" }
          : resolved
      );
      setSettings(settingsRes.data ?? {});
    });
    return () => {
      cancelled = true;
    };
  }, [forceTheme]);

  const base = (["university", "language-academy", "online-course"].includes(theme.extends)
    ? theme.extends
    : "university") as KhThemeId;
  const chrome = themeChrome(base);
  const Header = chrome.Header;
  const Footer = chrome.Footer;

  return (
    <div
      className={`kh-theme kh-theme--${base}`}
      data-kh-theme={theme.id}
      data-kh-extends={base}
      style={applyThemeTokens(theme, settings)}
    >
      {theme.css_url ? <link rel="stylesheet" href={theme.css_url} /> : null}
      <BacktoTop />
      <Header settings={settings} />
      <main className="main-area">{children}</main>
      <Footer variant={chrome.footerVariant} settings={settings} />
    </div>
  );
}

export default function ThemeShell({
  children,
  forceTheme,
}: {
  children: React.ReactNode;
  forceTheme?: KhThemeId;
}) {
  return (
    <KhI18nProvider>
      <ThemeChrome forceTheme={forceTheme}>{children}</ThemeChrome>
    </KhI18nProvider>
  );
}
