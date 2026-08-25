"use client"
import blogData from '@/data/blog-data';
import React from 'react';
//swiper
import { Swiper, SwiperSlide, } from 'swiper/react';
import BlogSingleCard from '../common/blog/BlogSingleCard';
import { Navigation, Pagination } from 'swiper/modules';

const ModarnSchoolingBlogArea = () => {
    return (
        <>
            {/* -- blog area start -- */}
            <section className="bd-blog-area section-space-bottom fix">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">News & Insights</span>
                                <h2 className="bd-section-title">Latest Updates</h2>
                            </div>
                        </div>
                    </div>
                    <div className="blogSlideActivation">
                        <Swiper
                            modules={[Navigation, Pagination]}
                            spaceBetween={30}
                            centeredSlides={false}
                            loop={false}
                            allowTouchMove={true}
                            observer={true}
                            className='swiper swiper-shadow-add'
                            pagination={{
                                el: ".bd-dots-pagination",
                                clickable: true,
                            }}
                            navigation={{
                                nextEl: ".blog-navigation-next",
                                prevEl: ".blog-navigation-prev",
                            }}
                            breakpoints={{
                                1400: {
                                    slidesPerView: 3,
                                },
                                1200: {
                                    slidesPerView: 3,
                                },
                                992: {
                                    slidesPerView: 3,
                                },
                                768: {
                                    slidesPerView: 2,
                                },
                                576: {
                                    slidesPerView: 1,
                                },
                                0: {
                                    slidesPerView: 1,
                                }
                            }
                            }
                        >
                            {
                                blogData.slice(6, 9).map((item) => (
                                    <SwiperSlide key={item.id}>
                                        <BlogSingleCard item={item} />
                                    </SwiperSlide>
                                ))
                            }
                        </Swiper>
                        <div className="bd-blog-pagination bd-dots-pagination has-primary"></div>
                    </div>
                </div>
            </section>

          
            {/* -- blog area end -- */}
        </>
    );
};

export default ModarnSchoolingBlogArea;