"use client";

import Link from "next/link";
import { KH_NAV } from "@/nucleus/theme/types";

export default function KhPageHeader({ title, intro }: { title: string; intro?: string }) {
  return (
    <section className="bd-breadcrumb-area p-relative fix">
      <div className="container">
        <div className="row">
          <div className="col-12">
            <div className="bd-breadcrumb-content">
              <h1 className="bd-breadcrumb-title">{title}</h1>
              {intro ? <p className="mt-15">{intro}</p> : null}
              <div className="bd-breadcrumb-list mt-15">
                <Link href="/">Home</Link>
                {KH_NAV.filter((link) => link.href !== "/").map((link) => (
                  <span key={link.href}>
                    {" / "}
                    <Link href={link.href}>{link.label}</Link>
                  </span>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
