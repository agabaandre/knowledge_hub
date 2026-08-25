"use client";

import { useKhI18n } from "@/nucleus/i18n/KhI18nProvider";

export default function KhLanguageSelect() {
  const { locale, languages, t, setLocale } = useKhI18n();
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
    </div>
  );
}
