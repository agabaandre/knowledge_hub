import React from 'react';
import joiningBg from '../../../../public/assets/images/cta/joining-area.webp';
import curvedLine from '../../../../public/assets/images/shape/white-curved-line.webp';
import Image from 'next/image';
import Link from 'next/link';

const CtaStyleTwo = () => {
    return (
        <>
            {/* -- cta style 02 start -- */}
            <section className="bd-cta-wrapper style-two  p-relative section-space">
                <div className="bd-cta-bg" style={{ backgroundImage: `url(${joiningBg.src})` }}></div>
                <div className="bd-cta-bg-overlay"></div>
                <div className="container">
                    <div className="bd-cta-content-wrapper p-relative">
                        <div className="bd-cta-shape-wrapper d-none d-md-block">
                            <div className="bd-cta-shape-1 p-absolute">
                                <Image src={curvedLine} alt="shape" />
                            </div>
                            <div className="bd-cta-shape-2 p-absolute">
                                <Image src={curvedLine} alt="shape" />
                            </div>
                        </div>
                        <div className="row justify-content-center">
                            <div className="col-lg-6 col-md-12">
                                <div className="bd-cta-content text-center">
                                    <div className="bd-section-title-wrapper section-title-space text-center">
                                        <h2 className="bd-section-title white-text mb-20">Call To Action Style 02</h2>
                                        <p className="bd-section-paragraph white-text">iStudy believes that good questions drive good
                                            answers. Whether {`it's`} a query about the world around us or a challenge.</p>
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
            {/* -- cta style 02 end --  */}
        </>
    );
};

export default CtaStyleTwo;