import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhHome from "@/nucleus/home/KhHome";
import { Metadata } from "next";

export const metadata: Metadata = {
  title: "Knowledge Hub — Language Academy theme",
  description: "Africa CDC Knowledge Hub public frontend",
};

export default function LanguageAcademy() {
  return (
    <ThemeShell forceTheme="language-academy">
      <KhHome />
    </ThemeShell>
  );
}
