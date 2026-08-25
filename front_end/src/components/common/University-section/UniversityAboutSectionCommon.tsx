"use client"
import Image from 'next/image';
import React from 'react';
import circleShape from '../../../../public/assets/images/shape/about-circle-big.webp';
import circleShapeTwo from '../../../../public/assets/images/shape/about-circle-2.webp';
import bookShape from '../../../../public/assets/images/shape/book-2.webp';
import thumbShapeTwo from '../../../../public/assets/images/about/about-thumb-02.webp';
import thumbShapeThree from '../../../../public/assets/images/about/about-thumb-03.webp';
import userImage from '../../../../public/assets/images/user/user-01.webp';
import Link from 'next/link';
import CountUpContent from '../counter/CountUpContent';
import { usePathname } from 'next/navigation';

const UniversityAboutSectionCommon = () => {
    const pathName = usePathname();
    return (
        <section className="bd-about-area section-space p-relative">
            <div className="container">
                <div className="bd-about-shape-wrap d-none d-xl-block">
                    <div className="shape-5"><Image src={circleShape} style={{ width: "100%", height: "auto" }} alt="shape"/></div>
                    <div className="shape-6"><Image src={circleShapeTwo} style={{ width: "100%", height: "auto" }} alt="shape"/></div>
                    <div className="shape-7"><Image src={bookShape} style={{ width: "100%", height: "auto" }} alt="shape"/></div>
                </div>
                <div className="row g-30 justify-content-between">
                    <div className="col-xl-6 col-lg-6 col-md-12">
                        <div className="bd-about-wrapper style-two">
                            <div className="bd-about-thumb-inner">
                                <div className="bd-about-thumb-left">
                                    <div className="bd-about-experience">
                                        <h3 className="bd-about-experience-title">
                                            <CountUpContent number={20} text='+'/>
                                       </h3>
                                        <p>Years of Academic Excellence</p>
                                    </div>
                                    <div className="bd-about-thumb">
                                        <Image src={thumbShapeTwo} style={{ width: "100%", height: "auto" }} alt="Academic Excellence"/>
                                    </div>
                                </div>
                                <div className="bd-about-thumb-left">
                                    <div className="bd-about-thumb has-small">
                                        <Image src={thumbShapeThree} style={{ width: "100%", height: "auto" }} alt="Student Success"/>
                                    </div>
                                    <div className="bd-about-review">
                                        <div className="d-flex flex-wrap align-items-center gap-15 mb-10">
                                            <div className="bd-about-review-user">
                                                <Image src={userImage} priority alt="Student"/>
                                            </div>
                                            <div className="bd-about-review-info">
                                                <h3 className="bd-about-review-title">
                                                    <CountUpContent number={30} text='K+'/>
                                                </h3>
                                            </div>
                                        </div>
                                        <p>Satisfied Alumni Worldwide</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-6 col-lg-6 col-md-12">
                        <div className="bd-about-wrapper style-two">
                            <div className="bd-about-content-wrapper">
                                <div className="bd-section-title-wrapper">
                                    <span className="bd-section-subtitle">About Us</span>
                                    <h2 className="bd-section-title mb-20">Shaping Futures with Quality <span
                                        className="down-mark-line">Education</span></h2>
                                    <p className="bd-section-paragraph has-border">Our university is dedicated to providing
                                        transformation education, equipping students with the knowledge, skills, and
                                        experiences essential for lifelong success and global impact.</p>
                                </div>
                                <div className="bd-about-feature-list">
                                    <ul>
                                        <li><i className="fa-regular fa-check"></i>Education Award Achived</li>
                                        <li><i className="fa-regular fa-check"></i>Access Lifetime any Device</li>
                                        <li><i className="fa-regular fa-check"></i>No Credit Card Required</li>
                                    </ul>
                                </div>
                                <div className="bd-about-btn">
                                    {
                                        pathName == "/university" ? 
                                        <Link className="bd-btn btn-primary" href="/about-university">More Details</Link>
                                        : <Link className="bd-btn btn-outline-primary" href="/about-university">More Details</Link>
                                    }
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default UniversityAboutSectionCommon;