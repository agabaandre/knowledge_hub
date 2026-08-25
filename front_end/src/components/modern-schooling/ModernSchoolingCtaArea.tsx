import React from 'react';
import newslatterBg from '../../../public/assets/images/bg/newsletter-bg-01.webp';
import newslatterThumb from '../../../public/assets/images/newsletter/newsletter-thumb-02.webp';
import logo from '../../../public/assets/images/icon/logo.webp';
import Image from 'next/image';

const ModernSchoolingCtaArea = () => {
    return (
        <>
            {/* -- cta area start -- */}
            <section className="bd-cta-area section-space">
                <div className="container">
                    <div className="bd-newsletter-wrapper style-three">
                        <div className="bd-newsletter-bg" style={{ backgroundImage: `url(${newslatterBg.src})` }}></div>
                        <div className="row align-items-center justify-content-between g-30">
                            <div className="col-xxl-5 col-xl-6 col-lg-6 col-12 d-none d-lg-block">
                                <div className="bd-newsletter-thumb-wrapper p-relative">
                                    <div className="thumb">
                                        <Image src={newslatterThumb} style={{ width: "100%", height: "auto" }} priority alt="image" />
                                    </div>
                                    <div className="bd-newsletter-badge">
                                        <div className="bd-newsletter-badge-thumb">
                                            <Image src={logo} style={{ width: "100%", height: "auto" }} priority alt="image" />
                                        </div>
                                        <div className="bd-newsletter-badge-text">
                                            <span>Hi There! <br />
                                                Welcome to iStudy</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-xxl-7 col-xl-6 col-lg-6 col-12">
                                <div className="newsletter-content">
                                    <div className="bd-section-title-wrapper mb-30">
                                        <h2 className="bd-section-title white-text mb-0">Subscribe to Our Newsletter!</h2>
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
            {/* -- cta area end -- */}
        </>
    );
};

export default ModernSchoolingCtaArea;