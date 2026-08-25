
import React from 'react';
import OnlineCoursesSvg from '@/svg/OnlineCoursesSvg';
import InstructorSvg from '@/svg/InstructorSvg';
import LearnSvg from '@/svg/LearnSvg';

const OverviewArea = () => {
    return (
        <>
            {/* -- overview area start -- */}
            <div className="bd-overview-area theme-bg section-space-small">
                <div className="container">
                    <div className="bd-overview-box">
                        <div className="bd-overview-wrapper">
                            <div className="icon">
                                <OnlineCoursesSvg />
                            </div>
                            <h6 className="title fw-6 text-white">Explore 100,000 <br /> online courses</h6>
                        </div>
                        <div className="bd-overview-wrapper">
                            <div className="icon">
                                <InstructorSvg />
                            </div>
                            <h6 className="title fw-6 text-white">Find the right <br /> instructor for you</h6>
                        </div>
                        <div className="bd-overview-wrapper">
                            <div className="icon">
                                <LearnSvg />
                            </div>
                            <h6 className="title fw-6 text-white">Lifetime access Learn <br /> on your schedule</h6>
                        </div>
                    </div>
                </div>
            </div>
            {/* -- overview area end -- */}
        </>
    );
};

export default OverviewArea;