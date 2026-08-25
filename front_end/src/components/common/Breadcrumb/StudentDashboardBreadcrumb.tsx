import React from 'react';
import avaterImg from '../../../../public/assets/images/avatar/avatar7.webp';
import profileBgImg from '../../../../public/assets/images/bg/profile-bg.webp';
import Image from 'next/image';
import Link from 'next/link';

const StudentDashboardBreadcrumb = () => {
    return (
        <>
            {/* -- dashboard breadcrumb start -- */}
            <div className="bd-dashboard-breadcrumb section-space-small-top">
                <div className="container custom-container">
                    <div className="row">
                        <div className="col-xl-12">
                            <div className="bd-dashboard-breadcrumb-wrapper p-relative">
                                <div className="bd-dashboard-breadcrumb-bg image-bg" style={{ backgroundImage: `url(${profileBgImg.src})` }}>
                                </div>
                                <div className="bd-dashboard-profile">
                                    <div className="bd-dashboard-profile-user">
                                        <div className="thumb"><Image src={avaterImg} alt="image" /></div>
                                        <div className="content">
                                            <h3 className="name">Michael Smith</h3>
                                            <span className="designation d-block">Web App Development</span>
                                            <div className="bd-dashboard-profile-meta">
                                                <div className="enrolled-course" data-title="enrolled">
                                                    <span className="icon"><i className="fa-light fa-book"></i></span> 5 Courses Enrolled
                                                </div>
                                                <div className="complete-course" data-title="completed">
                                                    <span className="icon"><i className="fa-solid fa-badge-check"></i></span> 2 Courses Complete
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="bd-dashboard-profile-btn">
                                        <Link href="/become-instructor" className="bd-btn btn-secondary-white">Become Instructor</Link>
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

export default StudentDashboardBreadcrumb;