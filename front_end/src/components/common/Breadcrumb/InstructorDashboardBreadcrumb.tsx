import React from 'react';
import rangBadge from "../../../../public/assets/images/shape/rank-badge.webp";
import avatarImg from "../../../../public/assets/images/avatar/avatar2.webp";
import profileBg from "../../../../public/assets/images/bg/profile-bg.webp";

import Image from 'next/image';
import Link from 'next/link';

const InstructorDashboardBreadcrumb = () => {
    return (
        <>
            {/* -- dashboard breadcrumb start -- */}
            <div className="bd-dashboard-breadcrumb section-space-small-top">
                <div className="container custom-container">
                    <div className="row">
                        <div className="col-xl-12">
                            <div className="bd-dashboard-breadcrumb-wrapper p-relative">
                                <div className="bd-dashboard-breadcrumb-bg image-bg" style={{ backgroundImage: `url(${profileBg.src})` }}>
                                </div>
                                <div className="bd-dashboard-profile">
                                    <div className="bd-dashboard-profile-user">
                                        <div className="bd-rank-badge">
                                            <Image src={rangBadge} alt="image" />
                                            <div className="badge-content">
                                                <span className="number"><span className="sub">#</span>1</span>
                                                <span className="icon">Rank</span>
                                            </div>
                                        </div>
                                        <div className="thumb"><Image src={avatarImg} alt="image" /></div>
                                        <div className="content">
                                            <h3 className="name">Stefan Sagmeister</h3>
                                            <span className="designation d-block">Graphic Designers</span>
                                            <div className="bd-course-rating d-inline-flex align-items-center gap-10">
                                                <div className="bd-course-rating-icon fs-14 d-flex rating-color">
                                                    <i className="fa-solid fa-star"></i>
                                                    <i className="fa-solid fa-star"></i>
                                                    <i className="fa-solid fa-star"></i>
                                                    <i className="fa-solid fa-star"></i>
                                                    <i className="fa-solid fa-star"></i>
                                                </div>
                                                <div className="bd-course-rating-text">
                                                    <span>4.8 (313 Ratings)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="bd-dashboard-profile-btn">
                                        <Link href="/create-course" className="bd-btn btn-secondary-white">Create a New Course</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* -- dashboard breadcrumb end -- */}
        </>
    );
};

export default InstructorDashboardBreadcrumb;