import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhListingPage from "@/nucleus/pages/KhListingPage";
import { Metadata } from "next";

export const metadata: Metadata = { title: "Communities" };

export default function CommunitiesPage() {
  return (
    <ThemeShell>
      <KhListingPage
        titleKey="frontend_nav.communities"
        introKey="home_sections.communities_intro"
        endpoint="/communities?page_size=12"
        hrefBase="/communities/show/"
      />
    </ThemeShell>
  );
}
