"use client";

import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { khGet, khReadCookie, khWriteCookie } from "@/nucleus/api/client";

export type KhDirectionMode = "auto" | "ltr" | "rtl";

export type KhLanguage = {
  code: string;
  name: string;
  flag: string;
  google_code?: string;
  direction?: "ltr" | "rtl";
};

type KhI18nPayload = {
  locale: string;
  direction: "ltr" | "rtl";
  direction_mode?: KhDirectionMode;
  is_rtl?: boolean;
  rtl_locales?: string[];
  ltr_available?: boolean;
  languages?: KhLanguage[];
  labels?: Record<string, string>;
};

type KhI18nContextValue = {
  locale: string;
  direction: "ltr" | "rtl";
  directionMode: KhDirectionMode;
  languages: KhLanguage[];
  labels: Record<string, string>;
  ready: boolean;
  t: (key: string, fallback?: string) => string;
  setLocale: (code: string) => void;
  setDirectionMode: (mode: KhDirectionMode) => void;
};

const KhI18nContext = createContext<KhI18nContextValue | null>(null);

const FALLBACK_LABELS: Record<string, string> = {
  "frontend_nav.home": "Home",
  "frontend_nav.records": "Records",
  "frontend_nav.health_topics": "Health Topics",
  "frontend_nav.forums": "Forums",
  "frontend_nav.communities": "Communities",
  "frontend_nav.faqs": "FAQs",
  "frontend_nav.login": "Login",
  "frontend_nav.publish": "Publish",
  "frontend_nav.read_more": "Read more",
  "frontend_nav.language": "Language",
  "frontend_nav.layout_auto": "Auto",
  "frontend_nav.layout_ltr": "LTR",
  "frontend_nav.layout_rtl": "RTL",
  "frontend_nav.text_direction": "Text direction",
  "frontend_nav.no_items": "No items yet.",
  "frontend_nav.loading": "Loading…",
  "frontend_nav.missing_item": "Missing item id.",
  "frontend_nav.untitled": "Untitled",
  "home_sections.footer_explore": "Explore",
};

function applyDocumentDirection(locale: string, direction: "ltr" | "rtl") {
  if (typeof document === "undefined") {
    return;
  }
  document.documentElement.lang = locale;
  document.documentElement.dir = direction;
  document.documentElement.classList.toggle("rtl", direction === "rtl");
  document.body?.classList.toggle("rtl", direction === "rtl");
}

export function KhI18nProvider({ children }: { children: ReactNode }) {
  const [locale, setLocaleState] = useState(() => khReadCookie("khub_locale") || "en");
  const [directionMode, setDirectionModeState] = useState<KhDirectionMode>(() => {
    const stored = khReadCookie("khub_dir");
    return stored === "ltr" || stored === "rtl" || stored === "auto" ? stored : "auto";
  });
  const [direction, setDirection] = useState<"ltr" | "rtl">("ltr");
  const [languages, setLanguages] = useState<KhLanguage[]>([]);
  const [labels, setLabels] = useState<Record<string, string>>(FALLBACK_LABELS);
  const [ready, setReady] = useState(false);

  const load = useCallback(async (nextLocale: string, nextMode: KhDirectionMode) => {
    const payload = await khGet<{ data?: KhI18nPayload }>(
      `/lookup/i18n?direction=${encodeURIComponent(nextMode)}`,
      nextLocale
    );
    const data = payload.data ?? ({} as KhI18nPayload);
    const resolvedLocale = data.locale || nextLocale;
    const resolvedDirection = data.direction === "rtl" ? "rtl" : "ltr";
    setLocaleState(resolvedLocale);
    setDirection(resolvedDirection);
    setDirectionModeState(data.direction_mode === "ltr" || data.direction_mode === "rtl" ? data.direction_mode : nextMode);
    setLanguages(Array.isArray(data.languages) ? data.languages : []);
    setLabels({ ...FALLBACK_LABELS, ...(data.labels ?? {}) });
    applyDocumentDirection(resolvedLocale, resolvedDirection);
    setReady(true);
  }, []);

  useEffect(() => {
    let cancelled = false;
    load(locale, directionMode).catch(() => {
      if (!cancelled) {
        applyDocumentDirection(locale, locale === "ar" && directionMode !== "ltr" ? "rtl" : "ltr");
        setReady(true);
      }
    });
    return () => {
      cancelled = true;
    };
    // Initial load only; later changes go through setters.
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const setLocale = useCallback(
    (code: string) => {
      khWriteCookie("khub_locale", code);
      setLocaleState(code);
      load(code, directionMode).catch(() => undefined);
    },
    [directionMode, load]
  );

  const setDirectionMode = useCallback(
    (mode: KhDirectionMode) => {
      khWriteCookie("khub_dir", mode);
      setDirectionModeState(mode);
      load(locale, mode).catch(() => undefined);
    },
    [locale, load]
  );

  const t = useCallback(
    (key: string, fallback?: string) => {
      const value = labels[key];
      if (typeof value === "string" && value !== "") {
        return value;
      }
      return fallback ?? FALLBACK_LABELS[key] ?? key;
    },
    [labels]
  );

  const value = useMemo(
    () => ({
      locale,
      direction,
      directionMode,
      languages,
      labels,
      ready,
      t,
      setLocale,
      setDirectionMode,
    }),
    [locale, direction, directionMode, languages, labels, ready, t, setLocale, setDirectionMode]
  );

  return <KhI18nContext.Provider value={value}>{children}</KhI18nContext.Provider>;
}

export function useKhI18n(): KhI18nContextValue {
  const ctx = useContext(KhI18nContext);
  if (!ctx) {
    return {
      locale: "en",
      direction: "ltr",
      directionMode: "auto",
      languages: [],
      labels: FALLBACK_LABELS,
      ready: false,
      t: (key, fallback) => fallback ?? FALLBACK_LABELS[key] ?? key,
      setLocale: () => undefined,
      setDirectionMode: () => undefined,
    };
  }
  return ctx;
}
