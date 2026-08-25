import GetRating from '@/components/common/GetRating';
import testimonialData from '@/data/testimonial-data';
import Image from 'next/image';
import React from 'react';

const QuranLearningTestimonial = () => {
    return (
        <>

            {/* -- testimonial area start -- */}
            <section className="bd-testimonial-area section-space-bottom">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Testimonials</span>
                                <h2 className="bd-section-title">What Our Students Say</h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {testimonialData.slice(9, 12).map(item => (
                            <div className="col-xl-4 col-lg-6 col-md-6" key={item.id}>
                                <div className="bd-testimonial-wrapper style-seven">
                                    <div className="bd-testimonial-item p-relative">
                                        <div className="bd-testimonial-meta d-flex-between mb-20">
                                            <div className="author">
                                                <div className="thumb">
                                                    <Image src={item.avatar} alt="testimonial" />
                                                </div>
                                                <div className="details">
                                                    <h6 className="name small">{item.name}</h6>
                                                    <p className="designation">{item.designation}</p>
                                                </div>
                                            </div>
                                            <div className="bd-testimonial-quotes">
                                                {item.quoteImage && <Image src={item.quoteImage} alt="quote" />}
                                            </div>
                                        </div>
                                        <div className="bd-testimonial-content">
                                            <h6 className="highlight-text">{item.highlight}</h6>
                                            <p className="description">{item.content}</p>
                                            <div className="rating rating-spacing-2">
                                                <GetRating averageRating={item.rating} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
            {/* -- testimonial area end -- */}
        </>
    );
};

export default QuranLearningTestimonial;