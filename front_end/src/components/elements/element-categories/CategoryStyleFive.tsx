"use client"
import Image, { StaticImageData } from 'next/image';
import Link from 'next/link';
import React from 'react';
import categoryThumb from '../../../../public/assets/images/category/category-thumb-01.webp';
//swiper functionality
import { Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

interface Category {
    title: string;
    coursesCount: number;
    imgSrc: StaticImageData;
}
const categories: Category[] = [
    { title: 'Web Development', coursesCount: 45, imgSrc: categoryThumb },
    { title: 'Digital Marketing', coursesCount: 32, imgSrc: categoryThumb },
    { title: 'Photography', coursesCount: 18, imgSrc: categoryThumb },
    { title: 'Health & Fitness', coursesCount: 22, imgSrc: categoryThumb },
    { title: 'Data Science', coursesCount: 20, imgSrc: categoryThumb },
    { title: 'Language Learning', coursesCount: 27, imgSrc: categoryThumb },
];

const CategoryStyleFive: React.FC = () => {
    return (
        <>
            {/* -- category style 05 start -- */}
            <section className="bd-elements-category section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Categories Style 05</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        <div className="categorySliderActiveThree">
                            <Swiper
                                modules={[Pagination]}
                                spaceBetween={30}
                                loop={true}
                                roundLengths={true}
                                pagination={{
                                    el: ".bd-dots-pagination",
                                    clickable: true
                                }}
                                className='swiper swiper-shadow-add'
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
                                        slidesPerView: 1,
                                    },
                                }}
                            >
                                {categories.map((category, index) => (
                                    <SwiperSlide key={index}>
                                        <div className="bd-category-wrapper style-five">
                                            <div className="bd-category-item">
                                                <div className="bd-category-thumb">
                                                    <Link href="#">
                                                        <Image src={category.imgSrc} alt={category.title} />
                                                    </Link>
                                                </div>
                                                <div className="bd-category-content">
                                                    <h6 className="bd-category-title underline">
                                                        <Link href="#">{category.title}</Link>
                                                    </h6>
                                                    <span className="bd-category-total">{category.coursesCount} courses</span>
                                                </div>
                                            </div>
                                        </div>
                                    </SwiperSlide>
                                ))}
                            </Swiper>
                        </div>
                    </div>
                </div>
            </section >
            {/* -- category style 05 end -- */}
        </>
    );
};

export default CategoryStyleFive;
