
import { ICourse } from '@/interFace/interFace';
import RenderTextContent from '@/utils/RenderTextContent';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import avatarImg from '../../../../public/assets/images/avatar/avatar.webp';
interface ICourseProps {
    course: ICourse;
}
const CourseListCard = ({ course }: ICourseProps) => {
    return (
        <>
            <div className="bd-course-wrapper style-twelve" key={course.id}>
                <Link href={`/courses/course-details/${course.id}`} className="bd-course-thumb-wrapper bd-course-thumb-style p-relative">
                    {
                        course.badge &&
                        <div className="bd-course-badge">
                            <span className={`bd-badge ${course.badgeClass}`}>{course.badge}</span>
                        </div>
                    }
                    <div className={`bd-course-thumb-bg ${course.imageClassName}`}><Image src={course.image} alt="images" /></div>
                    <div className={`bd-course-thumb-instructor ${course.instructorImageClassName}`}>{course.instructorImage && <Image src={course.instructorImage} alt="instructor" />}</div>
                    {RenderTextContent(course)}
                </Link>
                <div className="bd-course-content">
                    <h5 className="bd-course-title underline mb-10"><Link href={`/courses/course-details/${course.id}`}>{course.title}</Link></h5>
                    <p className="bd-course-description">{course.courseDescription}</p>
                    <div className="d-flex align-items-center gap-30 mb-15">
                        <div className="bd-course-author">
                            <div className="thumb"><Image src={avatarImg} alt="author" /></div>
                            <div className="name"><Link href="/instructor/instructor-details">{course.instructorName}</Link></div>
                        </div>
                        <div className="bd-author-totla-course">
                            <div className="bd-course-lesson">
                                <span><i className="fa-light fa-book"></i> {course.totalCourse} Course</span>
                            </div>
                        </div>
                    </div>
                    <div className="bd-course-meta d-flex align-items-center gap-20 mb-15">
                        <div className="bd-course-lesson">
                            <span><i className="fa-light fa-clock"></i> {course.lessons} Lessons</span>
                        </div>
                        <div className="bd-course-student">
                            <span><i className="fa-sharp fa-solid fa-list"></i> {course.hoursTime} Hours</span>
                        </div>
                        <div className="bd-course-duration">
                            <span><i className="fa-sharp fa-solid fa-list"></i>{course.level}</span>
                        </div>
                    </div>
                    <div className="bd-course-price">
                        <span className="current-price text-primary">{`$${course.price}`}</span>
                        {course.discount && <span className="old-price">{`$${course.discount}`}</span>}
                    </div>
                </div>
            </div>
        </>
    );
};

export default CourseListCard;