import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhShowPage from "@/nucleus/pages/KhShowPage";
import { Metadata } from "next";
import { Suspense } from "react";

export const metadata: Metadata = { title: "Forum" };

export default function ForumShowPage() {
  return (
    <ThemeShell>
      <Suspense fallback={<p className="container section-space">Loading…</p>}>
        <KhShowPage title="Forum" endpointPrefix="/forums/" />
      </Suspense>
    </ThemeShell>
  );
}
