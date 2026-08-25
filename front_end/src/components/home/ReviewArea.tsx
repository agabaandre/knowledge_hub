import customerReviews from '@/data/review-data';
import Image from 'next/image';
import React from 'react';

const ReviewArea = () => {
    return (
        <>
            {/* -- Review area start -- */}
            <div className="customer-review-area section-space-top">
                <div className="container">
                    <div className="customer-review-title">
                        <div className="section-title-wrapper text-center">
                            <span className="bd-section-subtitle">Customer Reviews</span>
                            <h2 className="bd-section-title">What Our Satisfied Customers Say</h2>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-xxl-12 overflow-hidden">
                            <div className="customer-review-wrapper">
                                <div className="customer-review-wrap">
                                    {
                                        customerReviews.slice(0, 10).map((item) => (
                                            <div className="customer-review-style-1" key={item.id}>
                                                <div className="content">
                                                    <div className="rating-icon">
                                                        <Image src={item.ratingIcon} alt="image" />
                                                    </div>
                                                    <p className="content-title">for <span>{item.contentTitle}</span></p>
                                                    <p className="text">{item.text}</p>
                                                    <div className="info">
                                                        <h5 className="title"> <span>by </span> {item.author}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    }
                                </div>
                                <div className="customer-review-wrap">
                                    {
                                        customerReviews.slice(11, 20).map((item) => (
                                            <div className="customer-review-style-1" key={item.id}>
                                                <div className="content">
                                                    <div className="rating-icon">
                                                        <Image src={item.ratingIcon} alt="image" />
                                                    </div>
                                                    <p className="content-title">for <span>{item.contentTitle}</span></p>
                                                    <p className="text">{item.text}</p>
                                                    <div className="info">
                                                        <h5 className="title"> <span>by </span> {item.author}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    }
                                </div>
                                <div className="customer-review-wrap">
                                    {
                                        customerReviews.slice(0, 10).map((item) => (
                                            <div className="customer-review-style-1" key={item.id}>
                                                <div className="content">
                                                    <div className="rating-icon">
                                                        <Image src={item.ratingIcon} alt="image" />
                                                    </div>
                                                    <p className="content-title">for <span>{item.contentTitle}</span></p>
                                                    <p className="text">{item.text}</p>
                                                    <div className="info">
                                                        <h5 className="title"> <span>by</span> {item.author}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    }
                                </div>
                                <div className="customer-review-wrap">
                                    {
                                        customerReviews.slice(11, 20).map((item) => (
                                            <div className="customer-review-style-1" key={item.id}>
                                                <div className="content">
                                                    <div className="rating-icon">
                                                        <Image src={item.ratingIcon} alt="image" />
                                                    </div>
                                                    <p className="content-title">for <span>{item.contentTitle}</span></p>
                                                    <p className="text">{item.text}</p>
                                                    <div className="info">
                                                        <h5 className="title"> <span>by </span> {item.author}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* -- Review area end -- */}
        </>
    );
};

export default ReviewArea;