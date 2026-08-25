"use client"
import Image from 'next/image';
import React from 'react';
import testimonialThumb from '../../../../public/assets/images/testimonial/testimonial-thumb-01.webp';
import trophyIcon from '../../../../public/assets/images/icon/trophy-icon.webp';
import testimonialData from '@/data/testimonial-data';
//swiper functionality
import { Autoplay, Navigation, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import CountUpContent from '@/components/common/counter/CountUpContent';


const LanguageAcademyTestimonial = () => {
    return (
        <>
            {/* -- testimonial area start -- */}
            <section className="bd-testimonial-area section-space p-relative theme-bg-05">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-7">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Hear from Our Students</span>
                                <h2 className="bd-section-title">Real Experiences, Real Success</h2>
                            </div>
                        </div>
                    </div>

                    <div className="row gy-30 align-items-center">
                        <div className="col-lg-6">
                            <div className="bd-testimonial-wrapper style-eight">
                                <div className="bd-testimonial-thumb-wrapper">
                                    <div className="bd-testimonial-thumb">
                                        <Image src={testimonialThumb} alt="image" />
                                    </div>
                                    <div className="bd-testimonial-counter bounce-slide">
                                        <div className="bd-testimonial-counter-thumb">
                                            <Image src={trophyIcon} style={{ width: "100%", height: "auto" }} alt="image" />
                                        </div>
                                        <div className="bd-testimonial-counter-content">
                                            <h3 className="bd-testimonial-counter-title">
                                                <CountUpContent number={30} text='K+' />
                                            </h3>
                                            <p>Positive Reviews</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="testimonialActiveFour">
                                <Swiper
                                    modules={[Autoplay, Pagination, Navigation]}
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
                                        testimonialData.slice(12, 15).map((item) => (
                                            <SwiperSlide key={item.id}>
                                                <div className="bd-testimonial-wrapper style-eight">
                                                    <div className="bd-testimonial-item">
                                                        <div className="bd-testimonial-quote">
                                                            {item.quoteImage && <Image src={item.quoteImage} style={{ width: "100%", height: "auto" }} alt="image" />}
                                                        </div>
                                                        <div className="bd-testimonial-description">
                                                            <p>{item.content}</p>
                                                        </div>
                                                        <div className="bd-testimonial-avater">
                                                            <div className="bd-testimonial-avater-thumb">
                                                                <Image src={item.avatar} alt="image" />
                                                            </div>
                                                            <div className="bd-testimonial-avater-info">
                                                                <h5 className="bd-testimonial-avater-title">{item.name}</h5>
                                                                <span className="bd-testimonial-avater-designation">{item.designation}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </SwiperSlide>
                                        ))
                                    }
                                </Swiper>
                            </div>
                            {/* -- slider dots pagination -- */}
                            <div className="bd-testimonial-pagination has-primary bd-dots-pagination justify-content-start">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- testimonial area end -- */}
        </>
    );
};

export default LanguageAcademyTestimonial;