"use client"
import Image from 'next/image';
import React from 'react';
import homeProgram2 from "../../../../../public/assets/images/program/home-program-2.webp";
import homeProgram3 from "../../../../../public/assets/images/program/home-program-3.webp";
import avaterImg from "../../../../../public/assets/images/avatar/avatar.webp";
//swiper functionality
import { Swiper, SwiperSlide } from 'swiper/react';
import { Navigation } from 'swiper/modules';
import { ProgramDataType } from '@/interFace/interFace';

interface IPropsType{
    program: ProgramDataType | undefined;
}

const ProgramDetailsSliderArea = ({program}:IPropsType) => {
    const images = [
        { id: 1, image: program?.image },
        { id: 2, image: homeProgram2 },
        { id: 3, image: homeProgram3 },
    ]

    return (
        <>
            {/* -- program details slider area start here -- */}
            <section className="bd-program-details-widget section-space-top section-space-medium-bottom">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-xl-6 col-lg-12">
                            <div className="bd-program-details-slider p-relative">
                                <div className="swiper bd-program-details-active">
                                    <Swiper
                                        modules={[Navigation]}
                                        slidesPerView={1}
                                        spaceBetween={24}
                                        observeParents={true}
                                        observer={true}
                                        loop={true}
                                        navigation={{
                                            nextEl: ".bd-program-details-next",
                                            prevEl: ".bd-program-details-prev",
                                        }}
                                    >
                                        {
                                            images.map((img) => (
                                                <SwiperSlide key={img.id}>
                                                    <div className="bd-program-details-slider-thumb">
                                                        {img.image && <Image src={img.image} alt="images" />}
                                                    </div>
                                                </SwiperSlide>
                                            ))
                                        }
                                    </Swiper>
                                </div>
                                {/* -- program details slider navigation  -- */}
                                <div className="bd-program-details-navigation d-sm-flex">
                                    <button className="bd-program-details-next">
                                        <i className="fa-regular fa-angle-right"></i>
                                    </button>
                                    <button className="bd-program-details-prev">
                                        <i className="fa-regular fa-angle-left"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-12">
                            <div className="bd-program-details-widget-content theme-bg-05">
                                <h3 className="bd-program-details-widget-title">{program?.title}</h3>
                                <p className="mb-25">
                                    The foundation of the Montessori philosophy is based upon the idea that children should work at
                                    their own pace, according to their own strengths in surroundings that help to develop their
                                    intelligence, as well as social and physical abilities. </p>
                                <p className="mb-25">Observers of Montessori children note that they are confident, caring, independent
                                    as well as enthusiastic and motivated learners what they learn years comes
                                    from perceptive. </p>
                                <div className="bd-program-details-author-wrapper mt-35">
                                    <div className="bd-program-details-author">
                                        <div className="bd-program-details-author-thumb"><Image src={avaterImg} alt="images" /></div>
                                        <div className="bd-program-details-author-name">
                                            <span>Settling Teacher</span>
                                            <h5>Alexia Honix</h5>
                                        </div>
                                    </div>
                                    <div className="bd-program-details-cat">
                                        <span>Categories</span>
                                        <h5>Kindergarten</h5>
                                    </div>
                                    <div className="bd-program-details-cat">
                                        <span>Per/Month</span>
                                        <h5>$160.00</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- program details slider area end here -- */}
        </>
    );
};

export default ProgramDetailsSliderArea;