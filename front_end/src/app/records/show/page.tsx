import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhShowPage from "@/nucleus/pages/KhShowPage";
import { Metadata } from "next";
import { Suspense } from "react";

export const metadata: Metadata = { title: "Record" };

export default function RecordShowPage() {
  return (
    <ThemeShell>
      <Suspense fallback={<p className="container section-space">Loading…</p>}>
        <KhShowPage title="Record" endpointPrefix="/publications/" />
      </Suspense>
    </ThemeShell>
  );
}
