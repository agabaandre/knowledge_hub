import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhListingPage from "@/nucleus/pages/KhListingPage";
import { Metadata } from "next";

export const metadata: Metadata = { title: "Forums" };

export default function ForumsPage() {
  return (
    <ThemeShell>
      <KhListingPage
        titleKey="frontend_nav.forums"
        introKey="home_sections.forums_intro"
        endpoint="/forums?page_size=12"
        hrefBase="/forums/show/"
      />
    </ThemeShell>
  );
}
