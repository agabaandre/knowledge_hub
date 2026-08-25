"use client"
import productsData from '@/data/products-data';
import React from 'react';
import ShopSingleCard from '../../common/ShopSingleCard';

//swiper
import { Navigation } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

const TradingAreaStart = () => {
    return (
        <>
            {/* -- trading area start -- */}
            <section className="bd-trading-area section-space-bottom">
                <div className="container">
                    <div className="bd-section-bg section-title-space">
                        <div className="row gy-30 justify-content-between align-items-center">
                            <div className="col-xl-6 col-lg-7 col-md-7">
                                <div className="bd-section-wrapper">
                                    <h2 className="bd-section-title x-small">Upcoming Books – Pre-Order Now!</h2>
                                </div>
                            </div>
                            <div className="col-xl-2 col-lg-3 col-md-5">
                                <div className="bd-book-navigation d-flex-items justify-content-md-end gap-15">
                                    <button className="book-trading-prev"><i className="fa-regular fa-angle-left"></i></button>
                                    <button className="book-trading-next"><i className="fa-regular fa-angle-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="swiper bookTradingSlider">
                        <Swiper
                            modules={[Navigation]}
                            spaceBetween={30}
                            loop={false}
                            centeredSlides={false}
                            navigation={{
                                nextEl: ".book-trading-next",
                                prevEl: ".book-trading-prev",
                            }}
                            breakpoints={{
                                1400: {
                                    slidesPerView: 5,
                                },
                                1200: {
                                    slidesPerView: 4,
                                },
                                992: {
                                    slidesPerView: 3,
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
                            {
                                productsData.slice(0, 8).map((item) => (
                                    <SwiperSlide key={item.id}>
                                        <ShopSingleCard item={item} />
                                    </SwiperSlide>
                                ))
                            }
                        </Swiper>
                    </div>
                </div>
            </section>
            {/* -- trading area end -- */}
        </>
    );
};

export default TradingAreaStart;