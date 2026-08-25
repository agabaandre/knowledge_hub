import courseReviews from '@/data/courses/reviews-data';
import { ICourseReview } from '@/interFace/interFace';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const StarRating: React.FC<{ rating: number }> = ({ rating }) => (
    <div className="post-rating rating-spacing-2">
        {[...Array(5)].map((_, index) => (
            <Link href="#" key={index}>
                <i className={`fa ${index < rating ? 'fa-star' : 'fa-star-o'}`}></i>
            </Link>
        ))}
    </div>
);

const ReviewItem: React.FC<{ review: ICourseReview }> = ({ review }) => (
    <li>
        <div className="bd-postbox-comment-box d-sm-flex align-items-start">
            <div className="bd-postbox-comment-info">
                <div className="bd-postbox-comment-avatar">
                    <Image src={review.avatar} alt={review.name} />
                </div>
            </div>
            <div className="bd-postbox-comment-text">
                {
                    review.rating != 0 &&
                    <div className="d-flex flex-wrap gap-15 align-items-center justify-content-between mb-10">
                        <StarRating rating={review.rating} />
                        <button className="bd-like-btn" type="button">
                            <i className="fa-regular fa-heart"></i>
                        </button>
                    </div>
                }
                <div className="bd-postbox-comment-name">
                    <h5 className="title mb--5"><a href="#">{review.name}</a></h5>
                    <span className="post-meta">{review.date}</span>
                </div>
                <p>{review.comment}</p>
                <div className="bd-postbox-comment-reply">
                    <Link href="#">Reply</Link>
                </div>
            </div>
        </div>
        {review.replies && (
            <ul className="children">
                {review.replies.map(reply => (
                    <ReviewItem key={reply.id} review={reply} />
                ))}
            </ul>
        )}
    </li>
);

const CourseDetailsReviews: React.FC = () => {
    return (
        <div className="bd-course-detalis-reviews mb-30">
            <h3 className="bd-course-details-content-title">Reviews</h3>
            <div className="bd-postbox-comment">
                <ul>
                    {courseReviews.slice(0,3).map(review => (
                        <ReviewItem key={review.id} review={review} />
                    ))}
                </ul>
            </div>
        </div>
    );
};

export default CourseDetailsReviews;
