import React from 'react';
import Shape1 from '../../../../../public/assets/images/shape/banner-4-shape-5.webp';
import Shape2 from '../../../../../public/assets/images/shape/banner-4-shape-3.webp';
import Shape3 from '../../../../../public/assets/images/shape/banner-4-shape-1.webp';
import Shape4 from '../../../../../public/assets/images/shape/banner-4-shape-2.webp';
import ctaThumb from '../../../../../public/assets/images/cta/cta-six-thumb.webp';
import Image from 'next/image';

const CtaAreaStart = () => {
    return (
        <>
            {/* -- cta area start -- */}
            <section className="bd-cta-wrapper bd-cta-section-wave style-six gradient-bg section-space">
                <div className="container">
                    <div className="bd-cta-shape-wrap d-none d-lg-block">
                        <div className="shape-1"><Image src={Shape1} alt="shape" /></div>
                        <div className="shape-2"><Image src={Shape2} alt="shape" /></div>
                        <div className="shape-3"><Image src={Shape3} alt="shape" /></div>
                        <div className="shape-4"><Image src={Shape4} alt="shape" /></div>
                    </div>
                    <div className="row gy-30 justify-content-between align-items-center">
                        <div className="col-xxl-5 col-lg-8 col-md-12">
                            <div className="bd-cta-content rel-z-2">
                                <div className="bd-section-title-wrapper section-title-space">
                                    <h2 className="bd-section-title">Begin a Bright Future – Join Our Kindergarten!</h2>
                                </div>
                                <form action="#">
                                    <div className="bd-newsletter-input">
                                        <input type="text" placeholder="Your email" />
                                        <button type="submit" className="bd-btn btn-primary radius-6">Subscribe
                                            Now</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div className="col-xxl-5 col-lg-6 d-none d-xl-block">
                            <div className="bd-cta-thumb"><Image src={ctaThumb} alt="image" /></div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- cta area end -- */}
        </>
    );
};

export default CtaAreaStart;