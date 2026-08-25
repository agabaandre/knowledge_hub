import Link from 'next/link';
import React from 'react';

const CommonCourseFeature = () => {
    return (
        <>
            {/* Course Features List */}
            <div className="bd-course-sidebar-widget-list mb-20">
                <h6 className="mb-15">This course includes:</h6>
                <ul>
                    {[
                        { icon: "fas fa-filter", label: "Level", value: "Beginners" },
                        { icon: "fas fa-desktop", label: "Lectures", value: "8 Lectures" },
                        { icon: "far fa-clock", label: "Duration", value: "1h 30m 12s" },
                        { icon: "fas fa-th-list", label: "Category", value: "Data Science" },
                        { icon: "fas fa-globe", label: "Language", value: "English" },
                        { icon: "fas fa-bookmark", label: "Access", value: "Full Lifetime" },
                        { icon: "fas fa-award", label: "Certificate", value: "Yes" },
                        { icon: "fas fa-file-alt", label: "Resource", value: "5 Downloadable Files" }
                    ].map((item, index) => (
                        <li key={index} className="d-flex-between">
                            <div className="icon">
                                <i className={item.icon}></i>
                                <span>{item.label}</span>
                            </div>
                            <div className="video-course-info">
                                <span>{item.value}</span>
                            </div>
                        </li>
                    ))}
                </ul>
            </div>

            {/* Social Share Section */}
            <div className="bd-course-sidebar-widget-share mb-20">
                <div className="bd-post-share">
                    <span className="b2 fw-5 mb-15 d-block">Share Now:</span>
                    <div className="theme-social circle">
                        <ul className="social-icon-list">
                            <li><Link href="https://www.facebook.com/" target="_blank"><i className="fa-brands fa-facebook-f"></i></Link></li>
                            <li><Link href="https://x.com/" target="_blank"><i className="fa-brands fa-x-twitter"></i></Link></li>
                            <li><Link href="https://www.linkedin.com" target="_blank"><i className="fa-brands fa-linkedin-in"></i></Link></li>
                            <li><Link href="https://www.instagram.com/" target="_blank"><i className="fa-brands fa-instagram"></i></Link></li>
                            <li><Link href="https://www.google.co.uk/" target="_blank"><i className="fa-brands fa-google"></i></Link></li>
                        </ul>
                    </div>
                </div>
            </div>

            {/* Gift Course Button */}
            <div className="bd-course-sidebar-widget-btn">
                <Link href="/cart" className="bd-btn btn-primary w-100">
                    <span className="left-icon"><i className="fa-sharp fa-light fa-gift"></i></span>
                    Gift this Course
                </Link>
            </div>
        </>
    );
};

export default CommonCourseFeature;