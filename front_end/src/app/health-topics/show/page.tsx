import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhShowPage from "@/nucleus/pages/KhShowPage";
import KhLoading from "@/nucleus/molecules/KhLoading";
import { Metadata } from "next";
import { Suspense } from "react";

export const metadata: Metadata = { title: "Health topic" };

export default function HealthTopicShowPage() {
  return (
    <ThemeShell>
      <Suspense fallback={<KhLoading />}>
        <KhShowPage titleKey="frontend_nav.health_topics" endpointPrefix="/health-topics/" />
      </Suspense>
    </ThemeShell>
  );
}
