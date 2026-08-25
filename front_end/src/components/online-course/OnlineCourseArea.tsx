"use client"
import coursesData from '@/data/courses/courses-data';
import { ICourse, ProductsType } from '@/interFace/interFace';
import { wishlist_product } from '@/redux/slices/wishlistSlice';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import { useDispatch } from 'react-redux';
import GetRating from '../common/GetRating';

const OnlineCourseArea = () => {
    const dispatch = useDispatch();
    const handleAddToWishlist = (product: ProductsType) => {
        if (product) {
            dispatch(wishlist_product(product))
        }
    }
    
    return (
        <>
            {/* -- course area start -- */}
            <section className="bd-course-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-8">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Trending Courses</span>
                                <h2 className="bd-section-title">Find Your <span className="down-mark-line">Course</span></h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            coursesData.slice(0, 6).map((item:ICourse) => (
                                <div className="col-xl-4 col-lg-6 col-md-6" key={item.id}>
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
                                        <div className="bd-course-info">
                                            <div className="bd-course-info-wrapper">
                                                <Link className="bd-badge badge-primary mb-15" href="#">{item.certificateBadge}</Link>
                                                <h5 className="bd-course-title underline mb-10"><Link href={`/courses/course-details/${item.id}`}>{item.advancedTitle}</Link></h5>
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
                                                    <span className="old-price has-big">{`$${item.discount}.00`}</span>
                                                </div>
                                                <div className="bd-course-action-btn d-flex align-items-center gap-15">
                                                    <Link href={`/courses/course-details/${item.id}`} className="bd-btn btn-outline-border-primary">View Details</Link>
                                                    <button  onClick={() => handleAddToWishlist(item)} className="bd-btn-icon btn-warning outline-warning"><i
                                                        className="icon-heart"></i></button>
                                                    <Link href={`/courses/course-details/${item.id}`} className="bd-btn-icon btn-info outline-info"><i
                                                        className="fa-light fa-share"></i></Link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                    <div className="bd-course-btn d-flex-center mt-50">
                        <Link className="bd-btn btn-outline-border-primary" href="/courses-grid">See More Courses</Link>
                    </div>
                </div>
            </section>
            {/* -- course area end -- */}
        </>
    );
};

export default OnlineCourseArea;