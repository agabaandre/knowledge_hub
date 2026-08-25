import { Roboto, Big_Shoulders } from "next/font/google";
import "./globals.scss";
import "swiper/css/bundle";
import "react-photo-view/dist/react-photo-view.css";
import "nouislider/dist/nouislider.css";
import "react-circular-progressbar/dist/styles.css";
import AppProvider from "@/contextApi/AppProvider";
import { Toaster } from "sonner";
import ReduxProvider from "@/redux/provider";
import { VideoProvider } from "@/contextApi/VideoProvider";
import GlobalVideoModal from "@/components/common/popup/GlobalVideoModal";
import { Metadata } from "next";
import "@/nucleus/theme/tokens.css";

// Load Roboto font
const roboto = Roboto({
  variable: "--font-roboto",
  subsets: ["latin"],
  weight: ["100", "300", "400", "500", "700", "900"],
});

// Load Big Shoulders Display font
const bigShoulders = Big_Shoulders({
  variable: "--font-big-shoulders",
  subsets: ["latin"],
  weight: ["100", "200", "300", "400", "500", "600", "700", "800", "900"],
});

export const metadata: Metadata = {
  title: "Knowledge Hub",
  description: "Africa CDC Knowledge Hub — records, forums, communities, and health topics.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body suppressHydrationWarning className={`body-bg ${roboto.variable} ${bigShoulders.variable}`}>
        <VideoProvider>
          <ReduxProvider>
            <AppProvider>
              {children}
            </AppProvider>
            <Toaster position="top-center" richColors />
            <GlobalVideoModal />
          </ReduxProvider>
        </VideoProvider>
      </body>
    </html>
  );
}