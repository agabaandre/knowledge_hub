import Image from 'next/image';
import React from 'react';
import testimonialThumb from '../../../public/assets/images/testimonial/testimonial-thumb-02.webp';
import parentsThumb from '../../../public/assets/images/testimonial/parents-thumb-01.webp';
import parentsThumb2 from '../../../public/assets/images/testimonial/parents-thumb-02.webp';
import Link from 'next/link';
import TestimonialQuote from '@/svg/TestimonialQuote';

const TestimonialArea = () => {
    return (
        <>
            {/* -- testimonial area start -- */}
            <section className="bd-testimonial-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6 col-lg-8">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Testimonial</span>
                                <h2 className="bd-section-title mb-20">Parents Says</h2>
                                <p className="bd-section-paragraph">
                                    With the guidance of dedicated teachers and the supportive environment serving as a third teacher,
                                    students are empowered to explore and take risks with confidence.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        <div className="col-xl-6 col-lg-6">
                            <div className="bd-testimonial-wrapper style-ten">
                                <div className="bd-testimonial-thumb">
                                    <Image src={testimonialThumb} alt="testimonial" />
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-6">
                            <div className="bd-testimonial-box">
                                {[{
                                    name: "Emma L. Smith",
                                    avatar: parentsThumb,
                                    review: "The teachers at Little Learners Academy are so caring and attentive. My child has blossomed so much since joining, and every day is filled with fun learning activities!"
                                }, {
                                    name: "James T. Walker",
                                    avatar: parentsThumb2,
                                    review: "Our son absolutely loves his time at Bright Start Kindergarten! The staff is so warm and friendly, and they make learning an enjoyable adventure for all the kids."
                                }].map((testimonial, index) => (
                                    <div key={index} className={`bd-testimonial-wrapper style-ten  mb-25`}>
                                        <div className={`bd-testimonial-item  ${index === 1 ? 'bg-2' : ''}`}>
                                            <div className="bd-testimonial-rating rating-spacing-2 mb-30">
                                                {[...Array(5)].map((_, starIndex) => (
                                                    <Link key={starIndex} href="#"><i className="fa-solid fa-star"></i></Link>
                                                ))}
                                            </div>
                                            <div className="bd-testimonial-content mb-35">
                                                <p>{testimonial.review}</p>
                                            </div>
                                            <div className="bd-testimonial-avatar d-flex-between">
                                                <div className="d-flex align-items-center gap-10">
                                                    <div className="bd-testimonial-avatar-thumb">
                                                        <Image style={{ width: "100%", height: "auto" }} src={testimonial.avatar} alt="avatar" />
                                                    </div>
                                                    <h6 className="name mb-0">{testimonial.name}</h6>
                                                </div>
                                                <div className="bd-testimonial-quote d-none d-sm-block clr-2">
                                                    <TestimonialQuote />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- testimonial area end -- */}
        </>
    );
};

export default TestimonialArea;
