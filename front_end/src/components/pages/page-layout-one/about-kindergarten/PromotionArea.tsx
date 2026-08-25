"use client"
import React from 'react';
import PromotionThumb from '../../../../../public/assets/images/about/promotion-thumb-1.webp';
import Image from 'next/image';
import Link from 'next/link';
import { useVideoModal } from '@/contextApi/VideoProvider';

const PromotionArea = () => {
    const { playVideo } = useVideoModal();

    return (
        <>
        {/* -- promotion area start -- */}
        <section className="bd-promotion-area section-space">
            <div className="container">
                <div className="row gy-30 align-items-center justify-content-between">
                    <div className="col-xl-6 col-lg-6 col-md-12">
                        <div className="bd-promotion-wrapper">
                            <div className="bd-promotion-thumb-mask p-relative">
                                <Image src={PromotionThumb} style={{width:"100%", height:"auto"}} alt="image"/>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-6 col-lg-6 col-md-12">
                        <div className="bd-about-wrapper">
                            <div className="bd-about-content-wrapper">
                                <div className="bd-section-title-wrapper section-title-space">
                                    <h2 className="bd-section-title mb-20">A Bright Start for Little Minds</h2>
                                    <p className="bd-section-paragraph">Our kindergarten program is designed to nurture
                                        curiosity,
                                        creativity, and confidence in every child, laying the foundation for a lifetime
                                        of learning and growth.</p>
                                    <div className="bd-about-feature-list">
                                        <ul>
                                            <li><i className="fa-regular fa-check"></i>Fostering a love for learning through
                                                play and exploration.</li>
                                            <li><i className="fa-regular fa-check"></i>Experienced educators dedicated to
                                                your {`child's`} success.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div className="bd-about-btn d-flex-items gap-30">
                                    <Link className="bd-btn btn-primary" href="/contact-us">Learn More</Link>
                                    <button type='button' onClick={() => playVideo("HKk4oLIzhhM", "youtube")} className="bd-video-btn popup-video">
                                        <span className="icon"><i className="fa-solid fa-play"></i></span>
                                        Watch Video
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {/* -- promotion area end -- */}
        </>
    );
};

export default PromotionArea;