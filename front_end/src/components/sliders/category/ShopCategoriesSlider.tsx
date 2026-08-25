"use client"
import { shopCategoriesData } from '@/data/categories';
import Link from 'next/link';
import React from 'react';
//swiper
import { Navigation } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

const ShopCategoriesSlider = () => {
    return (
        <>
            {/* -- categories area start -- */}
            <section className="bd-categories-area section-space">
                <div className="container">
                    <div className="bd-section-bg section-title-space">
                        <div className="row gy-30 justify-content-between align-items-center">
                            <div className="col-xl-6 col-lg-7 col-md-7 col-sm-6">
                                <div className="bd-section-wrapper">
                                    <h2 className="bd-section-title x-small">Categories</h2>
                                </div>
                            </div>
                            <div className="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div className="bd-book-navigation d-flex-items justify-content-sm-end gap-10">
                                    <button className="book-category-prev"><i className="fa-regular fa-angle-left"></i></button>
                                    <button className="book-category-next"><i className="fa-regular fa-angle-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="bookCategorySlider bd-book-category">
                        <Swiper
                            modules={[Navigation]}
                            spaceBetween={30}
                            loop={false}
                            centeredSlides={false}
                            navigation={{
                                nextEl: ".book-category-next",
                                prevEl: ".book-category-prev",
                            }}
                            className='swiper swiper-shadow-add'
                            breakpoints={{
                                1400: {
                                    slidesPerView: 5,
                                },
                                1200: {
                                    slidesPerView: 4,
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
                            {shopCategoriesData.map((category, index) => (
                                <SwiperSlide key={index}>
                                    <Link href="/shop" className="bd-category-wrapper style-six">
                                        <div className="bd-category-item">
                                            <span className={`bd-category-icon ${category.color} has-white`}>
                                                <i className={category.icon}></i>
                                            </span>
                                            <div className="bd-category-content">
                                                <h6 className="bd-category-title">{category.title}</h6>
                                                <span className={`bd-category-total ${category.color}`}>{category.totalBooks}</span>
                                            </div>
                                        </div>
                                    </Link>
                                </SwiperSlide>
                            ))}
                        </Swiper>
                    </div>
                </div>
            </section>
            {/* -- categories area end -- */}
        </>
    );
};

export default ShopCategoriesSlider;