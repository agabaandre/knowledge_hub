"use client";

import { useKhI18n } from "@/nucleus/i18n/KhI18nProvider";

export default function KhLoading() {
  const { t } = useKhI18n();
  return <p className="container section-space">{t("frontend_nav.loading")}</p>;
}
