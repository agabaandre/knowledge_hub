"use client";

import React from "react";
import Image from "next/image";

import iconOne from "../../../public/assets/images/banner/banner-3/banner-3-icon-1.webp";
import iconTwo from "../../../public/assets/images/banner/banner-3/banner-3-icon-2.webp";
import iconThree from "../../../public/assets/images/banner/banner-3/banner-3-icon-3.webp";

import bannerThumbImg from "../../../public/assets/images/banner/banner-3/banner-3-img-1.webp";
import dotCircleImg from "../../../public/assets/images/shape/dot-circle.webp";
import starGroupImg from "../../../public/assets/images/shape/star-group.webp";
import badgeImg from "../../../public/assets/images/shape/badge-img-2.webp";
import bannerIcon1 from "../../../public/assets/images/banner/banner-3/banner-3-shape-1.webp";
import bannerIcon2 from "../../../public/assets/images/banner/banner-3/banner-3-shape-2.webp";
import bannerIcon3 from "../../../public/assets/images/banner/banner-3/banner-3-shape-3.webp";
import bannerIcon4 from "../../../public/assets/images/banner/banner-3/banner-3-shape-4.webp";
import bannerIcon5 from "../../../public/assets/images/banner/banner-3/banner-3-shape-5.webp";
import bannerIcon6 from "../../../public/assets/images/banner/banner-3/banner-3-shape-6.webp";
import useMouseEffect from "@/hooks/useMouseEffect";

const counterItems = [
    { icon: iconOne, title: "4000+", subtitle: "Online courses" },
    { icon: iconTwo, title: "150+", subtitle: "Expert instructor" },
    { icon: iconThree, title: "2500+", subtitle: "Enrolled Students" },
];

const shapeImages = [
    bannerIcon1,
    bannerIcon2,
    bannerIcon3,
    bannerIcon4,
    bannerIcon5,
    bannerIcon6,
];

const ModernSchoolingBanner: React.FC = () => {
    // This hook enables smooth shape movements based on mouse position.
    // It adds a dynamic effect to the banner, improving the user experience.
    useMouseEffect();

    return (
        <section className="bd-banner-area bd-banner-three fix">
            <div className="container">
                <div className="row gy-30 align-items-center">
                    <div className="col-xxl-6 col-xl-6 col-lg-12">
                        <h1 className="bd-banner-title small mb-20">
                            Discover the Top <span className="text-primary">Courses</span> to Enhance Your Skills
                        </h1>
                        <p className="bd-banner-description">
                            Universal is dedicated to offering educational experiences designed to align with new and evolving career paths.
                        </p>
                        <div className="bd-banner-search-form">
                            <div className="bd-banner-search-form-input p-relative mb-30">
                                <input type="text" placeholder="Search Program" />
                                <button className="bd-btn-icon btn-primary radius-10" type="button">
                                    <i className="fa-light fa-magnifying-glass"></i>
                                </button>
                            </div>
                            <div className="features-list">
                                {counterItems.map(({ icon, title, subtitle }, index) => (
                                    <div className="features-list-item" key={index}>
                                        <span className="thumb">
                                            <Image src={icon} alt="icon" style={{ width: "100%", height: "auto" }} />
                                        </span>
                                        <div className="content">
                                            <h6 className="title">{title}</h6>
                                            <p className="subtitle">{subtitle}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                    <div className="col-xxl-6 col-xl-6 col-lg-12">
                        <div className="p-relative">
                            <div className="bd-banner-thumb">
                                <Image src={bannerThumbImg} alt="image" priority style={{ width: "100%", height: "auto" }} />
                            </div>
                            <div className="bd-banner-tag-wrapper">
                                <div className="bd-banner-tag-one">
                                    <div className="shape-one">
                                        <Image src={dotCircleImg} alt="shape" style={{ width: "100%", height: "auto" }} />
                                    </div>
                                    <Image className="shape-two" src={starGroupImg} priority alt="shape" />
                                    <div className="inner">
                                        <div className="icon">
                                            <Image src={badgeImg} alt="shape" style={{ width: "100%", height: "auto" }} />
                                        </div>
                                        <div className="title">
                                            <span>300+</span> Instructors
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="bd-banner-shape shape-move d-none d-xl-block">
                    {shapeImages.map((shape, index) => (
                        <div className={`shape-${index + 1} p-absolute`} key={index}>
                            <Image src={shape} alt={`shape-${index + 1}`} style={{ width: "100%", height: "auto" }} />
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default ModernSchoolingBanner;
