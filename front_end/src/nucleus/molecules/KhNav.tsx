"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useKhI18n } from "@/nucleus/i18n/KhI18nProvider";
import { KH_NAV, type KhNavLink } from "@/nucleus/theme/types";

export default function KhNav({ links = KH_NAV }: { links?: KhNavLink[] }) {
  const pathname = usePathname() || "/";
  const { t } = useKhI18n();

  return (
    <ul>
      {links.map((link) => {
        const active =
          link.href === "/"
            ? pathname === "/" || pathname === ""
            : pathname.startsWith(link.href.replace(/\/$/, ""));
        return (
          <li key={link.href} className={active ? "active" : undefined}>
            <Link href={link.href}>{t(link.labelKey)}</Link>
          </li>
        );
      })}
    </ul>
  );
}
