import React from 'react';

const BlogCommentForm = () => {
    return (
        <>
            <form action="#">
                <div className="row gy-30">
                    <div className="col-xxl-6 col-xl-6 col-lg-6 col-md-6">
                        <div className="bd-postbox-comment-input">
                            <input type="text" placeholder="Name" />
                        </div>
                    </div>
                    <div className="col-xxl-6 col-xl-6 col-lg-6 col-md-6">
                        <div className="bd-postbox-comment-input">
                            <input type="email" placeholder="Email" />
                        </div>
                    </div>
                    <div className="col-xxl-12">
                        <div className="bd-postbox-comment-input mb-15">
                            <textarea placeholder="Your Comment Here..."></textarea>
                        </div>
                        <div className="checkbox-option">
                            <input id="course-check-1" type="checkbox" />
                            <label htmlFor="course-check-1">Save my name, email, and website in this
                                browser for
                                the next time I comment.</label>
                        </div>
                    </div>
                    <div className="col-xxl-12">
                        <div className="bd-postbox-comment-btn">
                            <button type="submit" className="bd-btn btn-primary">Submit
                            Comment</button>
                        </div>
                    </div>
                </div>
            </form>
        </>
    );
};

export default BlogCommentForm;