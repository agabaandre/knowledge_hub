"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import coursesData from '@/data/courses/courses-data';
import Image from 'next/image';
import React from 'react';
import avatarImg from '../../../../../public/assets/images/avatar/avatar.webp';
import Link from 'next/link';
import CourseWillYouLearn from './CourseWillYouLearn';
import CourseRequirements from './CourseRequirements';
import CourseCurriculum from './CourseCurriculum';
import DetailsInstructor from './DetailsInstructor';
import StudentFeedback from './StudentFeedback';
import CourseDetailsReviews from './CourseDetalisReviews';
import CourseSidebarWidget from './CourseSidebarWidget';
import CommonReviewForm from '../../../common/course-details/CommonReviewForm';

const CoursesDetailsMain = ({ courseId }: { courseId: number }) => {
    const course = coursesData.find((item) => item.id == courseId);

    return (
        <>
            <Breadcrumbs breadcrumbTitle='Course Details 01' />
            {/* -- course details area start -- */}
            <section className="bd-course-details-area bd-course-details-top section-space-bottom">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-xxl-8 col-xl-8 col-lg-8">
                            <div className="bd-course-details-wrapper mb-30">
                                <div className="bd-course-details-heading mb-30">
                                    <h2 className="bd-course-details-title mb--5">{course?.title}: Beginner to Advanced</h2>
                                    <div className="bd-course-details-rating rating-spacing">
                                        <ul>
                                            <li><i className="fas fa-star"></i></li>
                                            <li><i className="fas fa-star"></i></li>
                                            <li><i className="fas fa-star"></i></li>
                                            <li><i className="fas fa-star"></i></li>
                                            <li><i className="fal fa-star"></i></li>
                                        </ul>
                                        <span>(1,230 reviews)</span>
                                    </div>
                                </div>
                                <div className="bd-course-details-meta mb-30">
                                    <div className="bd-course-author border-line-meta">
                                        <div className="thumb"><Link href="#">{course?.avatarImg ? <Image src={course?.avatarImg} alt="author" /> : <Image src={avatarImg} alt="author" />}</Link>
                                        </div>
                                        <div className="authour-meta">
                                            <span className="subtitle">Created by</span>
                                            <div className="name"><Link href="/instructor/instructor-details">{course?.instructorName ? course?.instructorName : "John Doe"}</Link></div>
                                        </div>
                                    </div>
                                    <div className="bd-course-details-meta-item border-line-meta">
                                        <p className="title">Total Enrolled</p>
                                        <span className="subtitle">12,580</span>
                                    </div>
                                    <div className="bd-course-details-meta-item border-line-meta">
                                        <p className="title">Last Update</p>
                                        <span className="subtitle">15 September 2024</span>
                                    </div>
                                    <div className="bd-course-details-meta-item">
                                        <p className="title">Category</p>
                                        <span className="subtitle"><Link href="#">Web Development</Link></span>
                                    </div>
                                </div>
                                <div className="bd-course-details-content mb-30">
                                    <h3 className="bd-course-details-content-title">Description</h3>
                                    <p className="description">This comprehensive course covers all aspects of web development from the
                                        basics of HTML, CSS, and JavaScript to advanced topics like React, Node.js, and database
                                        integration. Whether {`you're`} a complete beginner or an experienced developer looking to hone
                                        your skills, this course has everything you need to master web development. Learn best
                                        practices and gain hands-on experience with real-world projects.</p>
                                </div>
                                <CourseWillYouLearn />
                                <CourseRequirements />
                                <CourseCurriculum />
                                <DetailsInstructor />
                                <StudentFeedback />
                                <CourseDetailsReviews />
                                <CommonReviewForm/>
                            </div>
                        </div>
                        <div className="col-xxl-4 col-xl-4 col-lg-4">
                            {course ? <CourseSidebarWidget course={course} /> : <p>Course not found</p>}
                        </div>
                    </div>
                </div>
            </section>
            {/* -- course details area end -- */}
        </>
    );
};

export default CoursesDetailsMain;