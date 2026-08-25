"use client";
import React from "react";
import UserSettingsDropdown from "./UserSettingsDropdown";
import ChangePasswordForm from "@/form/dashboard/student/change-password";

const StudentChangePassword = () => {

    return (
        <div className="col-xl-9 col-lg-9 col-md-8">
            <div className="bd-dashboard-inner">
                <div className="bd-dashboard-title-inner">
                    <div className="d-flex justify-content-between">
                        <h4 className="bd-dashboard-title">Change Password</h4>
                        <UserSettingsDropdown />
                    </div>
                </div>
                <div className="dashboard-profile-info">
                    <div className="dashboard-profile-inner">
                        <ChangePasswordForm />
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentChangePassword;
