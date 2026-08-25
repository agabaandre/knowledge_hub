import React from 'react';
import Link from 'next/link';
import { IReviewItem } from '@/interFace/dashboard-interface';


const ReviewItem: React.FC<IReviewItem> = ({ title, rating, reviewsCount, description }) => (
  
  <div className="bd-dashboard-review-item mb-30">
    <h5 className="title underline">
      <Link href="courses-details.html">{title}</Link>
    </h5>
    <div className="course-info d-flex align-content-center justify-content-between flex-wrap gap-2 mb-10">
      <div className="bd-course-rating-wrap d-flex align-items-center gap-10">
        <div className="bd-course-rating-icon fs-14 d-flex rating-color">
          {Array.from({ length: 5 }, (_, index) => (
            <i key={index} className={`fa-solid fa-star${index < rating ? '' : '-half'}`}></i>
          ))}
        </div>
        <div className="bd-course-rating-text">
          <span>{rating} ({reviewsCount})</span>
        </div>
      </div>
      <div className="bd-button-action">
        <Link className="bd-default-tooltip edit" href="#">
          <span><i className="fa-light fa-pen-to-square"></i></span>
        </Link>
        <Link className="bd-default-tooltip view" href="#">
          <span><i className="fa-sharp fa-light fa-eye"></i></span>
        </Link>
        <Link className="bd-default-tooltip delete" href="#">
          <span><i className="fa-light fa-trash-can"></i></span>
        </Link>
      </div>
    </div>
    <p>{description}</p>
  </div>
);

const InstructorReviewsMain: React.FC = () => {
  const reviews = [
    {
      title: 'Full Stack Web Development with MERN',
      rating: 5,
      reviewsCount: 258,
      description: 'So I said lurgy dropped a clanger Jeffrey bugger cuppa gosh David blatant have it, standard A bit of how\'s your father my lady absolutely.'
    },
    {
      title: 'Web Development with PHP & Laravel',
      rating: 4,
      reviewsCount: 258,
      description: 'So I said lurgy dropped a clanger Jeffrey bugger cuppa gosh David blatant have it, standard A bit of how\'s your father my lady absolutely.'
    },
    {
      title: 'Digital Marketing Mastery seo & Social Media',
      rating: 5,
      reviewsCount: 258,
      description: 'So I said lurgy dropped a clanger Jeffrey bugger cuppa gosh David blatant have it, standard A bit of how\'s your father my lady absolutely.'
    }
  ];

  return (
      <div className="col-xl-9 col-lg-9 col-md-8">
        <div className="bd-dashboard-inner">
          <div className="bd-dashboard-title-inner">
            <h4 className="bd-dashboard-title">Reviews</h4>
          </div>
          <div className="bd-dashboard-reviews-wrapper">
            {reviews.map((review, index) => (
              <ReviewItem key={index} {...review} />
            ))}
          </div>
        </div>
      </div>
  );
};

export default InstructorReviewsMain;
