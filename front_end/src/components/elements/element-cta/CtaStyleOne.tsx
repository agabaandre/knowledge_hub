import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import carrerCta from '../../../../public/assets/images/cta/career-cta-bg-01.webp';
import carrerCtaThumb from '../../../../public/assets/images/cta/career-cta-thumb-01.webp';

const CtaStyleOne = () => {
    return (
        <>
            {/* -- cta style 01 start -- */}
            <section className="bd-elements-cta section-space">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">CTA Style 01</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row justify-content-center">
                        <div className="col-xl-6 col-lg-9 col-md-12">
                            <div className="bd-career-wrapper style-one">
                                <div className="bd-career-item">
                                    <div className="bd-career-bg">
                                        <Image src={carrerCta} alt="image" />
                                    </div>
                                    <div className="bd-career-thumb">
                                        <Image src={carrerCtaThumb} alt="thumb" />
                                    </div>
                                    <div className="bd-career-content w-50">
                                        <span className="bd-career-subtitle text-secondary">Start Your Journey Today</span>
                                        <h4 className="bd-career-title underline"><Link href="#">Become an Instructor & Share Your
                                            Expertise</Link></h4>
                                        <div className="bd-career-btn">
                                            <Link className="bd-btn btn-secondary btn-small" href="#">Learn More</Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- cta style 01 end -- */}
        </>
    );
};

export default CtaStyleOne;