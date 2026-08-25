import React from 'react';
import Image from 'next/image';

import CircleShape from '../../../public/assets/images/shape/circle-sec.png';
import Square1 from '../../../public/assets/images/shape/square-1.png';
import Ellipse1 from '../../../public/assets/images/shape/ellipse-1.png';
import CircleWhite from '../../../public/assets/images/shape/circle-w.png';
import Ellipse2 from '../../../public/assets/images/shape/ellipse-2.png';
import Square2 from '../../../public/assets/images/shape/square-2.png';

const NewsletterArea = () => {
    return (
        <>
            {/* -- newsletter area start -- */}
            <section className="bd-cta-wrapper section-space-bottom">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="bd-newsletter-wrapper style-two theme-bg bd-noise-bg radius-16 fix">
                            <div className="container">
                                <div className="bd-newsletter-item">
                                    <div className="row gy-30 align-items-center justify-content-between">
                                        <div className="col-xl-5 col-lg-12">
                                            <div className="bd-section-title-wrapper">
                                                <h2 className="bd-section-title white-text mb-0">Stay in the Book Loop!</h2>
                                            </div>
                                        </div>
                                        <div className="col-xl-6 col-lg-12">
                                            <form action="#">
                                                <div className="bd-newsletter-input">
                                                    <input type="text" placeholder="Your email" />
                                                    <button type="submit" className="bd-btn btn-primary radius-6">
                                                        Subscribe Now
                                                    </button>
                                                </div>
                                            </form>
                                            <p className="description">*Get <span>15%</span> off on subscribe</p>
                                        </div>
                                    </div>
                                    <div className="shape-1"><Image src={CircleShape} alt="Circle Shape" /></div>
                                    <div className="shape-2"><Image src={Square1} alt="Square Shape 1" /></div>
                                    <div className="shape-3"><Image src={Ellipse1} alt="Ellipse Shape 1" /></div>
                                    <div className="shape-4"><Image src={CircleWhite} alt="White Circle Shape" /></div>
                                    <div className="shape-5"><Image src={Ellipse2} alt="Ellipse Shape 2" /></div>
                                    <div className="shape-6"><Image src={Square2} alt="Square Shape 2" /></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- newsletter area end -- */}
        </>
    );
};

export default NewsletterArea;
