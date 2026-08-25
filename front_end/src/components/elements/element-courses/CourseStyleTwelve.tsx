import React from 'react';
import Image from 'next/image';

import Link from 'next/link';
import elementCoursesData from '@/data/elements/element-course-data';

const CourseStyleTwelve: React.FC = () => {
    return (
        <section className="bd-elements-course section-space-bottom">
            <div className="container">
                <div className="row align-items-center justify-content-center">
                    <div className="col-lg-12">
                        <div className="bd-elements-section-wrapper section-title-space text-center">
                            <div className="bd-elements-line">
                                <div className="bd-separator-line line-left"></div>
                                <div className="bd-elements-title-wrapper">
                                    <h2 className="bd-elements-title small">Course Style 12</h2>
                                </div>
                                <div className="bd-separator-line line-right"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="row gy-30">
                    {elementCoursesData.slice(23, 26).map((course, index) => (
                        <div key={index} className="col-xl-4 col-lg-6 col-md-6">
                            <div className="bd-course-wrapper style-ten">
                                <div className="bd-course-thumb-wrapper bd-course-thumb-style p-relative">
                                    <div className="bd-course-badge">
                                        {typeof course.price === "string" ? (
                                            <Link href="#" className="bd-badge badge-secondary"> {course.price} </Link>)
                                            :
                                            (
                                                <span className="bd-circle-badge warning">{`$${course.price}`}</span>
                                            )}
                                    </div>
                                    <div className={`bd-course-thumb-bg ${course.imageBgClass}`}>
                                        {course.bgImage && <Image src={course.bgImage} alt={course.title} layout="responsive" />}
                                    </div>
                                    <div className={`bd-course-thumb-instructor ${course.instructorImagePosition}`}>
                                        <Image style={{width:"100%", height:"auto"}} src={course.image} alt="Instructor" />
                                    </div>
                                    {
                                        course.courseTextContent == true ?
                                            <>
                                                {course.isCourseThreeStyled == true ?
                                                    <>
                                                        <div className="bd-course-text-content">
                                                            <div className="text-1 fs-50 text-white">{course.level}</div>
                                                            <div className={`text-1 fs-50 ${course.spacingClass} text-white`}>{course.lavelTwo}</div>
                                                            <div className="text-1 fs-50 text-white">{course.lavelThree}</div>
                                                        </div>
                                                    </>
                                                    :
                                                    <>
                                                        <div className="bd-course-text-content">
                                                            <div className="text-1 fs-50 mb--5 text-white">{course.level}</div>
                                                            <div className={`text-2 fs-50 ${course.spacingClass} text-white`}>{course.lavelTwo}</div>
                                                            <div className="text fs-28 white-bg text-primary pl-10 pr-10 pt--5 pb--5 d-inline-block">
                                                                {course.lavelThree}
                                                            </div>
                                                        </div>
                                                    </>
                                                }
                                            </>
                                            :
                                            <>
                                                <div className="small-text bg-text-color">{course.level}</div>
                                                <div className="bd-course-overly-title fs-200 text-white">{course.lavelTwo}</div>
                                            </>
                                    }
                                </div>
                                <div className="bd-course-content primary-bg">
                                    <Link className="bd-badge badge-warning mb-10" href="#">{course.badgeText}</Link>
                                    <h5 className="bd-course-title underline mb-10">
                                        <Link href="#">{course.title}</Link>
                                    </h5>
                                    <p className="bd-course-description">{course.courseDescription}</p>
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

export default CourseStyleTwelve;
