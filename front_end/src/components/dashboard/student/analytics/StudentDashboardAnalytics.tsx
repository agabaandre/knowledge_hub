import React from "react";
import CoursesAnalyticsChart from "../../../common/dashboard-chart/CoursesAnalyticsChart";
import QuizMarkChart from "../../../common/dashboard-chart/QuizMarkChart";
import EarningsGraphChart from "../../../common/dashboard-chart/EarningsGraphChart";
import StudentEnrollChart from "../../../common/dashboard-chart/StudentEnrollChart";

const StudentDashboardAnalytics = () => {
    return (
        <div className="col-xl-9 col-lg-9 col-md-8">
            <div className="bd-dashboard-inner">
                <div className="dashboard-analytics-area">
                    <div className="bd-dashboard-title-inner">
                        <h4 className="bd-dashboard-title">Analytics</h4>
                    </div>
                    <div className="container p-0">
                        <div className="row">
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
                                        <h5 className="bd-dashboard-card-title">Student Graph</h5>
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
    );
};

export default StudentDashboardAnalytics;
