import React from 'react';
import shapeImg from '../../../../public/assets/images/shape/two-star.webp';
import Image from 'next/image';

const NewsLetterStyleTwo = () => {
    return (
        <>
                {/* -- newsletter style 02 start -- */}
        <section className="bd-elements-newsletter section-space-bottom">
            <div className="container">
                <div className="row align-items-center justify-content-center">
                    <div className="col-lg-12">
                        <div className="bd-elements-section-wrapper section-title-space text-center">
                            <div className="bd-elements-line">
                                <div className="bd-separator-line line-left"></div>
                                <div className="bd-elements-title-wrapper">
                                    <h2 className="bd-elements-title small">Newsletter Style 02</h2>
                                </div>
                                <div className="bd-separator-line line-right"></div>
                            </div>
                            <p className=""></p>
                        </div>
                    </div>
                </div>
                <div className="row gy-30 justify-content-center">
                    <div className="bd-newsletter-wrapper style-two">
                        <div className="container">
                            <div className="bd-newsletter-item bd-newsletter-bg">
                                <div className="row align-items-center">
                                    <div className="col-lg-6">
                                        <div className="bd-section-title-wrapper">
                                            <h2 className="bd-section-title white-text mb-0">Let’s Join To Our Newsletters</h2>
                                        </div>
                                    </div>
                                    <div className="col-lg-6">
                                        <form action="#">
                                            <div className="bd-newsletter-input">
                                                <input type="text" placeholder="Your email"/>
                                                <button type="submit" className="bd-btn btn-primary radius-6">Subscribe
                                                    Now</button>
                                            </div>
                                        </form>
                                        <p className="description">*get <span>30%</span> off on Subscribe</p>
                                    </div>
                                </div>
                                <div className="shape">
                                    <Image src={shapeImg} alt="image"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {/* -- newsletter style 02 end -- */}
        </>
    );
};

export default NewsLetterStyleTwo;