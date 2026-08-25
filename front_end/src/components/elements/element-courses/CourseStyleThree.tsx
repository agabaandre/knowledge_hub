import elementCoursesData from '@/data/elements/element-course-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const CourseStyleThree = () => (
    <section className="bd-elements-course section-space-bottom">
        <div className="container">
            <div className="row align-items-center justify-content-center">
                <div className="col-lg-12">
                    <div className="bd-elements-section-wrapper section-title-space text-center">
                        <div className="bd-elements-line">
                            <div className="bd-separator-line line-left"></div>
                            <div className="bd-elements-title-wrapper">
                                <h2 className="bd-elements-title small">Course Style 03</h2>
                            </div>
                            <div className="bd-separator-line line-right"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="row gy-30">
                {
                    elementCoursesData.slice(7, 11).map((course,index) => (
                        <div className="col-xl-3 col-lg-4 col-md-6" key={index}>
                            <div className="bd-course-wrapper style-three">
                                <div className="bd-course-thumb-wrapper p-relative">
                                    <div className="bd-course-thumb">
                                        <Link href="#">
                                            <Image style={{ width: "100%", height: "auto" }} src={course.image} alt="Course Thumbnail" />
                                        </Link>
                                    </div>
                                    <div className="bd-course-top-meta">
                                        <div className="bd-course-badge">
                                            <div className="bd-course-badge-single">
                                                <Link className={`bd-badge ${course.badgeColor}`} href="#">{course.badgeText}</Link>
                                            </div>
                                        </div>
                                        <button className="bd-course-like has-bg">
                                            <i className="icon-heart"></i>
                                        </button>
                                    </div>
                                </div>
                                <div className="bd-course-content">
                                    <div className="bd-course-rating d-inline-flex align-items-center gap-10 mb-10">
                                        <div className="bd-course-rating-icon fs-14 d-flex rating-color">
                                            {[...Array(5)].map((_, index) => (
                                                <i key={index} className="fa-solid fa-star"></i>
                                            ))}
                                        </div>
                                        <div className="bd-course-rating-text">
                                            <span>{course.rating} ({course.reviews} Ratings)</span>
                                        </div>
                                    </div>
                                    <h5 className="bd-course-title underline mb-10">
                                        <Link href="#">{course.title}</Link>
                                    </h5>
                                    <div className="bd-course-price mb-10">
                                        <span className="current-price">{`$${course.price}.00`}</span>
                                        {course.oldPrice && <span className="old-price">{`$${course.oldPrice}.00`}</span>}
                                    </div>
                                    <div className="bd-course-meta d-flex-between">
                                        <div className="bd-course-author">
                                            <div className="thumb">
                                                {course.authorImage && <Image src={course.authorImage} alt="Author" />}
                                            </div>
                                            <div className="name">
                                                <Link href="#">{course.authorName}</Link>
                                            </div>
                                        </div>
                                        <div className="bd-course-lesson">
                                            <span><i className="fa-light fa-book"></i> {course.lessons} Lessons</span>
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
);

export default CourseStyleThree;
