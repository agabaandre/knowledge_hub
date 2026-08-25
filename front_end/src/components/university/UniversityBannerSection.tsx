'use client'
import React from 'react';
import bannerImgOne from '../../../public/assets/images/banner/banner-2/banner-2-img-1.webp'
import bannerImgTwo from '../../../public/assets/images/banner/banner-2/banner-2-img-2.webp'
import bannerImgThree from '../../../public/assets/images/banner/banner-2/banner-2-img-3.webp'

//swiper
import { Swiper, SwiperSlide } from 'swiper/react';
import { Navigation, A11y, EffectFade, Autoplay } from 'swiper/modules';


const UniversityBannerSection = () => {
    return (
        <>
            <section className="bd-banner-area">
                <div className="swiper bdSliderActive p-relative">
                    <Swiper
                        modules={[Navigation, A11y, EffectFade, Autoplay]}
                        spaceBetween={50}
                        slidesPerView={1}
                        autoplay={{
                            delay:5000,
                        }}
                        loop={true}
                        navigation={{
                            prevEl: ".banner-navigation-prev",
                            nextEl: ".banner-navigation-next",
                        }}
                        effect="fade"
                    >
                        <SwiperSlide>
                            <div className="bd-banner-slider-wrapper">
                                <div className="bd-banner-bg-image" style={{ backgroundImage: `url(${bannerImgOne.src})` }}>
                                </div>
                                <div className="container">
                                    <div className="row justify-content-center">
                                        <div className="col-xxl-6 col-xl-8">
                                            <div className="bd-banner-content-two text-center">
                                                <div className="bd-banner-subtitle mb-10">find your course here</div>
                                                <h1 className="bd-banner-title white-text mb-25">Empowering Minds, Shaping
                                                    Futures</h1>
                                                <div className="bd-banner-form">
                                                    <div className="bd-banner-form-input">
                                                        <input type="text" placeholder="Search Program" />
                                                        <button className="bd-btn-icon btn-primary btn-large radius-50" type="button"><i
                                                            className="fa-light fa-magnifying-glass"></i></button>
                                                    </div>
                                                    <div className="form-list">
                                                        <div className="form-list-item">
                                                            <span className="icon"><i className="fa-solid fa-check"></i></span>
                                                            <p className="description">10K+ Students</p>
                                                        </div>
                                                        <div className="form-list-item">
                                                            <span className="icon"><i className="fa-solid fa-check"></i></span>
                                                            <p className="description">50+ Programs</p>
                                                        </div>
                                                        <div className="form-list-item">
                                                            <span className="icon"><i className="fa-solid fa-check"></i></span>
                                                            <p className="description">200+ Professor</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </SwiperSlide>
                        <SwiperSlide>
                            <div className="bd-banner-slider-wrapper">
                                <div className="bd-banner-bg-image" style={{ backgroundImage: `url(${bannerImgTwo.src})` }}>
                                </div>
                                <div className="container">
                                    <div className="row justify-content-center">
                                        <div className="col-xxl-6 col-xl-8">
                                            <div className="bd-banner-content-two text-center">
                                                <div className="bd-banner-subtitle mb-10">find your course here</div>
                                                <h1 className="bd-banner-title white-text mb-25">Empowering Minds, Shaping
                                                    Futures</h1>
                                                <div className="bd-banner-form">
                                                    <div className="bd-banner-form-input">
                                                        <input type="text" placeholder="Search Program" />
                                                        <button className="bd-btn-icon btn-primary btn-large radius-50" type="button"><i
                                                            className="fa-light fa-magnifying-glass"></i></button>
                                                    </div>
                                                    <div className="form-list">
                                                        <div className="form-list-item">
                                                            <span className="icon"><i className="fa-solid fa-check"></i></span>
                                                            <p className="description">10K+ Students</p>
                                                        </div>
                                                        <div className="form-list-item">
                                                            <span className="icon"><i className="fa-solid fa-check"></i></span>
                                                            <p className="description">50+ Programs</p>
                                                        </div>
                                                        <div className="form-list-item">
                                                            <span className="icon"><i className="fa-solid fa-check"></i></span>
                                                            <p className="description">200+ Professor</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </SwiperSlide>
                        <SwiperSlide>
                            <div className="bd-banner-slider-wrapper">
                                <div className="bd-banner-bg-image" style={{ backgroundImage: `url(${bannerImgThree.src})` }}>
                                </div>
                                <div className="container">
                                    <div className="row justify-content-center">
                                        <div className="col-xxl-6 col-xl-8">
                                            <div className="bd-banner-content-two text-center">
                                                <div className="bd-banner-subtitle mb-10">find your course here</div>
                                                <h1 className="bd-banner-title white-text mb-25">Empowering Minds, Shaping
                                                    Futures</h1>
                                                <div className="bd-banner-form">
                                                    <div className="bd-banner-form-input">
                                                        <input type="text" placeholder="Search Program" />
                                                        <button className="bd-btn-icon btn-primary btn-large radius-50" type="button"><i
                                                            className="fa-light fa-magnifying-glass"></i></button>
                                                    </div>
                                                    <div className="form-list">
                                                        <div className="form-list-item">
                                                            <span className="icon"><i className="fa-solid fa-check"></i></span>
                                                            <p className="description">10K+ Students</p>
                                                        </div>
                                                        <div className="form-list-item">
                                                            <span className="icon"><i className="fa-solid fa-check"></i></span>
                                                            <p className="description">50+ Programs</p>
                                                        </div>
                                                        <div className="form-list-item">
                                                            <span className="icon"><i className="fa-solid fa-check"></i></span>
                                                            <p className="description">200+ Professor</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </SwiperSlide>
                    </Swiper>
                    {/* <!-- navigation button start --> */}
                    <div className="bd-banner-navigation d-none d-xl-block">
                        <div className="bd-banner-navigation-btn-two">
                            <button className="banner-navigation-prev"><i className="fa-regular fa-angle-left"></i></button>
                            <button className="banner-navigation-next"><i className="fa-regular fa-angle-right"></i></button>
                        </div>
                    </div>
                    {/* <!-- navigation button end --> */}
                </div>
            </section>
        </>
    );
};

export default UniversityBannerSection;