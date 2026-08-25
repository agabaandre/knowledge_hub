import RenderTextContentThree from '@/components/courses-inner-pages/courses-grid-four/RenderTextContentThree';
import coursesData from '@/data/courses/courses-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const InstructorCoursesMain = () => {
    return (
        <>
                <div className="col-xl-9 col-lg-9 col-md-8">
                    <div className="bd-dashboard-inner">
                        <div className="dashboard-enrolled-courses">
                            <div className="bd-dashboard-title-inner">
                                <h4 className="bd-dashboard-title">My Courses</h4>
                            </div>
                            <div className="row g-30">
                                {
                                    coursesData.slice(59, 65).map((course) => (
                                        <div className="col-xl-6 col-lg-6 col-md-12" key={course.id}>
                                            <div className="bd-course-wrapper style-ten">
                                                <div className="bd-course-thumb-wrapper bd-course-thumb-style p-relative">
                                                    <div className="bd-course-badge">
                                                        <span className={`bd-circle-badge ${course.badgeClassTwo}`}>{course.badge}</span>
                                                    </div>
                                                    <div className={`bd-course-thumb-bg ${course.imageClassName}`}><Image src={course.image} alt="images" /></div>
                                                    <div className={`bd-course-thumb-instructor ${course.instructorImageClassName}`}>{course.instructorImage && <Image src={course.instructorImage} alt="instructor" />}</div>
                                                    {RenderTextContentThree(course)}
                                                </div>
                                                <div className="bd-course-content primary-bg">
                                                    <Link className={`bd-badge ${course.badgeClass} mb-10`} href="#">{course.smallTextTwo}</Link>
                                                    <h5 className="bd-course-title underline mb-10"><Link href={`/courses/course-details/${course.id}`}>{course.title}</Link></h5>
                                                    <p className="bd-course-description">{course.courseDescription}</p>
                                                    <div className="bd-course-content-bottom d-flex align-items-center">
                                                        <div className="bd-course-lesson has-separator">
                                                            <span><i className="fa-light fa-clock"></i> {course.lessons} Lessons</span>
                                                        </div>
                                                        <div className="bd-course-lesson">
                                                            <span><i className="fa-sharp fa-solid fa-list"></i> {course.students}{" "}
                                                                Students</span>
                                                        </div>
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

export default InstructorCoursesMain;