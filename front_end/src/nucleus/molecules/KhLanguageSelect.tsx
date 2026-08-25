"use client";

import { useKhI18n } from "@/nucleus/i18n/KhI18nProvider";

export default function KhLanguageSelect() {
  const { locale, languages, directionMode, t, setLocale, setDirectionMode } = useKhI18n();
  const active = languages.find((row) => row.code === locale);

  return (
    <div className="kh-language-select d-flex align-items-center gap-2">
      <label className="visually-hidden" htmlFor="kh-language">
        {t("frontend_nav.language", "Language")}
      </label>
      <select
        id="kh-language"
        className="form-select form-select-sm"
        value={locale}
        onChange={(event) => setLocale(event.target.value)}
        aria-label={t("frontend_nav.language", "Language")}
      >
        {(languages.length ? languages : [{ code: "en", name: "English", flag: "🇺🇸" }]).map((row) => (
          <option key={row.code} value={row.code}>
            {row.flag ? `${row.flag} ` : ""}
            {row.name}
          </option>
        ))}
      </select>
      <span className="kh-language-flag" aria-hidden="true">
        {active?.flag || ""}
      </span>
      <label className="visually-hidden" htmlFor="kh-direction">
        {t("frontend_nav.text_direction", "Text direction")}
      </label>
      <select
        id="kh-direction"
        className="form-select form-select-sm"
        value={directionMode}
        onChange={(event) => setDirectionMode(event.target.value as "auto" | "ltr" | "rtl")}
        aria-label={t("frontend_nav.text_direction", "Text direction")}
      >
        <option value="auto">{t("frontend_nav.layout_auto", "Auto")}</option>
        <option value="ltr">{t("frontend_nav.layout_ltr", "LTR")}</option>
        <option value="rtl">{t("frontend_nav.layout_rtl", "RTL")}</option>
      </select>
    </div>
  );
}
