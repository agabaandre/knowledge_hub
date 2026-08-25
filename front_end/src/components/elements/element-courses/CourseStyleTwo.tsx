import elementCoursesData from '@/data/elements/element-course-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const CourseStyleTwo = () => {

    return (
        <section className="bd-elements-course section-space-bottom">
            <div className="container">
                <div className="row align-items-center justify-content-center">
                    <div className="col-lg-12">
                        <div className="bd-elements-section-wrapper section-title-space text-center">
                            <div className="bd-elements-line">
                                <div className="bd-separator-line line-left"></div>
                                <div className="bd-elements-title-wrapper">
                                    <h2 className="bd-elements-title small">Course Style 02</h2>
                                </div>
                                <div className="bd-separator-line line-right"></div>
                            </div>
                            <p></p>
                        </div>
                    </div>
                </div>
                <div className="row gy-30">
                    {elementCoursesData.slice(4, 7).map((course,index) =>
                        <div className="col-xl-4 col-lg-6 col-md-6" key={index}>
                            <div className="bd-course-wrapper style-two">
                                <Link href="#" className="bd-course-thumb-wrapper bd-course-thumb-style-three p-relative">
                                    <div className="bd-course-thumb-bg">{course.bgImage && <Image src={course.bgImage} alt="course" />}</div>
                                    <div className="bd-course-instructor"><Image src={course.image} alt="instructor" /></div>
                                    <div className="bd-course-logo">{course.logo && <Image src={course.logo} alt="logo" />}</div>
                                    {
                                        course.courseTextContent == true ?
                                            <>
                                                {course.isCourseThreeStyled == true ?
                                                    <>
                                                        <div className="bd-course-text-content">
                                                            <div className="text fs-40 mb--5 text-white">{course.level}</div>
                                                            <div className="text fs-40 mb--5 text-white">{course.lavelTwo}</div>
                                                            <div className="text fs-40 text-white">{course.lavelThree}</div>
                                                        </div>
                                                    </>
                                                    :
                                                    <>
                                                        <div className="bd-course-text-content">
                                                            <div className="text-3 fs-30 white-bg text-primary pl-10 pr-10 pt--5 pb--5 d-inline-block latter-sp-2 uppercase mb-30 filter-shadow radius-10">
                                                                {course.level}</div>
                                                            <div className="text fs-30 white-bg text-primary pl-10 pr-10 pt--5 pb--5 latter-sp-2 uppercase filter-shadow radius-10">
                                                                {course.lavelTwo}</div>
                                                        </div>
                                                    </>
                                                }
                                            </>
                                            :
                                            <>
                                                <div className="bd-course-text-content">
                                                    <div className="text-1 fs-50 text-white mb-25">{course.level}</div>
                                                    <div className="text-3 fs-30 white-bg text-primary pl-10 pr-10 pt--5 pb--5 d-inline-block latter-sp-2 uppercase filter-shadow radius-10">
                                                        {course.lavelTwo}</div>
                                                </div>
                                            </>
                                    }
                                    <div className="bd-course-badge">
                                        <span className="bd-badge badge-primary">15% Off</span>
                                    </div>

                                </Link>
                                <div className="bd-course-content">
                                    <div className="bd-course-meta d-flex-between mb-15">
                                        <div className="bd-course-tag">
                                            <Link className="bd-badge badge-outline-light badge-transparent" href="#">{course.tag}</Link>
                                        </div>
                                        <div className="bd-course-lesson">
                                            <span><i className="fa-light fa-book"></i> {course.lessons} Lessons</span>
                                        </div>
                                    </div>
                                    <h5 className="bd-course-title underline mb-10"><Link href="#">{course.title}</Link></h5>
                                    <div className="bd-course-rating d-flex-between">
                                        <div className="bd-course-price">
                                            <span className="current-price">{`$${course.price}.00`}</span>
                                        </div>
                                        <div className="bd-course-rating-wrap d-flex align-items-center gap-10">
                                            <div className="bd-course-rating-icon fs-14 d-flex rating-color">
                                                {Array.from({ length: 5 }).map((_, i) => (
                                                    <i className="fa-solid fa-star" key={i}></i>
                                                ))}
                                            </div>
                                            <div className="bd-course-rating-text">
                                                <span>{course.rating} ({course.reviews})</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="bd-course-full-border"></div>
                                    <div className="btn-wrap d-flex flex-wrap align-items-center justify-content-between">
                                        <div className="bd-course-author">
                                            <div className="thumb">{course.authorImage && <Image src={course.authorImage} alt="author" />}</div>
                                            <div className="name"><Link href="#">{course.authorName}</Link></div>
                                        </div>
                                        <div className="bd-course-btn">
                                            <Link className="bd-text-btn" href="#">
                                                Enroll Now
                                                <span className="box-icon">
                                                    <i className="fa-regular fa-arrow-right-long first-icon"></i>
                                                    <i className="fa-regular fa-arrow-right-long second-icon"></i>
                                                </span>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </section>
    );
};

export default CourseStyleTwo;
