import elementCoursesData from '@/data/elements/element-course-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const CourseStyleFourteen = () => {
    return (
        <section className="bd-elements-course section-space">
            <div className="container">
                <div className="row align-items-center justify-content-center">
                    <div className="col-lg-12">
                        <div className="bd-elements-section-wrapper section-title-space text-center">
                            <div className="bd-elements-line">
                                <div className="bd-separator-line line-left"></div>
                                <div className="bd-elements-title-wrapper">
                                    <h2 className="bd-elements-title small">Course Style 14</h2>
                                </div>
                                <div className="bd-separator-line line-right"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="row gy-30">
                    {elementCoursesData.slice(28, 30).map((course, index) => (
                        <div className="col-xl-6 col-lg-6 col-md-6" key={index}>
                            <div className="bd-course-wrapper style-eleven circle primary-bg">
                                <div className="bd-course-thumb-wrapper p-relative">
                                    <div className="bd-course-thumb">
                                        <Link href="#"><Image src={course.image} alt="Course" /></Link>
                                    </div>
                                    <div className="bd-course-badge badge-left">
                                        <Link className="bd-badge badge-warning mb-10" href="#">{course.badgeText}</Link>
                                    </div>
                                </div>
                                <div className="bd-course-content">
                                    <div className="bd-course-price mb-15">
                                        <span className="current-price text-primary">${course.price}.00</span>
                                        <span className="old-price">${course.oldPrice}.00</span>                                    </div>
                                    <h5 className="bd-course-title underline mb-10"><Link href="#">{course.title}</Link></h5>
                                    <p className="bd-course-description mb-15">{course.courseDescription}</p>
                                    <div className="bd-course-rating d-inline-flex align-items-center gap-10 mb-15">
                                        <div className="bd-course-rating-icon fs-14 d-flex rating-color">
                                            {[...Array(5)].map((_, i) => <i key={i} className="fa-solid fa-star"></i>)}
                                        </div>
                                        <div className="bd-course-rating-text">
                                            <span>{course.rating} (313 Ratings)</span>
                                        </div>
                                    </div>
                                    <div className="bd-course-content-bottom d-flex align-items-center">
                                        <div className="bd-course-lesson has-separator">
                                            <span><i className="fa-light fa-clock"></i> {course.lessons} Lessons</span>
                                        </div>
                                        <div className="bd-course-lesson">
                                            <span><i className="fa-sharp fa-solid fa-list"></i> {course.students} Students</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default CourseStyleFourteen;
