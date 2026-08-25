import React from 'react';
import favIcon from '../../../../public/assets/images/logo/favicon.svg';
import Image from 'next/image';
import Link from 'next/link';

const CtaStyleFive = () => {

    return (
        <>
            {/* -- cta style 05 start -- */}
            <section className="bd-elements-cta section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">CTA Style 05</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row justify-content-center">
                        <div className="col-xl-8">
                            <div className="bd-cta-wrapper style-four theme-bg">
                                <div className="bd-cta-item">
                                    <div className="bd-cta-content text-md-end">
                                        <span className="subtitle">Get In Touch:</span>
                                        <h4 className="title"><Link href="mailto:istudy@mail.com">istudy@mail.com</Link></h4>
                                    </div>
                                    <div className="bd-cta-separator">
                                        <span><Image src={favIcon} alt="" /></span>
                                    </div>
                                    <div className="bd-cta-content">
                                        <span className="subtitle">Contact Us At:</span>
                                        <h4 className="title"><Link href="tel:+012345678900"> +123-4567-8900</Link></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- cta style 05 end -- */}
        </>
    );
};

export default CtaStyleFive;