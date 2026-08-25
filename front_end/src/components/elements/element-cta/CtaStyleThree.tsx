import Link from 'next/link';
import React from 'react';
import ctaBgImage from '../../../../public/assets/images/cta/cta-bg-2.webp';
import Image from 'next/image';


const CtaStyleThree = () => {
    return (
        <>
            {/* -- cta style 03 start -- */}
            <section className="bd-elements-cta section-space">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">CTA Style 03</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-xl-12">
                            <div className="bd-cta-wrapper style-three p-relative">
                                <div className="thumb">
                                    <Image src={ctaBgImage} alt="image" />
                                </div>
                                <div className="bd-cta-box">
                                    <div className="bd-section-title-wrapper p-relative">
                                        <span className="bd-section-subtitle white-text">call us now</span>
                                        <h2 className="bd-section-title white-text mb-20">Request a consultation!</h2>
                                        <p className="bd-section-paragraph white-text">Available 24/7 on weekdays to answer your
                                            inquiries, provide assistance</p>
                                    </div>
                                    <div className="bd-cta-content d-flex-column gap-15 p-relative">
                                        <div className="bd-btn-icon btn-secondary btn-extra-large radius-50 pulse-white">
                                            <i className="icon-call-ring"></i>
                                        </div>
                                        <Link className="number" href="tel:+1(967)019242590">+1(967) 0192 425 90</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- cta style 03 end --  */}
        </>
    );
};

export default CtaStyleThree;