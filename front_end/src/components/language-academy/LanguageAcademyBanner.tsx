"use client";
import React from "react";
import Image from "next/image";
import Link from "next/link";
// Import images
import unitedStatesFlag from '../../../public/assets/images/banner/banner-7/united-states.png';
import arabicFlag from '../../../public/assets/images/banner/banner-7/arabic.png';
import chinaFlag from '../../../public/assets/images/banner/banner-7/china.png';
import germanyFlag from '../../../public/assets/images/banner/banner-7/germany.png';
import spainFlag from '../../../public/assets/images/banner/banner-7/spain.png';
import moreLanguagesFlag from '../../../public/assets/images/banner/banner-7/more.png';
import bannerThumb from '../../../public/assets/images/banner/banner-7/banner-seven-thumb.png';
import badgeImg from '../../../public/assets/images/shape/badge-img.webp';
import starGroupImg from '../../../public/assets/images/shape/star-group.webp';
import bannerMapImg from '../../../public/assets/images/banner/banner-7/banner-map.png';
import alphabetArabicImg from '../../../public/assets/images/banner/banner-7/alphabet-arabic.png';
import alphabetRussianImg from '../../../public/assets/images/banner/banner-7/alphabet-russian.png';
import alphabetHindiImg from '../../../public/assets/images/banner/banner-7/alphabet-hindi.png';
import alphabetBanglaImg from '../../../public/assets/images/banner/banner-7/alphabet-bangla.png';
import alphabetKoreanImg from '../../../public/assets/images/banner/banner-7/alphabet-korean.png';
import MouseMoveEffect from "../common/MouseMoveEffect";

// Language options
const languages = [
    { src: unitedStatesFlag, name: "English" },
    { src: arabicFlag, name: "Arabic" },
    { src: chinaFlag, name: "Mandarin" },
    { src: germanyFlag, name: "German" },
    { src: spainFlag, name: "Spanish" },
    { src: moreLanguagesFlag, name: "More Languages" },
];

const LanguageAcademyBanner: React.FC = () => {
    MouseMoveEffect();

    return (
        <section className="bd-banner-area bd-banner-seven theme-bg-05 p-relative fix">
            {/* Floating shapes */}
            <div className="shape-move d-none d-sm-block">
                <div className="shape-move d-none d-sm-block">
                    <div className="bd-banner-seven-shape">
                        <Image style={{ width: "100%", height: "auto" }} src={bannerMapImg} alt="map" />
                    </div>
                    <div className="bd-banner-seven-shape-two">
                        <Image style={{ width: "100%", height: "auto" }} src={alphabetArabicImg} alt="alphabet" />
                    </div>
                    <div className="bd-banner-seven-shape-three">
                        <Image style={{ width: "100%", height: "auto" }} src={alphabetRussianImg} alt="alphabet" />
                    </div>
                    <div className="bd-banner-seven-shape-four">
                        <Image style={{ width: "100%", height: "auto" }} src={alphabetHindiImg} alt="alphabet" />
                    </div>
                    <div className="bd-banner-seven-shape-five">
                        <Image style={{ width: "100%", height: "auto" }} src={alphabetBanglaImg} alt="alphabet" />
                    </div>
                    <div className="bd-banner-seven-shape-six">
                        <Image style={{ width: "100%", height: "auto" }} src={alphabetKoreanImg} alt="alphabet" />
                    </div>
                </div>
            </div>

            <div className="container custom-container">
                <div className="row gy-30 align-items-center justify-content-between">
                    <div className="col-xxl-6 col-xl-6 col-lg-12">
                        <div className="bd-banner-content p-relative">
                            <h1 className="bd-banner-title mb-20">
                                Which <span className="text-primary">language</span> would you like to learn?
                            </h1>
                            <p className="bd-banner-description">
                                Explore new cultures and expand your horizons by learning a new language. Start your journey today!
                            </p>
                            <div className="bd-banner-language">
                                {languages.map((lang, index) => (
                                    <Link key={index} className="item" href="/courses/course-details-three">
                                        <div className="language-thumb">
                                            <Image src={lang.src} alt={lang.name} width={40} height={40} />
                                        </div>
                                        <div className="language-name">{lang.name}</div>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </div>

                    <div className="col-xxl-5 col-xl-5 col-lg-12">
                        <div className="p-relative">
                            <div className="bd-banner-seven-thumb">
                                <Image src={bannerThumb} alt="Banner Thumbnail" layout="responsive" priority />
                            </div>
                            <div className="bd-banner-tag-wrapper">
                                <div className="bd-banner-tag-two">
                                    <div className="inner">
                                        <div className="icon">
                                            <Image src={badgeImg} alt="Badge" layout="intrinsic" />
                                            <Image src={starGroupImg} alt="Stars" layout="intrinsic" />
                                        </div>
                                        <div className="content">
                                            <div className="title">30K+</div>
                                            <div className="subtitle">
                                                Happy Student <br /> Worldwide
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default LanguageAcademyBanner;
