import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhListingPage from "@/nucleus/pages/KhListingPage";
import { Metadata } from "next";

export const metadata: Metadata = { title: "FAQs" };

export default function FaqsPage() {
  return (
    <ThemeShell>
      <KhListingPage
        title="FAQs"
        intro="Frequently asked questions."
        endpoint="/faqs"
        hrefBase="/faqs/"
      />
    </ThemeShell>
  );
}
