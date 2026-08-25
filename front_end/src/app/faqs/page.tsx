import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhListingPage from "@/nucleus/pages/KhListingPage";
import { Metadata } from "next";

export const metadata: Metadata = { title: "FAQs" };

export default function FaqsPage() {
  return (
    <ThemeShell>
      <KhListingPage
        titleKey="frontend_nav.faqs"
        introKey="home_sections.faqs_intro"
        endpoint="/faqs"
        hrefBase="/faqs/"
      />
    </ThemeShell>
  );
}
