"use client"
import GetRating from '@/components/common/GetRating';
import testimonialData from '@/data/testimonial-data';
import Image from 'next/image';
import React from 'react';
//swiper
import { Autoplay, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

interface ModernSchollingTestimonialProps {
    themeBackground: string;
  }

  const ModernSchollingTestimonial: React.FC<ModernSchollingTestimonialProps> = ({ themeBackground }) => {
    return (
        <>
            {/* -- testimonial area start -- */}
            <section className={`bd-testimonial-area section-space ${themeBackground} fix`}>
                <div className="container">
                    <div className="row gy-30 justify-content-between">
                        <div className="col-xl-4 col-lg-4">
                            <div className="bd-section-title-wrapper">
                                <span className="bd-section-subtitle">Testimonials</span>
                                <h2 className="bd-section-title mb-20">Words From Our Community</h2>
                                <p className="bd-section-paragraph">Hear from our community about the positive impact we’ve made
                                    together.</p>
                            </div>
                        </div>
                        <div className="col-xl-8 col-lg-8">
                            <div className="bd-testimonial-slider">
                                <div className="swiper testimonialActiveFive">
                                    <Swiper
                                        modules={[Autoplay, Pagination]}
                                        spaceBetween={30}
                                        loop={true}
                                        roundLengths={true}
                                        autoplay={true}
                                        speed={1200}
                                        pagination={{
                                            el: '.bd-testimonial-pagination',
                                            clickable: true,
                                        }}
                                        breakpoints={{
                                            1400: {
                                                slidesPerView: 2,
                                            },
                                            1200: {
                                                slidesPerView: 2,
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
                                            testimonialData.slice(3, 6).map((item) => (
                                                <SwiperSlide key={item.id}>
                                                    <div className="bd-testimonial-wrapper style-six has-bg">
                                                        <div className="bd-testimonial-item">
                                                            <div className="bd-testimonial-rating mb-30 rating-spacing-2">
                                                                <GetRating averageRating={item.rating} />
                                                            </div>
                                                            <div className="bd-testimonial-content mb-55">
                                                                <p>{`"${item.content}"`}</p>
                                                            </div>
                                                            <div className="bd-testimonial-meta d-flex-between">
                                                                <div className="author">
                                                                    <div className="thumb">
                                                                        <Image src={item.avatar} style={{width:"100%", height:"auto"}} priority alt="images" />
                                                                    </div>
                                                                    <div className="details">
                                                                        <h6 className="name">{item.name}</h6>
                                                                        <span className="designation">{item.designation}</span>
                                                                    </div>
                                                                </div>
                                                                <div className="bd-testimonial-quote">
                                                                    <i className="fa-regular fa-quote-right"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </SwiperSlide>
                                            ))
                                        }
                                    </Swiper>
                                    {/* -- slider dots pagination -- */}
                                    <div className="bd-testimonial-pagination has-primary bd-dots-pagination justify-content-start">
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

export default ModernSchollingTestimonial;