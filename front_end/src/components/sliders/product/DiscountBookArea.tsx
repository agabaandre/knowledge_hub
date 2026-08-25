"use client"
import productsData from '@/data/products-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

//swiper
import { Navigation } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';

const DiscountBookArea = () => {
    return (
        <>
            {/* -- discount book area start -- */}
            <div className="bd-discount-book-area section-space-bottom">
                <div className="container">
                    <div className="bd-section-bg section-title-space">
                        <div className="row gy-30 justify-content-between align-items-center">
                            <div className="col-xl-6 col-lg-7 col-md-7">
                                <div className="bd-section-wrapper">
                                    <h2 className="bd-section-title x-small">All {`Discount's`} Books</h2>
                                </div>
                            </div>
                            <div className="col-xl-2 col-lg-3 col-md-5">
                                <div className="bd-book-navigation d-flex-items justify-content-md-end gap-15">
                                    <button className="book-book-combo-prev"><i className="fa-regular fa-angle-left"></i></button>
                                    <button className="book-book-combo-next"><i className="fa-regular fa-angle-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="swiper offerCardActive">
                        <Swiper
                            modules={[Navigation]}
                            spaceBetween={30}
                            loop={false}
                            centeredSlides={false}
                            navigation={{
                                nextEl: ".book-book-combo-next",
                                prevEl: ".book-book-combo-prev",
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
                                480: {
                                    slidesPerView: 1,
                                },
                                0: {
                                    slidesPerView: 1,
                                },
                            }}
                        >
                            {
                                productsData.slice(8,13).map((item) => (
                                    <SwiperSlide key={item.id}>
                                        <div className="bd-book-combo-box">
                                            <h5 className="bd-book-combo-title mb-30">{item.title}</h5>
                                            <div className="bd-book-combo-content">
                                                {
                                                    item.books &&
                                                    item.books.map((book) => (
                                                        <Link href="/shop/shop-details" className="bd-book-combo-item" key={book.id}>
                                                            <div className="thumb">
                                                                {
                                                                    book.image &&
                                                                    <Image src={book.image} style={{ width: "100%", height: "auto" }} alt="book" />
                                                                }
                                                            </div>
                                                            <h6 className="title">{book.title}</h6>
                                                        </Link>
                                                    ))
                                                }
                                            </div>
                                            <div className="bd-book-combo-btn">
                                                <Link className="bd-btn btn-outline-border-primary btn-small" href="/shop">View
                                                    More</Link>
                                            </div>
                                        </div>
                                    </SwiperSlide>
                                ))
                            }
                        </Swiper>
                    </div>
                </div>
            </div >
            {/* -- discount book area end -- */}
        </>
    );
};

export default DiscountBookArea;