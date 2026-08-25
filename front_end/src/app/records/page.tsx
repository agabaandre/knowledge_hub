import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhListingPage from "@/nucleus/pages/KhListingPage";
import { Metadata } from "next";

export const metadata: Metadata = { title: "Records" };

export default function RecordsPage() {
  return (
    <ThemeShell>
      <KhListingPage
        titleKey="frontend_nav.records"
        introKey="home_sections.records_intro"
        endpoint="/publications?page_size=12"
        hrefBase="/records/show/"
      />
    </ThemeShell>
  );
}
