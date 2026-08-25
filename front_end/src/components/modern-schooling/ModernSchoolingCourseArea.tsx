import coursesData from '@/data/courses/courses-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import GetRating from '../common/GetRating';

const ModernSchoolingCourseArea = () => {
    return (
        <>
            {/* -- course area start -- */}
            <section className="bd-course-area section-space fix">
                <div className="container">
                    <div className="row justify-content-between align-items-center section-title-space g-30">
                        <div className="col-xl-7 col-lg-7 col-md-9">
                            <div className="bd-section-title-wrapper">
                                <span className="bd-section-subtitle">Top Picks for You</span>
                                <h2 className="bd-section-title">Discover Your Next Course</h2>
                            </div>
                        </div>
                        <div className="col-xl-5 col-lg-5 col-md-3">
                            <div className="bd-instructor-btn text-md-end">
                                <Link className="bd-btn btn-outline-border-primary" href="/courses">View More</Link>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            coursesData.slice(6, 10).map((course) => (
                                <div className="col-xl-6 col-lg-6 col-md-6" key={course.id}>
                                    <div className="bd-course-wrapper style-eleven bg-box-shadow wow bdFadeInUp" data-wow-delay=".3s">
                                        <div className="bd-course-thumb-wrapper p-relative">
                                            <div className="bd-course-thumb">
                                                <Link href={`/courses/course-details/${course.id}`}>
                                                    <Image src={course.image} style={{ width: "100%", height: "auto" }} alt="images" />
                                                </Link>
                                            </div>
                                            <div className="bd-course-badge badge-left">
                                                <Link className={`bd-badge ${course.badgeClass}`} href="#">{course.badge}</Link>
                                            </div>
                                        </div>
                                        <div className="bd-course-content">
                                            <div className="bd-course-price">
                                                <span className="current-price">{`$${course.price}.00`}</span>
                                                <span className="old-price">{`$${course.discount}.00`}</span>
                                            </div>
                                            <h5 className="bd-course-title underline"><Link href={`/courses/course-details/${course.id}`}>{course.title}</Link></h5>
                                            <p className="bd-course-description">{course.courseDescription}</p>
                                            <div className="bd-course-rating d-inline-flex align-items-center gap-10">
                                                <div className="bd-course-rating-icon fs-14 d-flex rating-color">
                                                    <GetRating averageRating={course.rating} />
                                                </div>
                                                <div className="bd-course-rating-text">
                                                    <span>{course.rating} ({course.ratingNum} Ratings)</span>
                                                </div>
                                            </div>
                                            <div className="bd-course-content-bottom">
                                                <div className="bd-course-lesson">
                                                    <span><i className="fa-light fa-clock"></i> {course.lessons} Lessons</span>
                                                </div>
                                                <div className="bd-course-lesson">
                                                    <span><i className="fa-sharp fa-solid fa-list"></i> {course.students} Students</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- course area end -- */}
        </>
    );
};

export default ModernSchoolingCourseArea;