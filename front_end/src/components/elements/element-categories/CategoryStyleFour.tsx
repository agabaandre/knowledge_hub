"use client"
import React from 'react';
import Link from 'next/link';
import Image, { StaticImageData } from 'next/image';
import categoriesThumb1 from '../../../../public/assets/images/category/category-thumb-01.webp';
//swiper functionality
import { Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

interface Category {
    title: string;
    totalCourses: number;
    icon: string;
    imgSrc: StaticImageData;
}

const categories: Category[] = [
    {
        title: 'Web Development',
        totalCourses: 25,
        icon: 'fa-laptop-code',
        imgSrc: categoriesThumb1,
    },
    {
        title: 'Data Science',
        totalCourses: 18,
        icon: 'fa-brain',
        imgSrc: categoriesThumb1,
    },
    {
        title: 'Digital Art',
        totalCourses: 22,
        icon: 'fa-palette',
        imgSrc: categoriesThumb1,
    },
    {
        title: 'Business Analytics',
        totalCourses: 15,
        icon: 'fa-chart-line',
        imgSrc: categoriesThumb1,
    },
    {
        title: 'Photography',
        totalCourses: 12,
        icon: 'fa-camera',
        imgSrc: categoriesThumb1,
    },
];

const CategoryStyleFour: React.FC = () => {
    return (
        <>
            {/* -- category style 04 start -- */}
            <section className="bd-elements-category section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Categories Style 04</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        <div className="categorySliderActiveFour">
                            <Swiper
                                modules={[Pagination]}
                                slidesPerView={1}
                                spaceBetween={30}
                                loop={false}
                                roundLengths={true}
                                pagination={{
                                    el: ".bd-dots-pagination",
                                    clickable: true,
                                }}
                                className='swiper swiper-shadow-add'
                                breakpoints={{
                                    1400: {
                                        slidesPerView: 4,
                                    },
                                    1200: {
                                        slidesPerView: 4,
                                    },
                                    992: {
                                        slidesPerView: 3,
                                    },
                                    768: {
                                        slidesPerView: 2,
                                    },
                                    576: {
                                        slidesPerView: 2,
                                    },
                                }}
                            >
                                {categories.map((category, index) => (
                                    <SwiperSlide key={index}>
                                        <div className="bd-category-wrapper style-four">
                                            <div className="bd-category-item">
                                                <div className="bd-category-thumb">
                                                    <Link href="#">
                                                        <Image src={category.imgSrc} alt={category.title} />
                                                    </Link>
                                                </div>
                                                <span className="bd-category-icon">
                                                    <i className={`fa-light ${category.icon}`} />
                                                </span>
                                                <div className="bd-category-content">
                                                    <h6 className="bd-category-title underline">
                                                        <Link href="#">{category.title}</Link>
                                                    </h6>
                                                    <span className="bd-category-total">{category.totalCourses} courses</span>
                                                </div>
                                            </div>
                                        </div>
                                    </SwiperSlide>
                                ))}
                            </Swiper>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- category style 04 end -- */}
        </>
    );
};

export default CategoryStyleFour;
