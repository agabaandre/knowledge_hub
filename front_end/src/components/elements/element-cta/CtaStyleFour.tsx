import Link from 'next/link';
import React from 'react';
import ctaShape from '../../../../public/assets/images/shape/cta-shape-1.webp';
import ctaShapeTwo from '../../../../public/assets/images/shape/cta-shape-2.webp';
import ctaShapeThree from '../../../../public/assets/images/shape/cta-shape-3.webp';
import ctaShapeFour from '../../../../public/assets/images/shape/cta-shape-4.webp';
import ctaBg from '../../../../public/assets/images/cta/cta-bg-3.webp';
import Image from 'next/image';

const CtaStyleFour = () => {
    return (
        <>
            {/* -- cta style 04 start -- */}
            <div className="bd-elements-cta section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">CTA Style 04</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                </div>
                <section className="bd-cta-wrapper style-two  p-relative section-space">
                    <div className="bd-cta-bg" style={{ backgroundImage: `url(${ctaBg.src})` }}></div>
                    <div className="container">
                        <div className="bd-cta-content-wrapper p-relative">
                            <div className="bd-cta-shape-wrapper d-none d-lg-block">
                                <div className="bd-cta-shape-1 p-absolute">
                                    <Image src={ctaShape} alt="shape" />
                                </div>
                                <div className="bd-cta-shape-2 p-absolute">
                                    <Image src={ctaShapeTwo} alt="shape" />
                                </div>
                                <div className="bd-cta-shape-3 p-absolute">
                                    <Image src={ctaShapeThree} alt="shape" />
                                </div>
                                <div className="bd-cta-shape-4 p-absolute">
                                    <Image src={ctaShapeFour} alt="shape" />
                                </div>
                            </div>
                            <div className="row justify-content-center">
                                <div className="col-xl-6 col-lg-8 col-md-12">
                                    <div className="bd-cta-content text-center">
                                        <div className="bd-section-title-wrapper section-title-space text-center">
                                            <h2 className="bd-section-title mb-20">Enhance Your Skills and Get Certified with iStudy
                                            </h2>
                                        </div>
                                        <div className="bd-cta-btn">
                                            <Link href="#" className="bd-btn btn-secondary">Apply Now</Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            {/* -- cta style 04 end -- */}
        </>
    );
};

export default CtaStyleFour;