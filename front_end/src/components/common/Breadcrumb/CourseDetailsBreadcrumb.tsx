import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import avatarImg from '../../../../public/assets/images/avatar/avatar.webp';

interface ICourseBreadcrumbProps {
    subtitle: string;
    description: string;
    courseBreadcrumbBuyShow?: boolean
}

const CourseDetailsBreadcrumb = ({ subtitle, description, courseBreadcrumbBuyShow }: ICourseBreadcrumbProps) => {
    return (
        <>
            {/* -- course-breadcrumb end -- */}
            <section className="bd-course-breadcrumb-area section-space">
                <div className="container">
                    <div className={`row ${courseBreadcrumbBuyShow == true ? "gy-30 align-items-center justify-content-between" : ""}`}>
                        <div className="col-xl-8 col-lg-8 col-md-12">
                            <div className="bd-course-breadcrumb-wrapper">
                                <div className="bd-course-breadcrumb-content">
                                    <div className="bd-breadcrumb-list justify-content-start mb-20">
                                        <span><Link href="/">iStudy</Link></span>
                                        <span className="divider"><i className="fa-regular fa-angle-right"></i></span>
                                        <span className="active">{subtitle}</span>
                                    </div>
                                    <h2 className="bd-course-breadcrumb-title mb-15">Graphic Design Fundamentals</h2>
                                    <p className="bd-course-breadcrumb-description"> {description}</p>
                                    <div className="bd-course-details-meta border-padding-none mb-20">
                                        <div className="bd-course-author">
                                            <div className="thumb"><Link href="#"><Image src={avatarImg} alt="author" /></Link></div>
                                            <div className="authour-meta">
                                                <span className="subtitle">Created by</span>
                                                <div className="name"><Link href="/instructor/instructor-details">Jane Smith</Link></div>
                                            </div>
                                        </div>
                                        <div className="bd-course-details-meta-item">
                                            <p className="title">Total Enrolled</p>
                                            <span className="subtitle">9,450</span>
                                        </div>
                                        <div className="bd-course-details-meta-item">
                                            <p className="title">Category</p>
                                            <span className="subtitle"><Link href="#">Graphic Design</Link></span>
                                        </div>
                                    </div>
                                    <div className="bd-course-details-language mb-20">
                                        <div className="bd-course-details-rating rating-spacing">
                                            <span className="fw-6">4.8</span>
                                            <ul>
                                                <li><i className="fas fa-star"></i></li>
                                                <li><i className="fas fa-star"></i></li>
                                                <li><i className="fas fa-star"></i></li>
                                                <li><i className="fas fa-star"></i></li>
                                                <li><i className="fal fa-star"></i></li>
                                            </ul>
                                            <span>(980 reviews)</span>
                                        </div>
                                        <div className="bd-audio-btn">
                                            <i className="fa-light fa-volume"></i>
                                            Audio: <span>English</span>
                                        </div>
                                        <div className="bd-subtitle-btn">
                                            <i className="fa-light fa-subtitles"></i>
                                            Subtitles: <span className="fw-5">15+ Languages</span>
                                        </div>
                                    </div>
                                    <div className="bd-course-add-time d-flex-items gap-30">
                                        <div className="bd-course-add-time-item">
                                            <p><span className="icon"><i className="fa-solid fa-calendar-days"></i></span> Last Update: <span
                                                className="date">10 September 2024</span></p>
                                        </div>
                                        <div className="bd-course-add-time-item">
                                            <p><span className="icon"><i className="fa-light fa-rotate"></i></span> Released: <span
                                                className="date">10 Aug 2024</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {
                            courseBreadcrumbBuyShow == true ? <>
                                <div className="col-xl-4 col-lg-5 col-md-12">
                                    <div className="bd-course-breadcrumb-buy">
                                        <div className="bd-course-sidebar-widget transparent">
                                            <div className="bd-course-sidebar-widget-price mb-20">
                                                <div className="d-flex-between">
                                                    <div className="bd-course-price">
                                                        <span className="current-price">$139.00</span>
                                                        <span className="old-price">$199.00</span>
                                                    </div>
                                                    <span className="bd-badge badge-danger">30% Off</span>
                                                </div>
                                                <div className="bd-discount-time">
                                                    <span>
                                                        <i className="fa-light fa-clock"></i>{" "}
                                                        5 Days left at this price!</span>
                                                </div>
                                            </div>
                                            <div className="bd-course-sidebar-widget-btn mb-20">
                                                <Link href="/cart" className="bd-btn btn-primary w-100 mb-15">Add to cart</Link>
                                                <Link href="/checkout" className="bd-btn btn-outline-primary w-100">Buy Now</Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </> : ""
                        }
                    </div>
                </div>
            </section>
            {/* -- course-breadcrumb start -- */}
        </>
    );
};

export default CourseDetailsBreadcrumb;