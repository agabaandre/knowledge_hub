"use client"
import GetRating from '@/components/common/GetRating';
import coursesData from '@/data/courses/courses-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const MyCoursesSection = () => {
    return (
        <>
            {/* -- course area start -- */}

            <div className="row gy-30">
                {
                    coursesData.slice(0, 4).map((item) => (
                        <div className="col-xl-6 col-lg-12 col-md-6" key={item.id}>
                            <div className="bd-course-wrapper style-seven">
                                <Link href={`/courses/course-details/${item.id}`} className="bd-course-thumb-wrapper bd-course-thumb-style p-relative">
                                    <div className="bd-course-badge">
                                        <span className={`bd-badge ${item.badgeClass}`}>{item.badge}</span>
                                    </div>
                                    <div className={`bd-course-thumb-bg ${item.imageClassName}`}><Image src={item.image} style={{ width: "100%", height: "auto" }} alt="images" /></div>
                                    <div className={`bd-course-thumb-instructor ${item.instructorImageClassName}`}>
                                        {
                                            item.instructorImage && <Image src={item.instructorImage} style={{ width: "100%", height: "auto" }} alt="instructor" />
                                        }
                                    </div>
                                    {
                                        item.courseTextContent == true ?
                                            <>
                                                <div className="bd-course-text-content">
                                                    <div className="text-1 fs-50 mb--5 text-white">{item.courseName}</div>
                                                    <div className={`text-2 fs-50 ${item.spacingClass} text-white`}>{item.smallText}</div>
                                                    <div className="text-3 fs-28 white-bg text-primary pl-10 pr-10 pt--5 pb--5 d-inline-block latter-sp-2 uppercase">
                                                        {item.courseTag}</div>
                                                </div>
                                            </>
                                            :
                                            <>
                                                <div className="small-text bg-text-color">{item.smallText}</div>
                                                <div className={`bd-course-overly-title ${item.FontSizeClass} ${item.courseTitleClass}`}>{item.courseName}</div>
                                            </>
                                    }
                                </Link>
                                <div className="bd-course-content">
                                    <div className="bd-course-content-bottom d-flex flex-wrap align-items-center mb-10">
                                        <div className="bd-course-lesson has-separator">
                                            <span><i className="fa-light fa-clock"></i> {item.lessons} Lessons</span>
                                        </div>
                                        <div className="bd-course-lesson">
                                            <span><i className="fa-sharp fa-solid fa-list"></i> {item.students} Students</span>
                                        </div>
                                    </div>
                                    <h5 className="bd-course-title underline mb-10"><Link href={`/courses/course-details/${item.id}`}>{item.title}</Link></h5>
                                    <p className="bd-course-description mb-10">{item.courseDescription}</p>
                                    <div className="bd-course-rating d-inline-flex align-items-center gap-10 mb-30">
                                        <div className="bd-course-rating-icon d-flex rating-color">
                                            <GetRating averageRating={item.rating} />
                                        </div>
                                        <div className="bd-course-rating-text">
                                            <span>( {item.rating}/5 Ratings )</span>
                                        </div>
                                    </div>
                                    <div className="bd-course-btn">
                                        <Link className="bd-btn btn-outline-primary" href={`/courses/course-details/${item.id}`}>Enroll Now</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))
                }
            </div>
            {/* -- course area end -- */}
        </>
    );
};

export default MyCoursesSection;