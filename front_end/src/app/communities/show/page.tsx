import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhShowPage from "@/nucleus/pages/KhShowPage";
import { Metadata } from "next";
import { Suspense } from "react";

export const metadata: Metadata = { title: "Community" };

export default function CommunityShowPage() {
  return (
    <ThemeShell>
      <Suspense fallback={<p className="container section-space">Loading…</p>}>
        <KhShowPage title="Community" endpointPrefix="/communities/" />
      </Suspense>
    </ThemeShell>
  );
}
