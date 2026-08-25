import Image from 'next/image';
import React from 'react';
import halfCircleShape from '../../../../public/assets/images/shape/half-circle-wave-1.webp';
import bookThreeShape from '../../../../public/assets/images/shape/book-3.webp';
import oneToThreeShape from '../../../../public/assets/images/shape/1-3-shape.webp';
import pencleShape from '../../../../public/assets/images/shape/pencle.webp';
import bulbShape from '../../../../public/assets/images/shape/shape-bulb.webp';
import eShape from '../../../../public/assets/images/shape/e-shape.webp';
import labShape from '../../../../public/assets/images/shape/shape-lab.webp';
import halfCircleWaveShapeTwo from '../../../../public/assets/images/shape/half-circle-wave-2.webp';

const AboutCtaArea = ({aboutWrapper}:{aboutWrapper?:boolean}) => {
    return (
        <>
            <section className={`bd-cta-area p-relative ${aboutWrapper ==true ? "section-space-top": ""}`}>
                <div className="bd-cta-wrapper style-two theme-bg p-relative section-space">
                    <div className="container">
                        <div className="bd-cta-content-wrapper p-relative">
                            <div className="row justify-content-center">
                                <div className="col-xxl-6 col-xl-8 col-lg-8 col-md-12">
                                    <div className="bd-cta-content text-center">
                                        <div className="bd-section-title-wrapper section-title-space text-center">
                                            <h2 className="bd-section-title text-white">Enhance Your Skills and Get Certified
                                                with iStudy
                                            </h2>
                                        </div>
                                        <div className="bd-cta-form">
                                            <form action="#">
                                                <div className="bd-newsletter-input">
                                                    <input type="text" placeholder="Your email"/>
                                                        <button type="submit" className="bd-btn btn-primary radius-6">Subscribe Now</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="bd-cta-shape-wrapper">
                            <div className="bd-cta-shape-1">
                                <Image src={halfCircleShape} alt="shape"/>
                            </div>
                            <div className="bd-cta-shape-2 d-none d-xl-block">
                                <Image src={bookThreeShape} alt="shape"/>
                            </div>
                            <div className="bd-cta-shape-3 d-none d-xl-block">
                                <Image src={oneToThreeShape} alt="shape"/>
                            </div>
                            <div className="bd-cta-shape-4 d-none d-xl-block">
                                <Image src={pencleShape} alt="shape"/>
                            </div>
                            <div className="bd-cta-shape-5 d-none d-xl-block">
                                <Image src={bulbShape} alt="shape"/>
                            </div>
                            <div className="bd-cta-shape-6 d-none d-xl-block">
                                <Image src={eShape} alt="shape"/>
                            </div>
                            <div className="bd-cta-shape-7 d-none d-xl-block">
                                <Image src={labShape} alt="shape"/>
                            </div>
                            <div className="bd-cta-shape-8">
                                <Image src={halfCircleWaveShapeTwo} alt="shape"/>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default AboutCtaArea;