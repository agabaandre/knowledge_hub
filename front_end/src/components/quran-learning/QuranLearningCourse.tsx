import coursesData from '@/data/courses/courses-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const QuranLearningCourse = () => {
    return (
        <>
            {/* -- courses area start -- */}
            <section className="bd-courses-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-7">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Popular Courses</span>
                                <h2 className="bd-section-title">Our Advanced Quran Learning Courses</h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            coursesData.slice(83, 86).map((item) => (
                                <div className="col-xl-4 col-lg-4 col-md-6" key={item.id}>
                                    <div className="bd-course-wrapper style-ten">
                                        <div className="bd-course-thumb-wrapper p-relative">
                                            <div className="bd-course-thumb">
                                                <Link href="courses/course-details"><Image src={item.image} alt="images" /></Link>
                                            </div>
                                            <div className="bd-course-badge">
                                                <span className="bd-circle-badge white">${item.price}</span>
                                            </div>
                                        </div>
                                        <div className="bd-course-content primary-bg">
                                            <Link className={`bd-badge ${`${item.badgeClass}`} mb-10`} href="#">{item.badge}</Link>
                                            <h5 className="bd-course-title underline mb-10"><Link href="courses/course-details">{item.title}</Link></h5>
                                            <p className="bd-course-description">{item.courseDescription}</p>
                                            <div className="bd-course-content-bottom d-flex align-items-center">
                                                <div className="bd-course-lesson has-separator">
                                                    <span><i className="fa-light fa-clock"></i> {item.lessons} Lessons</span>
                                                </div>
                                                <div className="bd-course-lesson">
                                                    <span><i className="fa-sharp fa-solid fa-list"></i> {item.students} Students</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        }
                        <div className="bd-course-btn d-flex-center mt-50">
                            <Link className="bd-btn btn-outline-border-primary" href="/courses-grid">See More Courses</Link>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- courses area start -- */}
        </>
    );
};

export default QuranLearningCourse;