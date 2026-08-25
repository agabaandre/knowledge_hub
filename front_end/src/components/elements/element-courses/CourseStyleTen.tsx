import elementCoursesData from '@/data/elements/element-course-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';


const CourseStyleTen = () => {
    return (
        <section className="bd-elements-course section-space-bottom">
            <div className="container">
                <div className="row align-items-center justify-content-center">
                    <div className="col-lg-12">
                        <div className="bd-elements-section-wrapper section-title-space text-center">
                            <div className="bd-elements-line">
                                <div className="bd-separator-line line-left"></div>
                                <div className="bd-elements-title-wrapper">
                                    <h2 className="bd-elements-title small">Course Style 10</h2>
                                </div>
                                <div className="bd-separator-line line-right"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="row gy-30">
                    {elementCoursesData.slice(20, 23).map((course, index) => (
                        <div className="col-xl-4 col-lg-6 col-md-6" key={index}>
                            <div className="bd-course-wrapper style-eight">
                                <div className="bd-course-thumb-wrapper p-relative">
                                    <div className="bd-course-thumb">
                                        <Link href="#">
                                            <Image src={course.image} alt="course" />
                                        </Link>
                                    </div>
                                    <div className="bd-course-badge badge-left">
                                        <Link className="bd-badge badge-warning" href="#">{course.badgeText}</Link>
                                    </div>
                                </div>
                                <div className="bd-course-content primary-bg">
                                    <div className="bd-course-btn">
                                        <Link className="bd-btn-icon btn-primary rounded-circle" href="#">
                                            <i className="fa-sharp fa-regular fa-arrow-right-long"></i>
                                        </Link>
                                    </div>
                                    <div className="bd-course-author mb-10">
                                        <div className="thumb">
                                            {course.authorImage && <Image src={course.authorImage} alt="author" />}
                                        </div>
                                        <div className="name">
                                            <Link href="#">{course.authorName}</Link>
                                        </div>
                                    </div>
                                    <h5 className="bd-course-title underline mb-10">
                                        <Link href="#">{course.title}</Link>
                                    </h5>
                                    <p className="bd-course-description mb-10">{course.courseDescription}</p>
                                    <div className="bd-course-rating d-inline-flex align-items-center gap-10 mb-15">
                                        <div className="bd-course-rating-icon d-flex rating-color">
                                            {[...Array(5)].map((_, i) => (
                                                <i key={i} className="fa-solid fa-star"></i>
                                            ))}
                                        </div>
                                        <div className="bd-course-rating-text">
                                            <span>( {course.rating}/5 Ratings )</span>
                                        </div>
                                    </div>
                                    <div className="bd-course-price">
                                        <span className="current-price has-big">{`$${course.price}.00`}</span>
                                        <span className="old-price has-big">{`$${course.oldPrice}.00`}</span>
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

export default CourseStyleTen;