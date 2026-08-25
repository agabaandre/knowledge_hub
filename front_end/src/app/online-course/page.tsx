import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhHome from "@/nucleus/home/KhHome";
import { Metadata } from "next";

export const metadata: Metadata = {
  title: "Knowledge Hub — Online Course theme",
  description: "Africa CDC Knowledge Hub public frontend",
};

export default function OnlineCourse() {
  return (
    <ThemeShell forceTheme="online-course">
      <KhHome />
    </ThemeShell>
  );
}
