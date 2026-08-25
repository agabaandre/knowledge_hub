import React from 'react';
import ReviewItem from './ReviewItem';
import { IReviewItem } from '@/interFace/dashboard-interface';

const reviews: IReviewItem[] = [
    {
        title: 'Full Stack Web Development with MERN',
        rating: 5,
        reviewsCount: 258,
        description:
            'So I said lurgy dropped a clanger Jeffrey bugger cuppa gosh David blatant have it, standard A bit of how\'s your father my lady absolutely.',
    },
    {
        title: 'Web Development with PHP & Laravel',
        rating: 5,
        reviewsCount: 258,
        description:
            'So I said lurgy dropped a clanger Jeffrey bugger cuppa gosh David blatant have it, standard A bit of how\'s your father my lady absolutely.',
    },
    {
        title: 'Digital Marketing Mastery SEO & Social Media',
        rating: 5,
        reviewsCount: 258,
        description:
            'So I said lurgy dropped a clanger Jeffrey bugger cuppa gosh David blatant have it, standard A bit of how\'s your father my lady absolutely.',
    },
];

const StudentReviewsMain: React.FC = () => (
    <div className="col-xl-9 col-lg-9 col-md-8">
        <div className="bd-dashboard-inner">
            <div className="bd-dashboard-title-inner">
                <h4 className="bd-dashboard-title">Reviews</h4>
            </div>
            <div className="bd-dashboard-reviews-wrapper">
                {reviews.map((review, idx) => (
                    <ReviewItem key={idx} {...review} />
                ))}
            </div>
        </div>
    </div>
);

export default StudentReviewsMain;
