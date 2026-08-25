"use client"
import instructorsData from "@/data/instructor-data";
import Image from "next/image";
import Link from "next/link";
import React from "react";
//swiper functionality
import { Navigation } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

const SelerAuthorSlider = () => {
    return (
        <>
            {/* -- best seller author area start -- */}
            <section className="bd-best-seller-author-area section-space-bottom">
                <div className="container">
                    <div className="bd-section-bg section-title-space">
                        <div className="row gy-30 justify-content-between align-items-center">
                            <div className="col-xl-6 col-lg-7 col-md-7">
                                <div className="bd-section-wrapper">
                                    <h2 className="bd-section-title x-small">Best Seller Author</h2>
                                </div>
                            </div>
                            <div className="col-xl-2 col-lg-3 col-md-5">
                                <div className="bd-book-navigation d-flex-items justify-content-md-end gap-15">
                                    <button className="author-prev">
                                        <i className="fa-regular fa-angle-left"></i>
                                    </button>
                                    <button className="author-next">
                                        <i className="fa-regular fa-angle-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="swiper authorActivation">
                            <Swiper
                                modules={[Navigation]}
                                spaceBetween={30}
                                loop={false}
                                centeredSlides={false}
                                navigation={{
                                    nextEl: ".author-next",
                                    prevEl: ".author-prev",
                                }}
                                breakpoints={{
                                    1400: {
                                        slidesPerView: 6,
                                    },
                                    1200: {
                                        slidesPerView: 6,
                                    },
                                    992: {
                                        slidesPerView: 5,
                                    },
                                    768: {
                                        slidesPerView: 3,
                                    },
                                    576: {
                                        slidesPerView: 2,
                                    },
                                    480: {
                                        slidesPerView: 2,
                                    },
                                    0: {
                                        slidesPerView: 1,
                                    },
                                }}
                            >
                                {instructorsData.map((item, index) => (
                                    <SwiperSlide key={index}>
                                        <Link href={`/instructor/instructor-details/${item.id}`} className="bd-author-wrapper style-one">
                                            <div className="bd-author-item">
                                                <div className="bd-author-thumb">
                                                    <Image src={item.image} alt={item.name} priority />
                                                </div>
                                                <h4 className="bd-author-name mt-15">{item.name}</h4>
                                            </div>
                                        </Link>
                                    </SwiperSlide>
                                ))}
                            </Swiper>
                    </div>
                </div>
            </section>
            {/* -- best seller author area end -- */}
        </>
    );
};

export default SelerAuthorSlider;
