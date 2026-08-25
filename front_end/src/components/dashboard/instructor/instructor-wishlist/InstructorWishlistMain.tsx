import GetRating from '@/components/common/GetRating';
import coursesData from '@/data/courses/courses-data';
import RenderTextContent from '@/utils/RenderTextContent';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const InstructorWishlistMain = () => {
    return (
        <>
                <div className="col-xl-9 col-lg-9 col-md-8">
                    <div className="bd-dashboard-inner">
                        <div className="bd-dashboard-title-inner">
                            <h4 className="bd-dashboard-title">Instructor Wishlist</h4>
                        </div>
                        <div className="container">
                            <div className="row g-30">
                                {
                                    coursesData.slice(91, 95).map((item) => (
                                        <div className="col-xl-6 col-lg-6 col-md-12" key={item.id}>
                                            <div className="bd-course-wrapper style-seven">
                                                <div className="bd-course-thumb-wrapper bd-course-thumb-style p-relative">
                                                    {
                                                        item.badge &&
                                                        <div className="bd-course-badge">
                                                            <Link className={`bd-badge ${item.badgeClass}`} href="#">{item.badge}</Link>
                                                        </div>
                                                    }
                                                    <div className={`bd-course-thumb-bg ${item.imageClassName}`}>
                                                        <Image src={item.image} alt="images" />
                                                    </div>
                                                    <div className={`bd-course-thumb-instructor ${item.instructorImageClassName}`}>
                                                        {item.instructorImage && <Image src={item.instructorImage} alt="instructor" />}
                                                    </div>
                                                    {RenderTextContent(item)} {/* Rendering text content conditionally */}
                                                </div>
                                                <div className="bd-course-content">
                                                    <div className="bd-course-content-bottom d-flex flex-wrap align-items-center mb-10">
                                                        <div className="bd-course-lesson has-separator">
                                                            <span><i className="fa-light fa-clock"></i> {item.lessons} Lessons</span>
                                                        </div>
                                                        <div className="bd-course-lesson">
                                                            <span><i className="fa-sharp fa-solid fa-list"></i> {item.students}{" "}Students</span>
                                                        </div>
                                                    </div>
                                                    <h5 className="bd-course-title underline mb-10">
                                                        <Link href={`/courses/course-details/${item.id}`}>{item.title}</Link>
                                                    </h5>
                                                    <p className="bd-course-description mb-10">{item.courseDescription}</p>
                                                    <div className="bd-course-rating d-inline-flex align-items-center gap-10 mb-30">
                                                        <div className="bd-course-rating-icon d-flex rating-color">
                                                            <GetRating averageRating={item.rating} />
                                                        </div>
                                                        <div className="bd-course-rating-text">
                                                            <span>( {item.rating}/5 Ratings )</span>
                                                        </div>
                                                    </div>
                                                    <div className="bd-course-btn">
                                                        <Link className="bd-btn btn-outline-primary" href={`/courses/course-details/${item.id}`}>Enroll Now</Link>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))
                                }
                            </div>
                        </div>
                    </div>
                </div>
        </>
    );
};

export default InstructorWishlistMain;