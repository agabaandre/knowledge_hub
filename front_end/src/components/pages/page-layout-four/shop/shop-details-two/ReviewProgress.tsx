import Link from 'next/link';
import React from 'react';

interface RatingBarProps {
  rating: number;
  percentage: number;
  totalReviews: number;
  barStyleClass: string;
}

const RatingBar: React.FC<RatingBarProps> = ({ rating, percentage, totalReviews, barStyleClass }) => {
  return (
    <div className="bd-review-progress-bar progress-style-2">
      <div className="bd-review-text">{rating}</div>
      <div className="single-progress">
        <div className="progress">
          <div
            className={`progress-bar ${barStyleClass} wow bdFadeInLeft`}
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
        <span className="bd-review-number">{totalReviews}</span>
      </div>
    </div>
  );
};

const ReviewProgress: React.FC = () => {
  const totalReviews = 150;
  const ratingsData = [
    { rating: 5, percentage: 70, totalReviews: 105, barStyleClass: 'bar-bg-3' },
    { rating: 4, percentage: 10, totalReviews: 15, barStyleClass: 'bar-bg-2' },
    { rating: 3, percentage: 5, totalReviews: 8, barStyleClass: 'bar-bg-4' },
    { rating: 2, percentage: 1, totalReviews: 1, barStyleClass: 'bar-bg-5' },
    { rating: 1, percentage: 1, totalReviews: 2, barStyleClass: 'bar-bg-6' }
  ];

  return (
    <>
      <div className="row gy-30 align-items-center">
        <div className="col-xl-2 col-lg-3 col-md-4 col-sm-4">
          <div className="bd-rating-box">
            <div className="bd-rating-box-number">4.8</div>
            <div className="bd-rating-box-icon">
              {[...Array(5)].map((_, index) => (
                <Link key={index} href="#">
                  <i className="fa fa-star"></i>
                </Link>
              ))}
            </div>
            <span className="bd-rating-box-title">({totalReviews} Reviews)</span>
          </div>
        </div>
        <div className="col-xl-10 col-lg-9 col-md-8 col-sm-12">
          <div className="bd-review-progress-wrapper">
            {ratingsData.map((data, index) => (
              <RatingBar
                key={index}
                rating={data.rating}
                percentage={data.percentage}
                totalReviews={data.totalReviews}
                barStyleClass={data.barStyleClass}
              />
            ))}
          </div>
        </div>
      </div>
    </>
  );
};

export default ReviewProgress;
