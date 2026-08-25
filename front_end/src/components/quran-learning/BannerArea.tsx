"use client";

import Link from 'next/link';
import React from 'react';
import Image from 'next/image';

// Importing images
import BannerLanternImg from '../../../public/assets/images/banner/banner-5/lantrin.webp';
import BottomTreeImg from '../../../public/assets/images/banner/banner-5/bottom-tree.webp';
import TopTreeImg from '../../../public/assets/images/banner/banner-5/top-tree.webp';
import MoonImg from '../../../public/assets/images/banner/banner-5/moon.webp';
import BannerDotImg from '../../../public/assets/images/banner/banner-5/banner-5-dot.webp';
import ThumbMaskImg from '../../../public/assets/images/banner/banner-5/thumb-mask.webp';
import BannerMainImg from '../../../public/assets/images/banner/banner-5/banner-5-img-1.webp';
import { useVideoModal } from '@/contextApi/VideoProvider';

const socialLinks = [
    { href: "https://www.facebook.com/", icon: "fa-facebook-f" },
    { href: "https://x.com/", icon: "fa-x-twitter" },
    { href: "https://www.linkedin.com/feed/", icon: "fa-linkedin-in" },
    { href: "https://www.instagram.com/", icon: "fa-instagram" },
];

const BannerArea: React.FC = () => {
    const { playVideo } = useVideoModal();

    return (
        <>
            <section className="bd-banner-area bd-banner-five theme-bg bd-noise-bg p-relative fix">
                <div className="container">
                    <div className="bd-banner-shape">
                        {[BannerLanternImg, BottomTreeImg, TopTreeImg, MoonImg, BannerDotImg].map((img, index) => (
                            <div key={index} className={`shape-${index + 1} p-absolute`}>
                                <Image src={img} alt={`Shape ${index + 1}`} />
                            </div>
                        ))}
                    </div>

                    <div className="row align-items-center">
                        <div className="col-xxl-5 col-xl-5 col-lg-6">
                            <div className="bd-banner-section-wrapper">
                                <span className="bd-banner-subtitle text-white mb-10 d-block fw-6">
                                    Study the Quran for Tranquility
                                </span>
                                <h1 className="bd-banner-title mb-20">
                                    Unveil the Marvels of <span className="text-secondary">The Quran</span>
                                </h1>
                                <p className="bd-banner-description">
                                    They are not liable for any offense caused by those who fail to carry out their duties, unless it is due to negligence.
                                </p>
                                <div className="bd-banner-btn d-flex align-items-center gap-30">
                                    <Link className="bd-btn btn-outline-border-white" href="/courses-list-two">
                                        Find Courses
                                    </Link>
                                    <button type='button' onClick={() => playVideo("HKk4oLIzhhM", "youtube")} className="bd-video-btn secondary popup-video">
                                        <span className="icon">
                                            <i className="fa-solid fa-play"></i>
                                        </span>
                                        Watch Video
                                    </button>
                                </div>
                                <div className="bd-banner-social">
                                    <span>Follow us</span>
                                    <div className="social-icon-style-01">
                                        <ul className="social-icon-list">
                                            {socialLinks.map(({ href, icon }) => (
                                                <li key={icon}>
                                                    <Link href={href} target="_blank">
                                                        <i className={`fa-brands ${icon}`}></i>
                                                    </Link>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="col-xxl-7 col-xl-7 col-lg-6">
                            <div className="bd-banner-thumb-wrapper">
                                <div className="bd-banner-thumb-shape">
                                    <Image src={ThumbMaskImg} alt="Thumb Mask" style={{ width: "auto", height: "auto" }}/>
                                </div>
                                <div className="bd-banner-thumb">
                                    <Image src={BannerMainImg} alt="Banner Main Image" priority style={{ width: "auto", height: "auto" }}/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default BannerArea;
