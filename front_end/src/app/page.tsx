import ThemeShell from "@/nucleus/theme/ThemeShell";
import KhHome from "@/nucleus/home/KhHome";
import { Metadata } from "next";

export const metadata: Metadata = {
  title: "Knowledge Hub",
  description: "Africa CDC Knowledge Hub public frontend",
};

export default function Home() {
  return (
    <ThemeShell>
      <KhHome />
    </ThemeShell>
  );
}
