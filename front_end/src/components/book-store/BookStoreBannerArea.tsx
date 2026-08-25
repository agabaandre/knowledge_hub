"use client"
import productsData from '@/data/products-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
//swiper
import { Autoplay, EffectCoverflow, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

const BookStoreBannerArea = () => {

    return (
        <>
            {/* -- banner area start  -- */}
            <section className="bd-banner-area bd-banner-six section-space theme-bg bd-noise-bg p-relative fix">
                <div className="container">
                    <div className="row gy-30 align-items-center justify-content-between">
                        <div className="col-xl-6">
                            <div className="bd-banner-content-six">
                                <span className="bd-banner-subtitle">Welcome to iStudy Book Shop</span>
                                <h1 className="bd-banner-title mb-20">Discover Your Next Favorite Book</h1>
                                <p className="bd-banner-description">Explore a diverse collection of books across genres,
                                    curated just for you. From timeless classics to the latest releases, {`we've`} got something
                                    for every reader.</p>
                                <div className="bd-banner-btn">
                                    <Link className="bd-btn btn-outline-border-white" href="https://codecanyon.net/user/topylo/portfolio" target="_blank">Download App</Link>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6">
                            <div className="swiper bookSlider">
                                <div className="swiper-wrapper">
                                    <Swiper
                                        modules={[Autoplay, Pagination, EffectCoverflow]}
                                        effect="coverflow"
                                        grabCursor={true}
                                        centeredSlides={true}
                                        coverflowEffect={{
                                            rotate: 0,
                                            stretch: 0,
                                            depth: 100,
                                            modifier: 3,
                                            slideShadows: true,
                                        }}
                                        keyboard={{
                                            enabled: true
                                        }}
                                        mousewheel={{
                                            thresholdDelta: 70
                                        }}
                                        loop={true}
                                        pagination={{
                                            el: ".swiper-pagination",
                                            clickable: true
                                        }}
                                        autoplay={true}
                                        breakpoints={{
                                            1400: {
                                                slidesPerView: 3,
                                            },
                                            1200: {
                                                slidesPerView: 3,
                                            },
                                            992: {
                                                slidesPerView: 4,
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
                                                slidesPerView: 2,
                                            },
                                        }}
                                    >
                                        {
                                            productsData.slice(0, 6).map((item) => (
                                                <SwiperSlide key={item.id}>
                                                    <div className="bd-book-slider-item">
                                                        <div className="bd-book-slider-thumb">
                                                            {item.image && <Image src={item.image} style={{width:"100%", height:"auto"}} alt="book" />}
                                                        </div>
                                                        <div className="bd-book-slider-btn">
                                                            <Link className="bd-btn btn-primary btn-very-small" href={`/shop/shop-details/${item.id}`}>Explore</Link>
                                                        </div>
                                                    </div>
                                                </SwiperSlide>
                                            ))
                                        }
                                    </Swiper>

                                </div>
                                {/* -- Add Pagination -- */}
                                <div className="bd-book-slider-pagination">
                                    <div className="swiper-pagination"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- banner area end  -- */}
        </>
    );
};

export default BookStoreBannerArea;