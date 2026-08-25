import Image from 'next/image';
import React from 'react';

import ctaThumb from '../../../../public/assets/images/cta/cta-thumb-01.webp';
import shapeCircleUni from '../../../../public/assets/images/cta/shape_circle_uni.svg';
import shape1 from '../../../../public/assets/images/shape/half-circle-wave-1.webp';
import shape2 from '../../../../public/assets/images/shape/half-circle-wave-2.webp';
import Link from 'next/link';

const CtaStyleSix = () => {
    return (
        <section className="bd-cta-area theme-bg p-relative fix">
            <div className="container">
                <div className="row gy-30 align-items-center justify-content-center">
                    <div className="col-lg-6">
                        <div className="bd-cta-wrapper style-five">
                            <div className="bd-cta-content">
                                <div className="bd-section-title-wrapper section-title-space">
                                    <h2 className="bd-section-title white-text mb-30">
                                        Transform Your Future with Excellence in Education
                                    </h2>
                                    <div className="bd-course-details-list">
                                        <ul>
                                            {[   "Unlock your potential with expert guidance.",
                                                "Achieve your academic and personal goals.",
                                                "Empower yourself with lifelong learning skills."
                                            ].map((text, index) => (
                                                <li key={index}>
                                                    <span className="list-icon white">
                                                        <i className="fa-solid fa-check"></i>
                                                    </span>
                                                    {text}
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                </div>
                                <div className="bd-cta-btn d-flex flex-wrap gap-30 align-items-center">
                                    <Link className="bd-btn btn-outline-border-secondary" href="#">
                                        Start Your Journey
                                    </Link>
                                    <Link className="bd-btn btn-outline-border-white" href="#">
                                        Contact Us
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-lg-6">
                        <div className="bd-cta-wrapper style-five">
                            <div className="bd-cta-thumb-wrapper">
                                <div className="bd-cta-thumb">
                                    <Image style={{width:"100%", height:"auto"}} src={ctaThumb} alt="campus life" priority />
                                </div>
                                <div className="bd-cta-thumb-shape">
                                    <Image style={{width:"100%", height:"auto"}} src={shapeCircleUni} alt="decorative shape" priority />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="bd-cta-five-shape">
                    <div className="shape-1">
                        <Image src={shape1} alt="pattern" priority />
                    </div>
                    <div className="shape-2">
                        <Image src={shape2} alt="pattern" priority />
                    </div>
                </div>
            </div>
        </section>
    );
};

export default CtaStyleSix;
