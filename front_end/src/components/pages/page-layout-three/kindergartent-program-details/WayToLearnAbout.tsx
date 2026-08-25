import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import detailsImg from '../../../../../public/assets/images/program/details-1.webp';

const WayToLearnAbout = () => {
    return (
        <>

            {/* -- program details area start here -- */}
            <section className="bd-program-details-widget section-space-medium-bottom">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-lg-6">
                            <div className="bd-program-details-widget-content theme-bg-05">
                                <h3 className="bd-program-details-widget-title">Way to Learn</h3>
                                <p className="mb-25">As a result, the child should have a reasonable amount of freedom to do as they
                                    please, while at the same time being wholly immersed in an environment which stimulates their
                                    senses as well as exercising their creativity. Literally their classroom is their world,
                                    providing exposure to materials and experiences that foster greater intellectual growth. You
                                    will love it.</p>
                                <p className="mb-25">
                                    Please take a moment to read this website, and I’m sure that you will come to agree that there
                                    would be better place. </p>
                                <div className="bd-program-details-btn">
                                    <Link href="/about" className="bd-btn btn-primary">Know More</Link>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="bd-program-details-slider p-relative">
                                <div className="bd-program-details-slider-thumb p-relative">
                                    <Image src={detailsImg} alt="images" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- program details area end here -- */}
        </>
    );
};

export default WayToLearnAbout;