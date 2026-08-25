import React from 'react';

const ShopTwoReviewForm = () => {
    return (
        <>
            <form action="#">
                <div className="row gy-30">
                    <div className="col-xxl-6 col-lg-6">
                        <div className="bd-review-form-group">
                            <label htmlFor="name">Name <span>*</span></label>
                            <input type="text" id="name" placeholder="Enter your name" required />
                        </div>
                    </div>
                    <div className="col-xxl-6 col-lg-6">
                        <div className="bd-review-form-group">
                            <label htmlFor="email">Email <span>*</span></label>
                            <input type="email" id="email" placeholder="Enter your email" required />
                        </div>
                    </div>
                    <div className="col-xxl-12 col-lg-12">
                        <div className="bd-review-form-group">
                            <label htmlFor="comment">Your Review <span>*</span></label>
                            <textarea id="comment" placeholder="Write your review" required></textarea>
                        </div>
                    </div>
                    <div className="col-xxl-12 col-lg-12">
                        <div className="bd-review-form-group">
                            <button className="bd-btn btn-primary" type="submit">Submit
                                Review</button>
                        </div>
                    </div>
                </div>
            </form>
        </>
    );
};

export default ShopTwoReviewForm;