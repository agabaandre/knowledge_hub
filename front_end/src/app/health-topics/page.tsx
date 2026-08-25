import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhListingPage from "@/nucleus/pages/KhListingPage";
import { Metadata } from "next";

export const metadata: Metadata = { title: "Health topics" };

export default function HealthTopicsPage() {
  return (
    <ThemeShell>
      <KhListingPage
        titleKey="frontend_nav.health_topics"
        introKey="home_sections.health_topics_intro"
        endpoint="/health-topics"
        hrefBase="/health-topics/show/"
      />
    </ThemeShell>
  );
}
