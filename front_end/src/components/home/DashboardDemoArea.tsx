import React from 'react';
import dashboardDemoImg1 from '../../../public/assets/images/landing-page/dashboard-demo.png';
import dashboardDemoImg2 from '../../../public/assets/images/landing-page/dashboard-demo-2.png';
import Image from 'next/image';
import Link from 'next/link';

const DashboardDemoArea = () => {
    return (
        <>
        {/* -- Dashboard Demo Area Start -- */}
        <section className="demo-dashboard-area section-space primary-bg">
            <div className="container">
                <div className="row gy-30 align-items-center justify-content-between">
                    <div className="col-xl-7 col-lg-6 order-xl-0 order-1">
                        <div className="demo-dashboard-thumb-wrap p-relative text-xl-start text-center">
                            <div className="demo-dashboard-thumb">
                                <Image src={dashboardDemoImg1} style={{width:"auto%", height:"auto"}} alt="dashboard-demo"/>
                            </div>
                            <div className="demo-dashboard-thumb-card">
                                <Image src={dashboardDemoImg2} style={{width:"auto", height:"auto"}} alt="dashboard-demo"/>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-5 col-lg-6 order-xl-1 order-0">
                        <div className="bd-section-title-wrapper">
                            <span className="bd-section-subtitle text-secondary">Comprehensive Dashboards</span>
                            <h2 className="bd-section-title mb-20">Streamline Your Learning Experience</h2>
                            <p className="bd-section-paragraph">
                                Dive into our feature-rich dashboards, crafted for both instructors and students.
                                With over 34+ essential pages, our dashboards provide all the tools you need to
                                effectively manage and enhance your educational journey.
                            </p>
                            <div className="demo-course-btn">
                                <Link className="bd-btn btn-primary" href="/instructor-dashboard">Explore Dashboards</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {/* -- Dashboard Demo Area End -- */}
        </>
    );
};

export default DashboardDemoArea;