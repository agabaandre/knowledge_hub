"use client"
import coursesData from '@/data/courses/courses-data';
import { ICourse } from '@/interFace/interFace';
import { wishlist_product } from '@/redux/slices/wishlistSlice';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import { useDispatch } from 'react-redux';
//swiper functionality
import { Navigation } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import GetRating from '../common/GetRating';


const LanguageAcademyCourses = () => {
    const dispatch = useDispatch();
    const handleAddToWishlist = (product: ICourse) => {
        if (product) {
            dispatch(wishlist_product(product))
        }
    }
    return (
        <>
            {/* -- course area start -- */}
            <section className="bd-course-area section-space">
                <div className="container custom-container">
                    <div className="row gy-30 justify-content-between align-items-center section-title-space">
                        <div className="col-xl-7 col-lg-8 col-md-7">
                            <div className="bd-section-title-wrapper">
                                <span className="bd-section-subtitle">Explore Popular Languages</span>
                                <h2 className="bd-section-title">Find Your Language Course</h2>
                            </div>
                        </div>
                        <div className="col-xl-2 col-lg-4 col-md-5">
                            <div className="bd-course-slider-navigation d-flex justify-content-md-end gap-15">
                                <button className="course-navigation-prev"><i className="fa-regular fa-angle-left"></i></button>
                                <button className="course-navigation-next"><i className="fa-regular fa-angle-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="languageCourseActive">
                        <Swiper
                            modules={[Navigation]}
                            spaceBetween={30}
                            loop={true}
                            roundLengths={true}
                            autoplay={false}
                            speed={1200}
                            className='swiper'
                            breakpoints={{
                                1400: {
                                    slidesPerView: 4,
                                },
                                1200: {
                                    slidesPerView: 3,
                                },
                                992: {
                                    slidesPerView: 2,
                                },
                                768: {
                                    slidesPerView: 2,
                                },
                                576: {
                                    slidesPerView: 1,
                                },
                                0: {
                                    slidesPerView: 1,
                                },
                            }}
                            navigation={{
                                nextEl: ".course-navigation-next",
                                prevEl: ".course-navigation-prev",
                            }}
                        >
                            {
                                coursesData.slice(86, 91).map((item) => (
                                    <SwiperSlide key={item.id}>
                                        <div className="bd-course-wrapper style-one">
                                            <div className="p-relative">
                                                <div className="bd-course-thumb">
                                                    <Link href={`/courses/course-details/${item.id}`}>
                                                        <Image src={item.image} alt="English Course" />
                                                    </Link>
                                                </div>
                                                <div className="bd-course-meta d-flex align-items-center justify-content-between">
                                                    <button onClick={() => handleAddToWishlist(item)} className="bd-course-favorite bd-course-like">
                                                        <i className="icon-heart"></i>
                                                    </button>
                                                    <div className="bd-course-tag">
                                                        <span><Link href={`/courses/course-details/${item.id}`}>{item.courseTag}</Link></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="bd-course-content">
                                                <div className="bd-course-rating d-inline-flex align-items-center gap-10 mb-10">
                                                    <div className="bd-course-rating-icon fs-14 d-flex rating-color">
                                                        <GetRating averageRating={item.rating} />
                                                    </div>
                                                    <div className="bd-course-rating-text">
                                                        <span>{item.rating} ({item.ratingNum} Ratings)</span>
                                                    </div>
                                                </div>
                                                <h5 className="bd-course-title underline mb-10">
                                                    <Link href={`/courses/course-details/${item.id}`}>{item.title}</Link>
                                                </h5>
                                                <div className="bd-course-price">
                                                    <span className="current-price">{`$${item.price}.00`}</span>
                                                    {item.discount && <span className="old-price">{`$${item.discount}.00`}</span>}
                                                </div>
                                                <div className="bd-course-divider"></div>
                                                <div className="bd-course-meta d-flex-between">
                                                    <div className="bd-course-author">
                                                        <div className="thumb">
                                                            {item.avatarImg && <Image src={item.avatarImg} alt="English Tutor" />}
                                                        </div>
                                                        <div className="name">
                                                            <Link href="/instructor/instructor-details">{item.instructorName}</Link>
                                                        </div>
                                                    </div>
                                                    <div className="bd-course-lesson">
                                                        <span><i className="fa-light fa-book"></i> {item.lessons} Lessons</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </SwiperSlide>
                                ))
                            }
                        </Swiper>
                    </div>
                </div>
                <div className="bd-course-btn d-flex-center mt-50">
                    <Link className="bd-btn btn-outline-border-primary" href="/courses-grid-three">Explore All Languages</Link>
                </div>
            </section >
            {/* -- course area end -- */}
        </>
    );
};

export default LanguageAcademyCourses;