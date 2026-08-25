import CoursesAnalyticsChart from '@/components/common/dashboard-chart/CoursesAnalyticsChart';
import EarningsGraphChart from '@/components/common/dashboard-chart/EarningsGraphChart';
import QuizMarkChart from '@/components/common/dashboard-chart/QuizMarkChart';
import StudentEnrollChart from '@/components/common/dashboard-chart/StudentEnrollChart';
import React from 'react';

const InstructorAnalyticsMain = () => {
    return (
        <>
            <div className="col-xl-9 col-lg-9 col-md-8">
                <div className="bd-dashboard-inner">
                    <div className="dashboard-analytics-area">
                        <div className="bd-dashboard-title-inner">
                            <h4 className="bd-dashboard-title">Analytics</h4>
                        </div>
                        <div className="container p-0">
                            <div className="row gy-30">
                                <div className="col-xl-12">
                                    <div className="bd-dashboard-card-wrapper">
                                        <div className="bd-dashboardcard-header">
                                            <h5 className="bd-dashboard-card-title">Student Enrollment</h5>
                                        </div>
                                        <div className="chart-inner">
                                            <StudentEnrollChart />
                                        </div>
                                    </div>
                                </div>
                                <div className="col-xl-6 col-lg-6">
                                    <div className="bd-dashboard-card-wrapper">
                                        <div className="bd-dashboardcard-header">
                                            <h5 className="bd-dashboard-card-title">Courses Analytics</h5>
                                        </div>
                                        <div className="chart-inner d-flex justify-content-center">
                                            <CoursesAnalyticsChart />
                                        </div>
                                    </div>
                                </div>
                                <div className="col-xl-6 col-lg-6">
                                    <div className="bd-dashboard-card-wrapper">
                                        <div className="bd-dashboardcard-header">
                                            <h5 className="bd-dashboard-card-title">Quiz Mark</h5>
                                        </div>
                                        <div className="chart-inner d-flex justify-content-center">
                                            <QuizMarkChart />
                                        </div>
                                    </div>
                                </div>
                                <div className="col-xl-12">
                                    <div className="bd-dashboard-card-wrapper">
                                        <div className="bd-dashboardcard-header">
                                            <h5 className="bd-dashboard-card-title">Earnings Graph</h5>
                                        </div>
                                        <div className="chart-inner">
                                            <EarningsGraphChart />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default InstructorAnalyticsMain;