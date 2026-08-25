"use client";
import React, { useEffect, useState } from "react";
import { usePathname } from "next/navigation";
import BacktoTop from "@/utils/BacktoTop";
import FooterFour from "./footer/FooterFour";
import FooterTwo from "./footer/FooterTwo";
import HeaderThree from "./header/HeaderThree";
import HeaderTwo from "./header/HeaderTwo";
import HeaderOne from "./header/HeaderOne";
import FooterThree from "./footer/FooterThree";
import FooterOne from "./footer/FooterOne";
import HeaderFour from "./header/HeaderFour";
import FooterFive from "./footer/FooterFive";
import HeaderFive from "./header/HeaderFive";
import CommonFooter from "./footer/CommonFooter";
import KindergartentFooter from "./footer/KindergartentFooter";
import KindergartenHeader from "./header/KindergartenHeader";
import QuranLearningHeader from "./header/QuranLearningHeader";
import QuranLearningFooter from "./footer/QuranLearningFooter";
import LanguageAcademyHeader from "./header/LanguageAcademyHeader";
import Preloader from "@/components/common/preloader/Preloader";
import WOW from 'wow.js';

interface WrapperProps {
  children: React.ReactNode;
}

const Wrapper: React.FC<WrapperProps> = ({ children }) => {
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const pathName = usePathname();
  // Set the loading state to false after a timeout
  useEffect(() => {
    const loadingTimeout = setTimeout(() => setIsLoading(false), 1000);
    return () => clearTimeout(loadingTimeout);
  }, []);

  // Initialize WOW.js animations on the client
  useEffect(() => {
    const wow = new WOW({
      boxClass: 'wow',
      animateClass: 'animated',
      offset: 0,
      mobile: true,
      live: true,
    });
    wow.init();
  }, []);

  // Dynamic header and footer rendering based on the path
  const headerMap: Record<string, React.ReactNode> = {
    "/": <HeaderOne />,
    "/university": <HeaderThree />,
    "/online-course": <HeaderTwo />,
    "/kindergarten": <KindergartenHeader />,
    "/quran-learning": <QuranLearningHeader />,
    "/language-academy": <LanguageAcademyHeader />,
    "/book-store": <HeaderFour />,
  };

  const footerMap: Record<string, React.ReactNode> = {
    "/": <FooterOne />,
    "/online-course": <FooterTwo />,
    "/university": <FooterThree />,
    "/modern-schooling": <FooterFour />,
    "/kindergarten": <KindergartentFooter />,
    "/about-kindergarten": <KindergartentFooter />,
    "/quran-learning": <QuranLearningFooter />,
    "/book-store": <FooterFive />,
  };

  return (
    <>
      <BacktoTop />
      {headerMap[pathName] || <HeaderFive />}
      {isLoading ? <Preloader /> : children}
      {footerMap[pathName] || <CommonFooter />}
    </>
  );
};

export default Wrapper;
