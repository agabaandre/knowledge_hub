"use client"
import React from 'react';
//swiper functionality
import {Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import programData from '@/data/programe-data';
import Link from 'next/link';
import Image from 'next/image';

const ProgramsSliderArea = () => {
    return (
        <>
            {/* -- programs area start -- */}
            <section className="bd-category-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="bd-section-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Popular Courses</span>
                                <h2 className="bd-section-title mb-20">Discover Our Programs</h2>
                            </div>
                        </div>
                    </div>
                    <div className="kindergartenProgramActive">
                        <Swiper
                            modules={[Pagination]}
                            spaceBetween={30}
                            centeredSlides={false}
                            loop={true}
                            allowTouchMove={true}
                            observer={true}
                            className='swiper swiper-shadow-add'
                            pagination={{
                                el: '.bd-testimonial-pagination',
                                clickable: true,
                            }}
                            breakpoints={{
                                1400: {
                                    slidesPerView: 3,
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
                        >
                            {
                                programData.slice(6, 11).map((item) => (
                                    <SwiperSlide key={item.id}>
                                        <div className="bd-course-wrapper style-four">
                                            <div className="bd-course-thumb-wrapper p-relative mb-20">
                                                <Link href={`/kindergarten-program-details/${item.id}`}>
                                                    <div className="bd-course-thumb">
                                                        <Image src={item.image} alt="image" />
                                                    </div>
                                                    <div className="shape"><Image src={item.shapeImage} alt="shape" />
                                                    </div>
                                                </Link>
                                            </div>
                                            <div className="bd-course-content mb-20">
                                                <h4 className="bd-course-title underline mb--5"><Link href={`/kindergarten-program-details/${item.id}`}>{item.title}</Link>
                                                </h4>
                                                <p>{item.description}</p>
                                            </div>
                                            <div className={`bd-course-info-item-wrapper ${item.BgClass}`}>
                                                <div className="bd-course-info-item">
                                                    <h5 className="bd-course-info-item-title">{item.age} <br /><span>Age</span></h5>
                                                </div>
                                                <div className="bd-course-info-item">
                                                    <h5 className="bd-course-info-item-title">{item.duration} <br /><span>weekly</span></h5>
                                                </div>
                                                <div className="bd-course-info-item">
                                                    <h5 className="bd-course-info-item-title">{item.hoursTime} <br /><span>Hours</span></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </SwiperSlide>
                                ))
                            }
                        </Swiper>
                        {/* -- slider dots pagination -- */}
                        <div className="bd-testimonial-pagination bd-dots-pagination has-primary">
                        </div>
                    </div>
                </div>
            </section>
            {/* -- programs area start -- */}
        </>
    );
};

export default ProgramsSliderArea;