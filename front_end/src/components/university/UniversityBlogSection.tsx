"use client"
import blogData from '@/data/blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
//swiper 
import { Navigation, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

const UniversityBlogSection = () => {
    return (
        <>
            {/* -- blog area start -- */}
            <section className="bd-blog-area section-space">
                <div className="container">
                    <div className="row gy-30 justify-content-between align-items-center section-title-space">
                        <div className="col-xl-6 col-lg-8 col-md-12">
                            <div className="bd-section-title-wrapper">
                                <span className="bd-section-subtitle">Insights & Updates</span>
                                <h2 className="bd-section-title">Our Latest <span className="down-mark-line">News</span></h2>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-4 col-md-12">
                            <div className="bd-instructor-btn text-start text-lg-end">
                                <Link className="bd-btn btn-outline-border-primary" href="/blog-grid">More Articles</Link>
                            </div>
                        </div>
                    </div>
                    <div className="swiper blogSlideActivation">
                        <div className="swiper-wrapper">
                            <Swiper
                                modules={[Navigation, Pagination]}
                                spaceBetween={30}
                                centeredSlides={false}
                                loop={false}
                                allowTouchMove={true}
                                observer={true}
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
                                {blogData.slice(3,6).map((item, index) => (
                                    <SwiperSlide key={index}>
                                        <div className="bd-blog-wrapper style-ten">
                                            <div className="bd-blog-thumb-wrapper">
                                                <div className="bd-blog-thumb">
                                                    <Link href={`/blog/blog-details/${item.id}`}>
                                                        {item.image && <Image src={item.image} alt="image" />}
                                                    </Link>
                                                </div>
                                                <div className="bd-blog-badge-circle">
                                                    <span className="bd-circle-badge primary">
                                                        {item.date}<span className="subtitle">{item.month}</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="bd-blog-content">
                                                <div className="bd-blog-meta-list">
                                                    <div className="bd-blog-meta-item has-separator">
                                                        <span className="meta-icon">
                                                            <i className="fa-solid fa-user"></i>
                                                        </span>
                                                        <span className="meta-text">
                                                            <Link className="meta-author" href={`/blog/blog-details/${item.id}`}>{item.authorName}</Link>
                                                        </span>
                                                    </div>
                                                    {item.comments !== undefined && (
                                                    <div className="bd-blog-meta-item">
                                                        <span className="meta-icon">
                                                            <i className="fa-light fa-comments"></i>
                                                        </span>
                                                        <span className="meta-text">{item.comments} {item?.comments > 1 ? "Comments" : "Comment"}</span>
                                                    </div>
                                                    )}
                                                </div>
                                                <h5 className="title underline">
                                                    <Link href={`/blog/blog-details/${item.id}`}>{item.title}</Link>
                                                </h5>
                                                <Link className="bd-btn btn-outline-primary btn-small" href={`/blog/blog-details/${item.id}`}>
                                                    Read more
                                                </Link>
                                            </div>
                                        </div>
                                    </SwiperSlide>
                                ))}
                            </Swiper>
                        </div>
                        <div className="bd-blog-pagination bd-dots-pagination has-primary"></div>
                    </div>
                </div>
            </section>
            {/* -- blog area end -- */}
        </>
    );
};

export default UniversityBlogSection;