import React from 'react';
import Avatar from '../../../../public/assets/images/avatar/avatar.webp';
import Image from 'next/image';
import Link from 'next/link';
import elementCoursesData from '@/data/elements/element-course-data';


const CourseStyleOne = () => {
    return (
        <>
            {/* -- course style 01 start -- */}
            <section className="bd-elements-course section-space">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Course Style 01</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {elementCoursesData.slice(0, 4).map((course, index) => (
                            <div key={index} className="col-xl-3 col-lg-4 col-md-6">
                                <div className="bd-course-wrapper style-one">
                                    <div className="p-relative">
                                        <div className="bd-course-thumb">
                                            <Link href="#">
                                                <Image src={course.image} alt={`course-thumb-${index + 1}`} />
                                            </Link>
                                        </div>
                                        <div className="bd-course-meta d-flex align-items-center justify-content-between">
                                            <button className="bd-course-favorite bd-course-like">
                                                <i className="icon-heart"></i>
                                            </button>
                                            <div className="bd-course-tag">
                                                <span><Link href="#">Learn PHP</Link></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="bd-course-content">
                                        <div className="bd-course-rating d-inline-flex align-items-center gap-10 mb-10">
                                            <div className="bd-course-rating-icon fs-14 d-flex rating-color">
                                                <i className="fa-solid fa-star"></i>
                                                <i className="fa-solid fa-star"></i>
                                                <i className="fa-solid fa-star"></i>
                                                <i className="fa-solid fa-star"></i>
                                                <i className="fa-solid fa-star"></i>
                                            </div>
                                            <div className="bd-course-rating-text">
                                                <span>{course.rating} (313 Ratings)</span>
                                            </div>
                                        </div>
                                        <h5 className="bd-course-title underline mb-10">
                                            <Link href="#">{course.title}</Link>
                                        </h5>
                                        <div className="bd-course-price">
                                            <span className="current-price">{`$${course.price}.00`}</span>
                                            {course.oldPrice && <span className="old-price">{`$${course.oldPrice}.00`}</span>}
                                        </div>
                                        <div className="bd-course-divider"></div>
                                        <div className="bd-course-meta d-flex-between">
                                            <div className="bd-course-author">
                                                <div className="thumb">
                                                    <Image src={Avatar} alt="author" />
                                                </div>
                                                <div className="name">
                                                    <Link href="#">{course.authorName}</Link>
                                                </div>
                                            </div>
                                            <div className="bd-course-lesson">
                                                <span><i className="fa-light fa-book"></i> {course.lessons} Lessons</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
            {/* -- course style 01 end -- */}
        </>
    );
};

export default CourseStyleOne;
