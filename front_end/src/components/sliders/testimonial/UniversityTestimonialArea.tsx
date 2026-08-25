"use client"
import testimonialData from '@/data/testimonial-data';
import Image from 'next/image';
import React from 'react';
import bookShape from '../../../../public/assets/images/shape/book.webp';
import triAngleShape from '../../../../public/assets/images/shape/tri-angle.webp';
import leftMap from '../../../../public/assets/images/testimonial/testimonial-left-map.webp';
import rightMap from '../../../../public/assets/images/testimonial/testimonial-right-map.webp';
import shapeEx1 from '../../../../public/assets/images/testimonial/testi-shape-ex-1.webp';
import shapeEx2 from '../../../../public/assets/images/testimonial/testi-shape-ex-2.webp';
import quotesShape from '../../../../public/assets/images/shape/testimonial-quotes-2.webp';
import thumb1 from '../../../../public/assets/images/testimonial/testimonial-sm-thumb-1.webp';
import thumb2 from '../../../../public/assets/images/testimonial/testimonial-sm-thumb-2.webp';
import thumb3 from '../../../../public/assets/images/testimonial/testimonial-sm-thumb-3.webp';
//swiper
import { Navigation, Autoplay, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import GetRating from '@/components/common/GetRating';


const UniversityTestimonialArea = () => {

    return (
        <>
            {/* -- testimonial area start -- */}
            <section className="bd-testimonial-area section-space bd-testimonial-sec-bg p-relative">
                <div className="container">
                    <div className="bd-testimonial-shape-wrap">
                        <div className="shape-1 d-none d-xxl-block"><Image src={bookShape} alt="shape" priority /></div>
                        <div className="shape-2 d-none d-xxl-block"><Image src={triAngleShape} alt="shape" priority />
                        </div>
                        <div className="shape-3"><Image src={leftMap} alt="shape" />
                        </div>
                        <div className="shape-4"><Image src={rightMap} alt="shape" />
                        </div>
                        <div className="shape-5"><Image src={shapeEx1} alt="shape" /></div>
                        <div className="shape-6"><Image src={shapeEx2} alt="shape" /></div>
                    </div>
                    <div className="row gy-30 justify-content-between align-items-end section-title-space">
                        <div className="col-xl-5 col-lg-6">
                            <div className="bd-section-wrapper">
                                <span className="bd-section-subtitle text-secondary">TESTIMONIALS</span>
                                <h2 className="bd-section-title text-white">What Our <span className="down-mark-line"> Student</span>{" "}
                                    Says</h2>
                            </div>
                        </div>
                        <div className="col-xl-5 col-lg-6">
                            <p className="bd-section-paragraph text-white">Education is one of the most essential
                                and valuable assets that an individual can possess, It plays a pivotal role in
                                shaping.
                            </p>
                        </div>
                    </div>
                    <div className="row g-30">
                        <div className="col-xl-12">
                            <div className="bd-testimonial-wrapper style-nine">
                                <div className="bd-testimonial-item p-relative">
                                    <div className="bd-testimonial-quotes-wrap">
                                        <div className="bd-testimonial-quotes">
                                            <Image src={quotesShape} alt="shape" />
                                        </div>
                                        <div className="bd-testimonial-meta-thumb-1"><Image src={thumb1} alt="image" />
                                        </div>
                                        <div className="bd-testimonial-meta-thumb-2"><Image src={thumb2} alt="image" />
                                        </div>
                                        <div className="bd-testimonial-meta-thumb-3"><Image src={thumb3} alt="image" />
                                        </div>
                                    </div>
                                    <div className="swiper testimonialActiveFour">
                                            <Swiper
                                                modules={[Navigation, Autoplay, Pagination]}
                                                spaceBetween={30}
                                                observeParents={true}
                                                observer={true}
                                                loop={true}
                                                speed={1200}
                                                autoplay={{
                                                    delay: 9500,
                                                }}
                                                pagination={{
                                                    el: '.bd-testimonial-pagination',
                                                    clickable: true,
                                                }}
                                                navigation={{
                                                    nextEl: ".testimonial-navigation-next",
                                                    prevEl: ".testimonial-navigation-prev",
                                                }}
                                                breakpoints={{
                                                    '1200': {
                                                        slidesPerView: 1,
                                                    },
                                                    '992': {
                                                        slidesPerView: 1,
                                                    },
                                                    '768': {
                                                        slidesPerView: 1,
                                                    },
                                                    '576': {
                                                        slidesPerView: 1,
                                                    },
                                                }}
                                            >
                                                {
                                                    testimonialData.slice(6, 9).map((item) => (
                                                        <SwiperSlide key={item.id}>
                                                            <div className="bd-testimonial-content">
                                                                <div className="rating rating-spacing-2">
                                                                    <GetRating averageRating={item.rating} />
                                                                </div>
                                                                <p className="description">“{item.content}”</p>
                                                                <div className="bd-testimonial-meta">
                                                                    <div className="author">
                                                                        <div className="thumb">
                                                                            <Image src={item.avatar} style={{width:"100%", height:"auto"}} alt="student" priority />
                                                                        </div>
                                                                        <div className="details">
                                                                            <h6 className="name">{item.name}</h6>
                                                                            <p className="designation">{item.designation}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </SwiperSlide>
                                                    ))
                                                }
                                            </Swiper>
                                        {/* -- slider dots pagination -- */}
                                        <div className="bd-testimonial-pagination bd-dots-pagination has-primary justify-content-start">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- testimonial area end -- */}
        </>
    );
};

export default UniversityTestimonialArea;