import { ICourseProps } from '@/interFace/interFace';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import RenderTextContentFour from '../../courses-inner-pages/course-grid-right/RenderTextContentFour';
import GetRating from '../GetRating';

const CommonCourseSingleCardTwo = ({ course }: ICourseProps) => {
    return (
        <>
            {
                <div className="bd-course-wrapper style-two">
                    <div className="bd-course-thumb-wrapper bd-course-thumb-style p-relative">
                        {
                            course.badge &&
                            <div className="bd-course-badge">
                                <Link className={`bd-badge ${course.badgeClass}`} href="#">{course.badge}</Link>
                            </div>
                        }
                        <div className={`bd-course-thumb-bg ${course.imageClassName}`}><Image src={course.image} alt="images" /></div>
                        <div className={`bd-course-thumb-instructor ${course.instructorImageClassName}`}>{course.instructorImage && <Image src={course.instructorImage} alt="instructor" />}</div>
                        {RenderTextContentFour(course)}
                    </div>
                    <div className="bd-course-content">
                        <div className="bd-course-meta d-flex-between mb-15">
                            <div className="bd-course-tag">
                                <Link className="bd-badge badge-outline-light badge-transparent" href="#">{course.courseTagTwo}</Link>
                            </div>
                            <div className="bd-course-lesson">
                                <span><i className="fa-light fa-book"></i> {course.lessons} Lessons</span>
                            </div>
                        </div>
                        <h5 className="bd-course-title underline mb-10"><Link href={`/courses/course-details/${course.id}`}>{course.title}</Link></h5>
                        <div className="bd-course-rating d-flex-between">
                            <div className="bd-course-price">
                                <span className="current-price">{`$${course.price}.00`}</span>
                                {course.discount && <span className="old-price">{`$${course.discount}.00`}</span>}
                            </div>
                            <div className="bd-course-rating-wrap d-flex align-items-center gap-10">
                                <div className="bd-course-rating-icon fs-14 d-flex rating-color">
                                    <GetRating averageRating={course.rating} />
                                </div>
                                <div className="bd-course-rating-text">
                                    <span>{course.rating} ({course.ratingNum})</span>
                                </div>
                            </div>
                        </div>
                        <div className="bd-course-full-border"></div>
                        <div className="btn-wrap d-flex flex-wrap align-items-center justify-content-between">
                            <div className="bd-course-author">
                                <div className="thumb">{course.avatarImg && <Image src={course.avatarImg} alt="author" />}</div>
                                <div className="name"><Link href="/instructor/instructor-details">{course.instructorName}</Link></div>
                            </div>
                            <div className="bd-course-btn">
                                <Link className="bd-text-btn" href={`/courses/course-details/${course.id}`}>Enroll Now
                                    <span className="box-icon">
                                        <i className="fa-regular fa-arrow-right-long first-icon"></i>
                                        <i className="fa-regular fa-arrow-right-long second-icon"></i>
                                    </span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            }
        </>
    );
};

export default CommonCourseSingleCardTwo;