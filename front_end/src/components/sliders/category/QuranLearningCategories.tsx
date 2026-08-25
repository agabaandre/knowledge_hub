
"use client"
import { quranLearningCategories } from '@/data/categories';
import Link from 'next/link';
import React from 'react';
//swiper functionality
import { Navigation, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

interface ICategoriesProps {
    title: string;
    icon: string;
    color: string
}

const CategoryItem = ({ title, icon, color }: ICategoriesProps) => (
    <div className="swiper-slide">
        <Link href="/courses-grid-right" className="bd-category-wrapper style-three">
            <div className="bd-category-item">
                <div className={`bd-category-icon-wrapper ${color}`}>
                    <span className="bd-category-icon"><i className={icon}></i></span>
                </div>
                <h6 className="bd-category-title">{title}</h6>
            </div>
        </Link>
    </div>
);

const QuranLearningCategoriesSlider = () => {
    return (
        <section className="bd-categories-area section-space">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-xl-6">
                        <div className="bd-section-wrapper section-title-space text-center">
                            <h2 className="bd-section-title mb-20">Top Categories</h2>
                        </div>
                    </div>
                </div>
                <div className="p-relative">
                    <div className="swiper categorySliderActiveTwo">
                        <Swiper
                            modules={[Navigation, Pagination]}
                            spaceBetween={30}
                            loop={false}
                            roundLengths={true}
                            speed={1000}
                            autoplay={false}
                            navigation={{
                                nextEl: ".category-nav-next",
                                prevEl: ".category-nav-prev",
                            }}
                            pagination={{
                                el: ".bd-dots-pagination",
                                clickable: true,
                            }}
                            breakpoints={{
                                1400: {
                                    slidesPerView: 6,
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
                                    slidesPerView: 3,
                                },
                                0: {
                                    slidesPerView: 2,
                                },
                            }}
                        >
                            {quranLearningCategories.map((category, index) => (
                                <SwiperSlide key={index}>
                                    <CategoryItem {...category} />
                                </SwiperSlide>
                            ))}
                        </Swiper>
                    </div>
                    <div className="bd-category-navigation">
                        <button className="category-nav-prev"><i className="fa-regular fa-angle-left"></i></button>
                        <button className="category-nav-next"><i className="fa-regular fa-angle-right"></i></button>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default QuranLearningCategoriesSlider;
