"use client"

import CourseReviewForm from '@/form/CourseReviewForm';
import useGlobalContext from '@/hooks/useContexts';
import SlideToggle from '@/utils/SlideToggle';
import React from 'react';

const CommonReviewForm = () => {
    const { toggleOpen } = useGlobalContext();
    return (
        <>
            {/* course review form */}
            <div className="review-form">
                <button onClick={toggleOpen} id="show-review-box" className="bd-btn btn-primary">Write a Review</button>
                <SlideToggle className="bd-review-form mt-15">
                    <div className="bd-review-form-rating mb-15">
                        <p>Your email address will not be published. Required fields are marked
                            *</p>
                        <div className="bd-ratings-wrapper bd-ratings-wrapper-two rating-spacing-2">
                            <i className="fa fa-star"></i>
                            <i className="fa fa-star"></i>
                            <i className="fa fa-star"></i>
                            <i className="fa fa-star"></i>
                            <i className="fa fa-star"></i>
                            <input type="hidden" name="rating" defaultValue={5} />
                        </div>
                    </div>
                    <CourseReviewForm />
                </SlideToggle>
            </div>
        </>
    );
};

export default CommonReviewForm;