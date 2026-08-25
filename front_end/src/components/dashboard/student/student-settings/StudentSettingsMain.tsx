"use client";
import StudentForm from "@/form/dashboard/student/student-form";
import UserSettingsDropdown from "./UserSettingsDropdown";

const StudentSettingsMain = () => {

    return (
        <div className="col-xl-9 col-lg-9 col-md-8">
            <div className="bd-dashboard-inner">
                <div className="bd-dashboard-title-inner">
                    <div className="d-flex justify-content-between">
                        <h4 className="bd-dashboard-title">Student Settings</h4>
                        <UserSettingsDropdown />
                    </div>
                </div>
                <div className="dashboard-profile-info">
                    <div className="dashboard-profile-inner">
                        <StudentForm />
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentSettingsMain;
