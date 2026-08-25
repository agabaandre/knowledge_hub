import elementCoursesData from '@/data/elements/element-course-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const CourseStyleNine = () => {
    return (
        <>
            {/* -- course style 09 start -- */}
            <section className="bd-elements-course section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Course Style 09</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            elementCoursesData.slice(17, 20).map((course, index) => (
                                <div className="col-xl-4 col-lg-6 col-md-6" key={index}>
                                    <div className="bd-course-wrapper style-seven">
                                        <div className="bd-course-thumb-wrapper bd-course-thumb-style p-relative">
                                            <div className="bd-course-badge">
                                                <Link className="bd-badge badge-secondary" href="#">{course.badgeText}</Link>
                                            </div>
                                            <div className={`bd-course-thumb-bg ${course.imageBgClass}`}>{course.bgImage && <Image src={course.bgImage} alt="images" />}</div>
                                            <div className={`bd-course-thumb-instructor ${course.instructorImagePosition}`}><Image src={course.image} alt="instructor" /></div>
                                            {
                                                course.courseTextContent == true ?
                                                    <>
                                                        {course.isCourseThreeStyled == true ?
                                                            <>
                                                                <div className="bd-course-text-content">
                                                                    <div className="text-1 fs-50 mb--5 text-white">{course.level}</div>
                                                                    <div className="text-2 fs-50 mb--5 text-white">{course.lavelTwo}</div>
                                                                    <div className="text-3 fs-28 white-bg text-primary pl-10 pr-10 pt--5 pb--5 d-inline-block latter-sp-2 uppercase">
                                                                        {course.lavelThree}
                                                                    </div>
                                                                </div>
                                                            </>
                                                            :
                                                            <>
                                                                <div className="bd-course-text-content">
                                                                    <div className="text-1 fs-50 text-white mb-25">{course.level}</div>
                                                                    <div className="text-3 fs-30 white-bg text-primary pl-10 pr-10 pt--5 pb--5 d-inline-block latter-sp-2 uppercase">
                                                                        {course.lavelTwo}</div>
                                                                </div>
                                                            </>
                                                        }
                                                    </>
                                                    :
                                                    <>
                                                        <div className="bd-course-overly-title fs-140 text-warning">{course.level}</div>
                                                    </>
                                            }
                                        </div>
                                        <div className="bd-course-content">
                                            <div className="bd-course-content-bottom d-flex flex-wrap align-items-center mb-10">
                                                <div className="bd-course-lesson has-separator">
                                                    <span><i className="fa-light fa-clock"></i> {course.lessons} Lessons</span>
                                                </div>
                                                <div className="bd-course-lesson">
                                                    <span><i className="fa-sharp fa-solid fa-list"></i> {course.students} Students</span>
                                                </div>
                                            </div>
                                            <h5 className="bd-course-title underline mb-10"><Link href="#">{course.title}</Link></h5>
                                            <p className="bd-course-description mb-10">{course.courseDescription}</p>
                                            <div className="bd-course-rating d-inline-flex align-items-center gap-10 mb-30">
                                                <div className="bd-course-rating-icon d-flex rating-color">
                                                    <i className="fa-solid fa-star"></i>
                                                    <i className="fa-solid fa-star"></i>
                                                    <i className="fa-solid fa-star"></i>
                                                    <i className="fa-solid fa-star"></i>
                                                    <i className="fa-solid fa-star"></i>
                                                </div>
                                                <div className="bd-course-rating-text">
                                                    <span>( {course.rating}/5 Ratings )</span>
                                                </div>
                                            </div>
                                            <div className="bd-course-btn">
                                                <Link className="bd-btn btn-outline-primary" href="#">Enroll Now</Link>
                                            </div>
                                        </div>
                                        <div className="bd-course-info">
                                            <div className="bd-course-info-wrapper">
                                                <Link className="bd-badge badge-primary mb-15" href="#">{course.certificateBadge}</Link>
                                                <h5 className="bd-course-title underline mb-10"><Link href="#">{course.courseTitle}</Link></h5>
                                                <div className="bd-course-info-label mb-15">Level : <span>{course.smallText}</span></div>
                                                <p>{course.details}</p>
                                                <div className="bd-course-info-list">
                                                    <ul>
                                                        {course.courseList?.map((topic, index) => (
                                                            <li key={index}>
                                                                <i className="far fa-check"></i> {topic}
                                                            </li>
                                                        ))}
                                                    </ul>
                                                </div>
                                                <div className="bd-course-price mb-20">
                                                    <span className="current-price has-big text-primary">{`$${course.price}.00`}</span>
                                                    <span className="old-price has-big">{`$${course.oldPrice}.00`}</span>
                                                </div>
                                                <div className="bd-course-action-btn d-flex align-items-center gap-15">
                                                    <Link href="#" className="bd-btn btn-outline-border-primary">View Details</Link>
                                                    <button className="bd-btn-icon btn-warning outline-warning"><i
                                                        className="icon-heart"></i></button>
                                                    <Link href="#" className="bd-btn-icon btn-info outline-info"><i
                                                        className="fa-light fa-share"></i></Link>
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
            {/* -- course style 09 end -- */}
        </>
    );
};

export default CourseStyleNine;