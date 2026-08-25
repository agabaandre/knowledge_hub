import Image from 'next/image';
import React from 'react';
import PromotionThumb from '../../../../../public/assets/images/about/promotion-thumb-2.webp';

const NurturingMindsSection = () => {
    return (
        <>
            <section className="bd-promotion-area section-space">
                <div className="container">
                    <div className="row gy-30 align-items-center justify-content-between">
                        <div className="col-xl-6 col-lg-6 col-md-12 order-lg-0 order-1">
                            <div className="bd-about-wrapper">
                                <div className="bd-about-content-wrapper">
                                    <div className="bd-section-title-wrapper section-title-space">
                                        <h2 className="bd-section-title mb-20">Nurturing Young Minds</h2>
                                        <p className="bd-section-paragraph">Our kindergarten fosters a caring and stimulating
                                            environment
                                            where every child feels valued and encouraged to explore their potential.</p>
                                        <p className="bd-section-paragraph">Inspired by innovative teaching methods, we provide
                                            the tools
                                            and support to help children grow academically, socially, and emotionally.</p>
                                    </div>
                                    <div className="bd-promotion-list-two">
                                        <ul>
                                            <li>
                                                <div className="bd-promotion-icon theme-bg">
                                                    <i className="fa-sharp fa-light fa-award-simple"></i>
                                                </div>
                                                <span>Comprehensive Learning</span>
                                            </li>
                                            <li>
                                                <div className="bd-promotion-icon secondary-bg">
                                                    <i className="fa-light fa-chalkboard"></i>
                                                </div>
                                                <span>Creative Activities</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-6 col-md-12 order-lg-1 order-0">
                            <div className="bd-promotion-wrapper">
                                <div className="bd-promotion-thumb-mask p-relative">
                                    <Image src={PromotionThumb} style={{width:"100%", height:"auto"}} alt="image" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default NurturingMindsSection;