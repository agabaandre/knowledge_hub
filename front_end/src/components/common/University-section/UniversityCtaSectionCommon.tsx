import Link from 'next/link';
import React from 'react';
import ctaThumb from '../../../../public/assets/images/cta/cta-thumb-01.webp';
import shapeCircle from '../../../../public/assets/images/cta/shape_circle_uni.svg';
import halfCircle from '../../../../public/assets/images/shape/half-circle-wave-1.webp';
import halfCircleTwo from '../../../../public/assets/images/shape/half-circle-wave-2.webp';
import Image from 'next/image';

const UniversityCtaSectionCommon = () => {
    return (
        <>
            {/* -- cta area start -- */}
            <section className="bd-cta-area theme-bg p-relative fix">
                <div className="container">
                    <div className="row gy-30 align-items-center justify-content-center">
                        <div className="col-lg-6">
                            <div className="bd-cta-wrapper style-five">
                                <div className="bd-cta-content">
                                    <div className="bd-section-title-wrapper section-title-space">
                                        <h2 className="bd-section-title white-text mb-30">Transform Your Future with Excellence in Education
                                        </h2>
                                        <div className="bd-course-details-list">
                                            <ul>
                                                <li>
                                                    <span className="list-icon white">
                                                        <i className="fa-solid fa-check"></i>
                                                    </span>
                                                    Unlock your potential with expert guidance.
                                                </li>
                                                <li>
                                                    <span className="list-icon white">
                                                        <i className="fa-solid fa-check"></i>
                                                    </span>
                                                    Achieve your academic and personal goals.
                                                </li>
                                                <li>
                                                    <span className="list-icon white">
                                                        <i className="fa-solid fa-check"></i>
                                                    </span>
                                                    Empower yourself with lifelong learning skills.
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div className="bd-cta-btn d-flex flex-wrap gap-30 align-items-center">
                                        <Link className="bd-btn btn-outline-border-secondary" href="/apply-online">Start
                                            Your Journey</Link>
                                        <Link className="bd-btn btn-outline-border-white" href="/contact-us">Contact Us</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="bd-cta-wrapper style-five">
                                <div className="bd-cta-thumb-wrapper">
                                    <div className="bd-cta-thumb">
                                        <Image src={ctaThumb} style={{width:"100%", height:"auto"}} alt="campus life" priority/>
                                    </div>
                                    <div className="bd-cta-thumb-shape">
                                        <Image src={shapeCircle} style={{width:"100%", height:"auto"}} alt="decorative shape" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="bd-cta-five-shape">
                        <div className="shape-1"><Image src={halfCircle} alt="pattern" /></div>
                        <div className="shape-2"><Image src={halfCircleTwo} alt="pattern" /></div>
                    </div>
                </div>
            </section>
            {/* -- cta area end -- */}
        </>
    );
};

export default UniversityCtaSectionCommon;