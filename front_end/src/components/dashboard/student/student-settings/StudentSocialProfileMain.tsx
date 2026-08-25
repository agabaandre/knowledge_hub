import React from 'react';
import UserSettingsDropdown from './UserSettingsDropdown';
import SocialProfileForm from '@/form/dashboard/student/social-profile-form';

const StudentSocialProfileMain = () => {
    return (
        <>
            <div className="col-xl-9 col-lg-9 col-md-8">
                <div className="bd-dashboard-inner">
                    <div className="bd-dashboard-title-inner">
                        <div className="d-flex justify-content-between">
                            <h4 className="bd-dashboard-title">Social Profile</h4>
                            <UserSettingsDropdown />
                        </div>
                    </div>
                    <div className="dashboard-profile-info">
                        <div className="dashboard-profile-inner">
                            <div className="row gy-30">
                                <SocialProfileForm />
                                <div className="col-lg-12">
                                    <button className="bd-btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default StudentSocialProfileMain;