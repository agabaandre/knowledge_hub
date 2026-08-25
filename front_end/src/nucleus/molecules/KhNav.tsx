"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { KH_NAV, type KhNavLink } from "@/nucleus/theme/types";

export default function KhNav({ links = KH_NAV }: { links?: KhNavLink[] }) {
  const pathname = usePathname() || "/";

  return (
    <ul>
      {links.map((link) => {
        const active =
          link.href === "/"
            ? pathname === "/" || pathname === ""
            : pathname.startsWith(link.href.replace(/\/$/, ""));
        return (
          <li key={link.href} className={active ? "active" : undefined}>
            <Link href={link.href}>{link.label}</Link>
          </li>
        );
      })}
    </ul>
  );
}
