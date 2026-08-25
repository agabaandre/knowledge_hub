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
  label: string;
};

export const KH_NAV: KhNavLink[] = [
  { href: "/", label: "Home" },
  { href: "/records/", label: "Records" },
  { href: "/health-topics/", label: "Health topics" },
  { href: "/forums/", label: "Forums" },
  { href: "/communities/", label: "Communities" },
  { href: "/faqs/", label: "FAQs" },
];

export const KH_HUB_LOGIN = "/knowledge_hub/login";
export const KH_HUB_PUBLISH = "/knowledge_hub/account/publish";
