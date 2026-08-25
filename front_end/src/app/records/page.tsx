import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhListingPage from "@/nucleus/pages/KhListingPage";
import { Metadata } from "next";

export const metadata: Metadata = { title: "Records" };

export default function RecordsPage() {
  return (
    <ThemeShell>
      <KhListingPage
        title="Records"
        intro="Search and browse publications from the Knowledge Hub."
        endpoint="/publications?page_size=12"
        hrefBase="/records/show/"
      />
    </ThemeShell>
  );
}
