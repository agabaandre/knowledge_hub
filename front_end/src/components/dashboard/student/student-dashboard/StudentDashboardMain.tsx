import Link from 'next/link';
import React from 'react';
import StudentProgressCounter from './StudentProgressCounter';
import EnrolledCoursesTable from './EnrolledCoursesTable';


const StudentDashboardMain = () => {
    return (
        <>
            <div className="col-xl-9 col-lg-9 col-md-8">
                <div className="bd-dashboard-inner">
                    <div className="bd-dashboard-status-box mb-30">
                        <div className="bd-dashboard-title-inner">
                            <h4 className="bd-dashboard-title">My Status</h4>
                        </div>
                        <div className="container p-0">
                            <div className="row gy-30 justify-content-center">
                                <StudentProgressCounter />
                            </div>
                        </div>
                    </div>
                    <div className="bd-dashboard-course-area">
                        <div className="bd-dashboard-title-inner">
                            <h4 className="bd-dashboard-title">My Enrolled Courses</h4>
                        </div>
                        <EnrolledCoursesTable />
                    </div>
                    <div className="bd-more-button text-center mt-30">
                        <Link href="/courses-list-one" className="bd-btn btn-primary">Browse All Course</Link>
                    </div>
                </div>
            </div>
        </>
    );
};

export default StudentDashboardMain;