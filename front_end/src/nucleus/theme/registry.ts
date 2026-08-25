"use client";

import { LanguageAcademyHeader, OnlineCourseHeader, UniversityHeader } from "@/nucleus/molecules/KhHeaders";
import KhPortalFooter from "@/nucleus/molecules/KhPortalFooter";
import type { KhResolvedTheme, KhSettings, KhThemeId } from "@/nucleus/theme/types";

export const THEME_IDS: KhThemeId[] = ["university", "language-academy", "online-course"];

export function themeChrome(id: KhThemeId) {
  if (id === "language-academy") {
    return { Header: LanguageAcademyHeader, Footer: KhPortalFooter, footerVariant: "language-academy" as const };
  }
  if (id === "online-course") {
    return { Header: OnlineCourseHeader, Footer: KhPortalFooter, footerVariant: "online-course" as const };
  }
  return { Header: UniversityHeader, Footer: KhPortalFooter, footerVariant: "university" as const };
}

export const builtinThemes: Array<Pick<KhResolvedTheme, "id" | "name" | "extends" | "source">> = [
  { id: "university", name: "University", extends: "university", source: "builtin" },
  { id: "language-academy", name: "Language Academy", extends: "language-academy", source: "builtin" },
  { id: "online-course", name: "Online Course", extends: "online-course", source: "builtin" },
];

export function applyThemeTokens(theme: KhResolvedTheme, settings?: KhSettings | null): Record<string, string> {
  const style: Record<string, string> = {};
  const tokens = theme.tokens ?? {};
  if (tokens.primary) {
    style["--bd-primary"] = tokens.primary;
  }
  if (tokens.secondary) {
    style["--bd-secondary"] = tokens.secondary;
  }
  if (settings && typeof settings === "object") {
    const primary = (settings as { primary_color?: string }).primary_color;
    if (primary && !tokens.primary) {
      style["--bd-primary"] = primary;
    }
  }
  return style;
}
