"use client"
import Image from "next/image";
import Link from "next/link";
import TypedText from "@/utils/TypedText";
import bannerAward from "../../../public/assets/images/landing-page/banner/award.webp";
import homethumb1 from "../../../public/assets/images/landing-page/banner/index-1.webp";
import homethumb2 from "../../../public/assets/images/landing-page/banner/index-2.webp";
import homethumb3 from "../../../public/assets/images/landing-page/banner/index-3.webp";
import homethumb4 from "../../../public/assets/images/landing-page/banner/index-4.webp";
import homethumb5 from "../../../public/assets/images/landing-page/banner/index-5.webp";
import homethumb6 from "../../../public/assets/images/landing-page/banner/index-6.webp";
import homethumb7 from "../../../public/assets/images/landing-page/banner/index-7.webp";
import MouseMoveEffect from "../common/MouseMoveEffect";

const DemoBannerArea = () => {
  // Call the custom hook here
  MouseMoveEffect();

    return (
        <div className="bd-demo-banner-area p-relative theme-bg p-relative bd-noise-bg fix">
            <div className="container">
                <div className="row gy-30 align-items-center justify-content-center">
                    <div className="col-xxl-12 col-xl-12 col-lg-12">
                        <div className="bd-demo-banner-content text-center">
                            <div className="demo-banner-top-inner justify-content-center wow bdFadeInUp" data-wow-delay=".3s">
                                <div className="demo-banner-top">
                                    <div className="bd-icon rating-spacing-2">
                                        {[...Array(5)].map((_, index) => (
                                            <i key={index} className="icon-star"></i>
                                        ))}
                                    </div>
                                    <div className="content">
                                        <span className="subtitle">Satisfied Users</span>
                                    </div>
                                </div>
                                <div className="demo-banner-top">
                                    <div className="icon">
                                        <Image src={bannerAward} alt="Award Image" />
                                    </div>
                                    <div className="content">
                                        <span className="subtitle">#1 Top New Template</span>
                                    </div>
                                </div>
                            </div>
                            <div className="content cd-headline clip wow bdFadeInUp" data-wow-delay=".4s">
                                <h1 className="demo-banner-title mb-20">
                                    Build Your Dream Site in
                                    <br className="d-none d-sm-block" />
                                    Minutes for <br className="demo-break" />
                                    <span className="cd-words-wrapper cd-words-wrapper-two">
                                        <TypedText
                                            strings={[
                                                "Online Courses",
                                                "University",
                                                "Kindergarten",
                                                "Quran Learning",
                                                "Modern Schooling",
                                                "Language Academy",
                                                "Book Shop",
                                            ]}
                                        />
                                    </span>
                                </h1>
                                <p>Transform your educational platform with iStudy, a premium HTML5 template</p>
                            </div>
                            <div className="demo-banner-btn d-flex flex-wrap align-items-center justify-content-center gap-15 mt-30 wow bdFadeInUp" data-wow-delay=".6s">
                                <Link className="bd-btn btn-outline-border-white" href="#homesDemos">
                                    Explore Demo
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="bd-banner-demo-thumb shape-move d-none d-xl-block">
                {[
                    { href: "/online-course", img: homethumb1 },
                    { href: "/university", img: homethumb2 },
                    { href: "/modern-schooling", img: homethumb3 },
                    { href: "/kindergarten", img: homethumb4 },
                    { href: "/quran-learning", img: homethumb5 },
                    { href: "/shop", img: homethumb6 },
                    { href: "/language-academy", img: homethumb7 },
                ].map(({ href, img }, index) => (
                    <div key={index} className={`thumb-shape-0${index + 1} thumb-shape-common`}>
                        <Link href={href}>
                            <Image className={`shape-${index + 1}`} src={img} style={{ width: "100%", height: "auto" }} alt="image" priority />
                        </Link>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default DemoBannerArea;
