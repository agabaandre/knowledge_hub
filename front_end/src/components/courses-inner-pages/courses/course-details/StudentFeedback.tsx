import Link from 'next/link';
import React from 'react';

interface Rating {
    stars: number;
    percentage: number;
    count: number;
    barColor: string;
}

const ratings: Rating[] = [
    { stars: 5, percentage: 90, count: 212, barColor: 'bar-bg-3' },
    { stars: 4, percentage: 75, count: 28, barColor: 'bar-bg-2' },
    { stars: 3, percentage: 50, count: 9, barColor: 'bar-bg-4' },
    { stars: 2, percentage: 30, count: 5, barColor: 'bar-bg-5' },
    { stars: 1, percentage: 10, count: 1, barColor: 'bar-bg-6' },
];

const RatingBar: React.FC<Rating> = ({ stars, percentage, count, barColor }) => (
    <div className="bd-review-progress-bar progress-style-2">
        <div className="bd-review-text">{stars}</div>
        <div className="single-progress">
            <div className="progress">
                <div
                    className={`progress-bar ${barColor} wow bdFadeInLeft`}
                    data-wow-duration="0.5s"
                    data-wow-delay=".3s"
                    role="progressbar"
                    style={{ width: `${percentage}%` }}
                    aria-valuenow={percentage}
                    aria-valuemin={0}
                    aria-valuemax={100}
                ></div>
            </div>
        </div>
        <div className="bd-review-meta">
            <span className="bd-review-percent">{percentage}%</span>
            <span className="bd-review-number">{count}</span>
        </div>
    </div>
);

const StudentFeedback: React.FC = () => {
    return (
        <div className="bd-student-feedback mb-30">
            <h3 className="bd-course-details-content-title">Student Feedback</h3>
            <div className="bd-review-rating-wrapper">
                <div className="row gy-30 align-items-center">
                    <div className="col-xl-3 col-lg-3 col-md-4 col-sm-4">
                        <div className="bd-rating-box">
                            <div className="bd-rating-box-number">4.9</div>
                            <div className="bd-rating-box-icon rating-spacing-2">
                                {[...Array(5)].map((_, index) => (
                                    <Link href="#" key={index}>
                                        <i className="fa fa-star"></i>
                                    </Link>
                                ))}
                            </div>
                            <span className="bd-rating-box-title">(234 Reviews)</span>
                        </div>
                    </div>
                    <div className="col-xl-9 col-lg-9 col-md-8 col-sm-12">
                        <div className="bd-review-progress-wrapper">
                            {ratings.map((rating) => (
                                <RatingBar key={rating.stars} {...rating} />
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentFeedback;