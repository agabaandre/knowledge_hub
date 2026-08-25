import React from 'react';
import newsletterImage from '../../../../public/assets/images/newsletter/newsletter-03.webp';
import newsletterBg from '../../../../public/assets/images/newsletter/newsletter-3-bg.webp';
import Image from 'next/image';
import NewsletterBadgeIcon from '@/svg/NewsletterBadgeIcon';

const NewsLetterStyleOne = () => {
    return (
        <>
            {/* -- newsletter style 01 start -- */}
            <section className="bd-elements-newsletter section-space">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Newsletter Style 01</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="bd-newsletter-wrapper style-three mt-150 p-relative z-index-11">
                        <div className="bd-newsletter-bg" style={{ backgroundImage: `url(${newsletterBg.src})` }}></div>
                        <div className="row gy-30 align-items-center justify-content-between">
                            <div className="col-xxl-5 col-xl-6 col-lg-6 col-12">
                                <div className="bd-newsletter-thumb-wrapper p-relative">
                                    <div className="thumb">
                                        <Image src={newsletterImage} style={{width:"100%", height:"auto"}} alt="image" />
                                    </div>
                                    <div className="bd-newsletter-badge">
                                        <div className="bd-newsletter-badge-icon">
                                            <span>
                                                <NewsletterBadgeIcon />
                                            </span>
                                        </div>
                                        <div className="bd-newsletter-badge-text">
                                            <span>Hi Alex! <br />
                                                Welcome to iStudy</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-xxl-7 col-xl-6 col-lg-6 col-12">
                                <div className="newsletter-content">
                                    <div className="bd-section-title-wrapper mb-30">
                                        <h2 className="bd-section-title white-text mb-0">Subscribe to our Newsletter!</h2>
                                    </div>
                                    <div className="bd-newsletter-form">
                                        <form action="#">
                                            <div className="bd-newsletter-input">
                                                <input type="text" placeholder="Your email" />
                                                <button type="submit" className="bd-btn btn-primary radius-6">Subscribe
                                                    Now</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- newsletter style 01 end -- */}
        </>
    );
};

export default NewsLetterStyleOne;