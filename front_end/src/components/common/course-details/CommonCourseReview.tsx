import courseReviews from '@/data/courses/reviews-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const CommonCourseReview = () => {
    return (
        <>
            <div className="bd-postbox-comment">
                <ul>
                    {courseReviews.slice(3, 7).map(({ id, name, avatar, date, rating, comment }) => (
                        <li key={id}>
                            <div className="bd-postbox-comment-box d-sm-flex align-items-start">
                                <div className="bd-postbox-comment-info">
                                    <div className="bd-postbox-comment-avatar thumb-radius">
                                        <Image src={avatar} alt={name} />
                                    </div>
                                </div>
                                <div className="bd-postbox-comment-text">
                                    <div className="d-flex flex-wrap gap-15 align-items-center justify-content-between mb-10">
                                        <div className="post-rating rating-spacing-2">
                                            {[...Array(rating)].map((_, index) => (
                                                <Link key={index} href="#"><i className="fa fa-star"></i></Link>
                                            ))}
                                        </div>
                                        <button className="bd-like-btn" type="button">
                                            <i className="fa-regular fa-heart"></i>
                                        </button>
                                    </div>
                                    <div className="bd-postbox-comment-name">
                                        <h5 className="title mb--5"><Link href="#">{name}</Link></h5>
                                        <span className="post-meta">{date}</span>
                                    </div>
                                    <p>{comment}</p>
                                    <div className="bd-postbox-comment-reply">
                                        <Link href="#">Reply</Link>
                                    </div>
                                </div>
                            </div>
                        </li>
                    ))}
                </ul>
            </div>
        </>
    );
};

export default CommonCourseReview;