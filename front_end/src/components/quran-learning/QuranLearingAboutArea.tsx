import Link from 'next/link';
import React from 'react';
import IslamicPatternShap from '../../../public/assets/images/shape/islamic-pattern.webp';
import aboutThumbImg from '../../../public/assets/images/about/about-thumb-06.webp';
import aboutCircleBookImg from '../../../public/assets/images/shape/about-circle-book.webp';
import aboutCircleExp from '../../../public/assets/images/shape/about-circle-exp.webp';
import aboutThumbImg2 from '../../../public/assets/images/about/about-thumb-06-2.webp';
import Image from 'next/image';

const QuranLearingAboutArea = () => {
    return (
        <>
            {/* -- about area start -- */}
            <section className="bd-about-area primary-bg section-space fix">
                <div className="container">
                    <div className="row gy-30 align-items-center">
                        <div className="col-xl-6 col-lg-6 col-md-12">
                            <div className="bd-about-wrapper style-five">
                                <div className="shape"><Image style={{width:"100%", height:"auto"}} src={IslamicPatternShap} alt="shape" /></div>
                                <div className="bd-about-thumb-wrapper justify-content-md-center">
                                    <div className="bd-about-thumb">
                                        <Image style={{width:"100%", height:"auto"}} src={aboutThumbImg} alt="image" />
                                    </div>
                                    <div className="text-center">
                                        <div className="bd-about-shape-wrap">
                                            <div className="main-shape">
                                                <Image style={{width:"100%", height:"auto"}} src={aboutCircleExp} alt="shape" />
                                                <div className="shape-two"><Image style={{width:"100%", height:"auto"}} src={aboutCircleBookImg} alt="shape" />
                                                </div>
                                            </div>
                                        </div>
                                        <div className="bd-about-thumb-two">
                                            <Image style={{width:"100%", height:"auto"}} src={aboutThumbImg2} alt="image" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-6 col-md-12">
                            <div className="bd-about-wrapper style-five">
                                <div className="bd-about-content-wrapper">
                                    <div className="bd-section-title-wrapper section-title-space">
                                        <span className="bd-section-subtitle">About Us</span>
                                        <h2 className="bd-section-title mb-20">We Believe Learning the Quran is a Lifelong
                                            Journey</h2>
                                        <p className="bd-section-paragraph has-border">Welcome to our online Quran learning
                                            platform, where we provide comprehensive guidance in Quranic studies and Islamic
                                            values, nurturing character development and lifelong spiritual growth.</p>
                                    </div>
                                    <div className="bd-about-feature-list mb-30">
                                        <ul>
                                            <li><i className="fa-regular fa-check"></i>Experienced Islamic Scholars</li>
                                            <li><i className="fa-regular fa-check"></i>Accessible Anytime, Anywhere</li>
                                            <li><i className="fa-regular fa-check"></i>Free Trial Classes Available</li>
                                        </ul>
                                    </div>
                                    <div className="bd-about-btn">
                                        <Link className="bd-btn btn-primary" href="/about-quran-learning">Learn More</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- about area end -- */}
        </>
    );
};

export default QuranLearingAboutArea;