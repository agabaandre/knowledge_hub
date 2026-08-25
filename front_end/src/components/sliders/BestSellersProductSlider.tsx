"use client"
import productsData from '@/data/products-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
//swiper
import { Swiper, SwiperSlide } from 'swiper/react';
import { Navigation } from 'swiper/modules';

const BestSellersProductSlider = () => {
    return (
        <>
            {/* -- similar products area start -- */}
            <section className="bd-similar-products-area section-space-bottom">
                <div className="container">
                    <div className="row gy-30 justify-content-between align-items-center section-title-space">
                        <div className="col-xl-7 col-lg-7 col-md-7">
                            <div className="bd-section-title-wrapper">
                                <h2 className="bd-section-title">Best Sellers of the Week</h2>
                            </div>
                        </div>
                        <div className="col-xl-2 col-lg-5 col-md-5">
                            <div className="bd-blog-slider-navigation d-flex justify-content-md-end gap-15">
                                <button className="blog-navigation-prev"><i className="fa-regular fa-angle-left"></i></button>
                                <button className="blog-navigation-next"><i className="fa-regular fa-angle-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="swiper similarProductActivation">
                        <Swiper
                            modules={[Navigation]}
                            spaceBetween={30}
                            centeredSlides={false}
                            loop={true}
                            allowTouchMove={true}
                            observer={true}
                            navigation={{
                                nextEl: ".blog-navigation-next",
                                prevEl: ".blog-navigation-prev",
                            }}
                            breakpoints={{
                                1400: {
                                    slidesPerView: 4,
                                },
                                1200: {
                                    slidesPerView: 4,
                                },
                                992: {
                                    slidesPerView: 3,
                                },
                                768: {
                                    slidesPerView: 2,
                                },
                                576: {
                                    slidesPerView: 2,
                                },
                                450: {
                                    slidesPerView: 2,
                                },
                                0: {
                                    slidesPerView: 1,
                                },
                            }}
                        >
                            {productsData.slice(13, 18).map((item) => (
                                <SwiperSlide key={item.id}>
                                    <div className="bd-product-card-wrap">
                                        <div className="bd-product-card-wrap">
                                            <Link href={`/shop/shop-details/${item.id}`} className="bd-product-card">
                                                <div className="bd-product-thumb mb-5">
                                                    {item.image && <Image src={item.image} style={{width:"100%", height:"auto"}} alt="image not found" />}
                                                </div>
                                                <div className="bd-product-content">
                                                    <h6 className="bd-product-title underline mb-10">{item.title}</h6>
                                                    <span
                                                        className="bd-product-rating fs-14 d-flex justify-content-center rating-color mb-10">
                                                        <i className="fa-solid fa-star"></i>
                                                        <i className="fa-solid fa-star"></i>
                                                        <i className="fa-solid fa-star"></i>
                                                        <i className="fa-solid fa-star"></i>
                                                        <i className="fa-solid fa-star"></i>
                                                    </span>
                                                    <div className="bd-product-price">
                                                        <span className="current-price">{`$${item.price}`}</span>
                                                    </div>
                                                </div>
                                                <div className="bd-product-cart-btn">
                                                    <button className="bd-btn btn-primary btn-small" type="button">Add To Cart</button>
                                                </div>
                                            </Link>
                                            <div className="bd-product-details-btn">
                                                <Link href={`/shop/shop-details/${item.id}`} className="bd-btn btn-outline-secondary btn-small">View
                                                    Details</Link>
                                            </div>
                                        </div>
                                    </div>
                                </SwiperSlide>
                            ))}
                        </Swiper>
                    </div>
                </div>
            </section>
            {/* -- similar products area end -- */}
        </>
    );
};

export default BestSellersProductSlider;