import elementCoursesData from '@/data/elements/element-course-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const CourseStyleFive = () => {

    return (
        <>
            {/* -- course style 05 start -- */}
            <section className="bd-elements-course section-space">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Course Style 05</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>

                    <div className="row gy-30">
                        {
                            elementCoursesData.slice(11, 14).map((item,index) => (
                                <div className="col-xl-4 col-lg-6 col-md-6" key={index}>
                                    <div className="bd-course-wrapper style-five">
                                        <div className="bd-course-info">
                                            <div className="bd-course-info-wrapper">
                                                <Link className="bd-badge badge-primary mb-15" href="#">{item.certificateBadge}</Link>
                                                <h5 className="bd-course-title underline mb-10"><Link href="#">{item.title}</Link></h5>
                                                <div className="bd-course-info-label mb-15">Level : <span>{item.level}</span></div>
                                                <p>{item.details}</p>
                                                <div className="bd-course-info-list">
                                                    <ul>
                                                        {item.courseList?.map((topic, index) => (
                                                            <li key={index}>
                                                                <i className="far fa-check"></i> {topic}
                                                            </li>
                                                        ))}
                                                    </ul>
                                                </div>
                                                <div className="bd-course-price mb-20">
                                                    <span className="current-price has-big">{`$${item.price}.00`}</span>
                                                    <span className="old-price has-big">{`$${item.oldPrice}.00`}</span>
                                                </div>
                                                <div className="bd-course-action-btn d-flex align-items-center gap-15">
                                                    <Link href="#" className="bd-btn btn-outline-border-primary">View Details</Link>
                                                    <button className="bd-btn-icon btn-warning outline-warning"><i
                                                        className="icon-heart"></i></button>
                                                    <Link href="#" className="bd-btn-icon btn-info outline-info"><i
                                                        className="fa-light fa-share"></i></Link>
                                                </div>
                                            </div>
                                        </div>
                                        <Link href="#" className="bd-course-thumb-wrapper bd-course-thumb-style p-relative">
                                            <div className="bd-course-badge">
                                                <span className={`bd-badge ${item.badgeColor}`}>{item.badgeText}</span>
                                            </div>
                                            <div className={`bd-course-thumb-bg ${item.imageBgClass}`}>{item.bgImage && <Image src={item.bgImage} style={{ width: "100%", height: "auto" }} alt="images" />}</div>
                                            <div className={`bd-course-thumb-instructor ${item.instructorImagePosition}`}>
                                                {
                                                    item.image && <Image src={item.image}  alt="instructor" />
                                                }
                                            </div>
                                            {
                                                item.courseTextContent == true ?
                                                    <>
                                                        <div className="bd-course-text-content">
                                                            <div className="text-1 fs-50 mb--5 text-white">{item.level}</div>
                                                            <div className={`text-2 fs-50 ${item.spacingClass} text-white`}>{item.lavelTwo}</div>
                                                            <div className="text-3 fs-28 white-bg text-primary pl-10 pr-10 pt--5 pb--5 d-inline-block latter-sp-2 uppercase">
                                                                {item.lavelThree}</div>
                                                        </div>
                                                    </>
                                                    :
                                                    <>
                                                        <div className="small-text bg-text-color">{item.level}</div>
                                                        <div className={`bd-course-overly-title ${item.FontSizeClass} ${item.courseTitleColor}`}>{item.lavelTwo}</div>
                                                    </>
                                            }
                                        </Link>
                                        <div className="bd-course-content ">
                                            <div className="bd-course-heading d-flex-between mb-15">
                                                <Link className="bd-badge badge-primary" href="#">istudy Badge</Link>
                                                <div className="bd-course-rating-wrap d-flex align-items-center gap-10">
                                                    <div className="bd-course-rating-icon fs-14 d-flex rating-color">
                                                        <i className="fa-solid fa-star"></i>
                                                    </div>
                                                    <div className="bd-course-rating-text">
                                                        <span>4.8 (258)</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="bd-course-text mb-10">
                                                <h5 className="bd-course-title underline"><Link href="#">{item.title}</Link>
                                                </h5>
                                            </div>
                                            <div className="bd-course-meta">
                                                <div className="bd-course-price mb-15">
                                                    <span className="current-price has-big text-primary">{`$${item.price}.00`}</span>
                                                    <span className="old-price has-big">{`$${item.oldPrice}.00`}</span>
                                                </div>
                                                <div className="d-flex-between">
                                                    <div className="bd-course-author">
                                                        <div className="thumb">{item.authorImage && <Image src={item.authorImage} alt="author" />}</div>
                                                        <div className="name"><Link href="#">{item.authorName}</Link></div>
                                                    </div>
                                                    <div className="bd-course-student">
                                                        <span><i className="fa-light fa-users"></i> {item.students}+ Students</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="bd-course-full-border"></div>
                                            <div className="bd-course-footer d-flex-between border-pile">
                                                <div className="bd-course-lesson">
                                                    <span><i className="icon-books-pile"></i> {item.lessons} Lessons</span>
                                                </div>
                                                <div className="bd-course-btn">
                                                    <Link className="bd-text-btn" href="#">Enroll Now
                                                        <span className="box-icon">
                                                            <i className="fa-regular fa-arrow-right-long first-icon"></i>
                                                            <i className="fa-regular fa-arrow-right-long second-icon"></i>
                                                        </span>
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- course style 05 end -- */}
        </>
    );
};

export default CourseStyleFive;