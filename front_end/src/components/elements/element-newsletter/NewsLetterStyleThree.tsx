import React from 'react';
import NewsletterBg from '../../../../public/assets/images/newsletter/newsletter-bg.webp';

const NewsLetterStyleThree = () => {
    return (
        <>
            {/* -- newsletter style 03 start -- */}
            <section className="bd-elements-newsletter section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Newsletter Style 03</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30 justify-content-center">
                        <div className="col-xl-10">
                            <div className="bd-newsletter-wrapper style-one section-space theme-bg">
                                <div className="bd-newsletter-item">
                                    <div className="bd-newsletter-bg" style={{ backgroundImage: `url(${NewsletterBg.src})` }}>
                                    </div>
                                    <div className="row justify-content-center">
                                        <div className="col-xl-8 col-lg-10">
                                            <div className="bd-newsletter-content">
                                                <div className="bd-section-title-wrapper section-title-space text-center">
                                                    <h2 className="bd-section-title white-text mb-0">Join Our Newsletter</h2>
                                                    <p className="bd-section-paragraph white-text">Subscribe our newsletter to get our
                                                        latest update & news.</p>
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
                        </div>
                    </div>
                </div>
            </section>
            {/* -- newsletter style 03 end -- */}
        </>
    );
};

export default NewsLetterStyleThree;