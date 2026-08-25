import { ICourseProps } from '@/interFace/interFace';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const CourseGridFiveCard = ({ course }: ICourseProps) => {
    return (
        <>
            <div className="col-xl-4 col-lg-4 col-md-6" >
                <div className="bd-course-wrapper style-four">
                    <div className="bd-course-thumb-wrapper p-relative mb-20">
                        <Link href={`/courses/course-details/${course.id}`}>
                            <div className="bd-course-thumb">
                                <Image src={course.image} alt="image" />
                            </div>
                            <div className="shape">{course.shape && <Image style={{ width: "auto", height: "auto" }} src={course.shape} alt="shape" />}</div>
                        </Link>
                    </div>
                    <div className="bd-course-content mb-20">
                        <h5 className="bd-course-title underline mb--5"><Link href={`/courses/course-details/${course.id}`}>{course.title}</Link></h5>
                        <p>{course.courseDescription}</p>
                    </div>
                    <div className={`bd-course-info-item-wrapper ${course.courseBgClass}`}>
                        <div className="bd-course-info-item">
                            <h5 className="bd-course-info-item-title">{course.lessons}<br /><span>Lessons</span></h5>
                        </div>
                        <div className="bd-course-info-item">
                            <h5 className="bd-course-info-item-title">{course.students}+ <br /><span>Students</span></h5>
                        </div>
                        <div className="bd-course-info-item">
                            <h5 className="bd-course-info-item-title">{course.hoursTime} <br /><span>Hours</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default CourseGridFiveCard;