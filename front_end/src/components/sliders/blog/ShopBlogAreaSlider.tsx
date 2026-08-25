"use client"
import blogData from '@/data/blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

//swiper
import { Navigation, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

const ShopBlogAreaSlider = () => {
    return (
        <>
            {/* -- blog area start -- */}
            <section className="bd-blog-area section-space-bottom">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-7">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Latest News</span>
                                <h2 className="bd-section-title">Our News & Blog</h2>
                            </div>
                        </div>
                    </div>
                    <div className="blogSlideActivation">
                        <Swiper
                            modules={[Pagination, Navigation]}
                            slidesPerView={3}
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
                                },
                            }}
                        >
                            {
                                blogData.slice(9, 13).map((item) => (
                                    <SwiperSlide key={item.id}>
                                        <article className="bd-blog-wrapper style-five">
                                            <div className="bd-blog-thumb">
                                                <Link href={`/blog/blog-details/${item.id}`}>{item.image && <Image src={item.image} style={{ width: "100%", height: "auto" }} alt="image" />}</Link>
                                            </div>
                                            <div className="bd-blog-badge">
                                                <Link href="/blog" className="bd-badge badge-success">{item.badge}</Link>
                                            </div>
                                            <div className="bd-blog-content">
                                                <div className="bd-blog-meta-list">
                                                    <div className="bd-blog-meta-item">
                                                        <span className="meta-icon">
                                                            <i className="fa-solid fa-user"></i>
                                                        </span>
                                                        <span className="meta-text"><Link className="meta-author" href={`/blog/blog-details/${item.id}`}>{item.authorName}</Link></span>
                                                    </div>
                                                    <div className="bd-blog-meta-item">
                                                        <span className="meta-icon">
                                                            <i className="fa-sharp fa-light fa-calendar-days"></i>
                                                        </span>
                                                        <span className="meta-text"><Link href={`/blog/blog-details/${item.id}`}>{item.date}</Link></span>
                                                    </div>
                                                </div>
                                                <h5 className="title underline"><Link href={`/blog/blog-details/${item.id}`}>{item.title}</Link></h5>
                                                <div className="bd-blog-btn">
                                                    <Link className="bd-text-btn" href={`/blog/blog-details/${item.id}`}>Read More
                                                        <span className="box-icon">
                                                            <i className="fa-regular fa-angle-right first-icon"></i>
                                                            <i className="fa-regular fa-angle-right second-icon"></i>
                                                        </span>
                                                    </Link>
                                                </div>
                                            </div>
                                        </article>
                                    </SwiperSlide>
                                ))
                            }
                        </Swiper>
                        <div className="bd-blog-pagination-2 bd-dots-pagination has-primary mt-50"></div>
                    </div>
                </div>
            </section>
            {/* -- blog area end -- */}
        </>
    );
};

export default ShopBlogAreaSlider;