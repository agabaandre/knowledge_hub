"use client"
import { schoolingCategoriesData } from '@/data/categories';
import Link from 'next/link';
import React from 'react';
//swiper
import { Navigation, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

const ModernSchoolingCategory = () => {
    return (
        <>
            {/* -- category area start -- */}
            <section className="bd-category-area section-space fix">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="bd-section-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Explore Categories</span>
                                <h2 className="bd-section-title mb-20"><span>Top Categories</span></h2>
                            </div>
                        </div>
                    </div>
                    <div className="p-relative">
                        <div className="row justify-content-center">
                            <div className="col-lg-10 col-md-12">
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
                                            schoolingCategoriesData.map((item) => (
                                                <SwiperSlide key={item.id}>
                                                    <>
                                                        {
                                                            item.categories.map((category, index) => (
                                                                <div className="bd-category-grid-item" key={index}>
                                                                    <Link href="/courses-grid-right" className="bd-category-wrapper style-seven">
                                                                        <div className="bd-category-item">
                                                                            <div className="bd-category-icon">
                                                                                <category.icon />
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
                            </div>
                        </div>
                        <div className="bd-category-navigation">
                            <button className="category-nav-prev"><i className="fa-regular fa-angle-left"></i></button>
                            <button className="category-nav-next"><i className="fa-regular fa-angle-right"></i></button>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- category area end -- */}
        </>
    );
};

export default ModernSchoolingCategory;