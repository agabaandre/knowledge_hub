import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhShowPage from "@/nucleus/pages/KhShowPage";
import KhLoading from "@/nucleus/molecules/KhLoading";
import { Metadata } from "next";
import { Suspense } from "react";

export const metadata: Metadata = { title: "Community" };

export default function CommunityShowPage() {
  return (
    <ThemeShell>
      <Suspense fallback={<KhLoading />}>
        <KhShowPage titleKey="frontend_nav.communities" endpointPrefix="/communities/" />
      </Suspense>
    </ThemeShell>
  );
}
