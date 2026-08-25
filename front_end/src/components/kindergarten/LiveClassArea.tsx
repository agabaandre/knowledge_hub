import React from 'react';
import LiveClassThumb from '../../../public/assets/images/live-class/live-class-thumb-01.webp';
import artShape from '../../../public/assets/images/shape/art-shape.webp';
import bulbShape from '../../../public/assets/images/shape/bulb-shape.webp';
import bookShape from '../../../public/assets/images/shape/book-shape.webp';
import Image from 'next/image';
import Link from 'next/link';

const LiveClassArea = () => {
    return (
        <>
            {/* -- live class area start -- */}
            <section className="bd-live-class-area theme-bg-05 section-space position-relative">
                <div className="bd-live-class-shape-wrapper d-none d-lg-block">
                    <div className="bd-live-class-shape-01 d-none d-xxl-block">
                        <Image style={{width:"100%", height:"auto"}} src={artShape} alt="image" />
                    </div>
                    <div className="bd-live-class-shape-02">
                        <Image style={{width:"100%", height:"auto"}} src={bulbShape} alt="image" />
                    </div>
                    <div className="bd-live-class-shape-03">
                        <Image style={{width:"100%", height:"auto"}} src={bookShape} alt="image" />
                    </div>
                </div>
                <div className="container">
                    <div className="row g-30 align-items-center">
                        <div className="col-xl-6 col-lg-6">
                            <div className="bd-live-class-wrapper style-one">
                                <div className="bd-live-class-section-wrapper">
                                    <div className="bd-section-title-wrapper">
                                        <span className="bd-section-subtitle">Live & Learn</span>
                                        <h2 className="bd-section-title mb-20">Join Our <span
                                            className="has-live-text">live</span><br />Learning Live Class</h2>
                                        <p className="bd-section-paragraph">A dynamic live session where kindergarten students
                                            can participate in a structured learning environment. The class covers essential
                                            topics such as language, math, and creative arts, encouraging active
                                            participation and hands-on learning</p>
                                    </div>
                                    <div className="bd-live-class-btn">
                                        <Link className="bd-btn btn-primary" href="/webinar-details">Join Live Class</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-6">
                            <div className="bd-live-class-thumb-wrapper style-one">
                                <div className="bd-live-class-thumb">
                                    <Image style={{width:"100%", height:"auto"}} src={LiveClassThumb} alt="image" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- live class area start -- */}
        </>
    );
};

export default LiveClassArea;