import elementCoursesData from '@/data/elements/element-course-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const CourseStyleEight = () => {
    return (
        <>
            {/* -- course style 08 start -- */}
            <section className="bd-elements-course section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Course Style 08</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            elementCoursesData.slice(14, 17).map((course,index) => (
                                <div className="col-xl-4 col-lg-6 col-md-6" key={index}>
                                    <div className="bd-course-wrapper style-seven">
                                        <div className="bd-course-thumb-wrapper bd-course-thumb-style p-relative">
                                            <div className="bd-course-badge">
                                                <Link className="bd-badge badge-secondary" href="#">{course.badgeText}</Link>
                                            </div>
                                            <div className={`bd-course-thumb-bg ${course.imageBgClass}`}>{course.bgImage && <Image src={course.bgImage} alt="images" />}</div>
                                            <div className={`bd-course-thumb-instructor ${course.instructorImagePosition}`}><Image style={{width:"100%", height:"auto"}} src={course.image} alt="instructor" /></div>
                                            {
                                                course.courseTextContent == true ?
                                                    <>
                                                        <div className="bd-course-text-content">
                                                            <div className="text-1 fs-50 mb--5 text-white">{course.level}</div>
                                                            <div className={`text-2 fs-50 ${course.spacingClass} text-white`}>{course.lavelTwo}</div>
                                                            <div className="text-3 fs-28 white-bg text-primary pl-10 pr-10 pt--5 pb--5 d-inline-block latter-sp-2 uppercase">
                                                                {course.lavelThree}</div>
                                                        </div>
                                                    </>
                                                    :
                                                    <>
                                                        <div className="small-text bg-text-color">{course.level}</div>
                                                        <div className={`bd-course-overly-title ${course.FontSizeClass} ${course.courseTitleColor}`}>{course.lavelTwo}</div>
                                                    </>
                                            }
                                        </div>
                                        <div className="bd-course-content">
                                            <div className="bd-course-content-bottom d-flex flex-wrap align-items-center mb-10">
                                                <div className="bd-course-lesson has-separator">
                                                    <span><i className="fa-light fa-clock"></i> {course.lessons} Lessons</span>
                                                </div>
                                                <div className="bd-course-lesson">
                                                    <span><i className="fa-sharp fa-solid fa-list"></i> {course.lessons} Students</span>
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
                                                    <i className="fa-solid fa-star-half"></i>
                                                </div>
                                                <div className="bd-course-rating-text">
                                                    <span>( {course.rating}/5 Ratings )</span>
                                                </div>
                                            </div>
                                            <div className="bd-course-btn">
                                                <Link className="bd-btn btn-outline-primary" href="#">Enroll Now</Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- course style 08 end -- */}
        </>
    );
};

export default CourseStyleEight;