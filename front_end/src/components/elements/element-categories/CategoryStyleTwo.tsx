"use client"
import elementCategoriesData from '@/data/elements/element-categories';
import Link from 'next/link';
import React from 'react';
//swiper functionality
import { Navigation, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

const CategoryStyleTwo = () => {
    return (
        <>
            {/* -- category style 02 start -- */}
            <section className="bd-elements-category section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Categories Style 02</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="p-relative">
                        <div className="swiper categorySliderActive">
                            <Swiper
                                modules={[Navigation, Pagination]}
                                spaceBetween={30}
                                loop={false}
                                roundLengths={true}
                                pagination={{
                                    el: ".bd-dots-pagination",
                                    clickable: true,
                                }}
                                navigation={{
                                    nextEl: ".category-nav-next",
                                    prevEl: ".category-nav-prev",
                                }}
                                breakpoints={{
                                    1400: {
                                        slidesPerView: 5,
                                    },
                                    1200: {
                                        slidesPerView: 5,
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
                                    elementCategoriesData.slice(8, 14).map((item) => (
                                        <SwiperSlide key={item.id}>
                                            <>
                                                {
                                                    item?.categories?.map((category, index) => (
                                                        <div className="bd-category-grid-item" key={index}>
                                                            <Link href="/courses-grid-right" className="bd-category-wrapper style-seven">
                                                                <div className="bd-category-item">
                                                                    <div className="bd-category-icon">
                                                                        {/* Render icon using React.createElement */}
                                                                        {category.icon && React.createElement(category.icon)}
                                                                    </div>
                                                                    <div className="bd-category-content">
                                                                        <h6 className="bd-category-title">{category.title}</h6>
                                                                        <span className="bd-category-subtitle">Course: <span
                                                                            className="total-course">{category.totalCourses}</span></span>
                                                                    </div>
                                                                </div>
                                                            </Link>
                                                        </div>
                                                    ))
                                                }
                                            </>
                                        </SwiperSlide>
                                    ))
                                }
                            </Swiper>
                        </div>
                        <div className="bd-category-navigation">
                            <button className="category-nav-prev"><i className="fa-regular fa-angle-left"></i></button>
                            <button className="category-nav-next"><i className="fa-regular fa-angle-right"></i></button>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- category style 02 end -- */}
        </>
    );
};

export default CategoryStyleTwo;
