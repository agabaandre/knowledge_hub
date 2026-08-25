import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhShowPage from "@/nucleus/pages/KhShowPage";
import { Metadata } from "next";
import { Suspense } from "react";

export const metadata: Metadata = { title: "Health topic" };

export default function HealthTopicShowPage() {
  return (
    <ThemeShell>
      <Suspense fallback={<p className="container section-space">Loading…</p>}>
        <KhShowPage title="Health topic" endpointPrefix="/health-topics/" />
      </Suspense>
    </ThemeShell>
  );
}
