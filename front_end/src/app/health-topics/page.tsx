import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhListingPage from "@/nucleus/pages/KhListingPage";
import { Metadata } from "next";

export const metadata: Metadata = { title: "Health topics" };

export default function HealthTopicsPage() {
  return (
    <ThemeShell>
      <KhListingPage
        title="Health topics"
        intro="Browse health topics from the Knowledge Hub."
        endpoint="/health-topics"
        hrefBase="/health-topics/show/"
      />
    </ThemeShell>
  );
}
