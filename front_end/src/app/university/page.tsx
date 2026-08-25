import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhHome from "@/nucleus/home/KhHome";
import { Metadata } from "next";

export const metadata: Metadata = {
  title: "Knowledge Hub — University theme",
  description: "Africa CDC Knowledge Hub public frontend",
};

export default function University() {
  return (
    <ThemeShell forceTheme="university">
      <KhHome />
    </ThemeShell>
  );
}
