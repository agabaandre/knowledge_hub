import Link from 'next/link';
import React from 'react';
interface IRating {
    stars: number;
    width: string;
    percent: string;
    count: number;
    barClass: string;
}
const ratings: IRating[] = [
    { stars: 5, width: '90%', percent: '82%', count: 212, barClass: 'bar-bg-3' },
    { stars: 4, width: '75%', percent: '12%', count: 28, barClass: 'bar-bg-2' },
    { stars: 3, width: '50%', percent: '4%', count: 9, barClass: 'bar-bg-4' },
    { stars: 2, width: '30%', percent: '1%', count: 5, barClass: 'bar-bg-5' },
    { stars: 1, width: '10%', percent: '1%', count: 1, barClass: 'bar-bg-6' }
];

const RatingProgress = ({ stars, width, percent, count, barClass }: IRating) => (
    <div className="bd-review-progress-bar progress-style-2">
        <div className="bd-review-text">{stars}</div>
        <div className="single-progress">
            <div className="progress">
                <div
                    className={`progress-bar ${barClass} wow bdFadeInLeft`}
                    data-wow-duration="0.5s"
                    data-wow-delay=".3s"
                    role="progressbar"
                    style={{ width }}
                    aria-valuenow={parseInt(width)}
                    aria-valuemin={0}
                    aria-valuemax={100}
                ></div>
            </div>
        </div>
        <div className="bd-review-meta">
            <span className="bd-review-percent">{percent}</span>
            <span className="bd-review-number">{count}</span>
        </div>
    </div>
);

const CourseRating = () => {
    return (
        <div className="bd-course-feature-box mt-30" id="review">
            <h3 className="bd-course-details-content-title">Course Rating</h3>
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
                                <RatingProgress key={rating.stars} {...rating} />
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CourseRating;
