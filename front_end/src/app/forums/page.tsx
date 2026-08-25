import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhListingPage from "@/nucleus/pages/KhListingPage";
import { Metadata } from "next";

export const metadata: Metadata = { title: "Forums" };

export default function ForumsPage() {
  return (
    <ThemeShell>
      <KhListingPage
        title="Forums"
        intro="Discussions from the Knowledge Hub community."
        endpoint="/forums?page_size=12"
        hrefBase="/forums/show/"
      />
    </ThemeShell>
  );
}
