export type KhThemeId = "university" | "language-academy" | "online-course";

export type KhThemeSource = "builtin" | "upload";

export type KhThemeTokens = Record<string, string>;

export type KhResolvedTheme = {
  id: string;
  name: string;
  extends: KhThemeId;
  source: KhThemeSource;
  css_url?: string;
  tokens?: KhThemeTokens;
};

export type KhSettings = {
  site_name?: string;
  slogan?: string;
  title?: string;
  email?: string;
  phone?: string;
  address?: string;
  logo?: string;
  partner_logos?: Array<{ file?: string; name?: string; url?: string; image?: string }>;
  show_partner_names?: boolean;
  partner_logo_max_height?: number;
  frontend_theme?: string;
};

export type KhNavLink = {
  href: string;
  labelKey: string;
};

export const KH_NAV: KhNavLink[] = [
  { href: "/", labelKey: "frontend_nav.home" },
  { href: "/records/", labelKey: "frontend_nav.records" },
  { href: "/health-topics/", labelKey: "frontend_nav.health_topics" },
  { href: "/forums/", labelKey: "frontend_nav.forums" },
  { href: "/communities/", labelKey: "frontend_nav.communities" },
  { href: "/faqs/", labelKey: "frontend_nav.faqs" },
];

export const KH_HUB_LOGIN = "/knowledge_hub/login";
export const KH_HUB_PUBLISH = "/knowledge_hub/account/publish";
