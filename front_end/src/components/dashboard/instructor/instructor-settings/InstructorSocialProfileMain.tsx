import React from 'react';
import SettingsDropdown from './SettingsDropdown';
import InstructorSocialProfile from '@/form/dashboard/instructor/instructor-social-profile';

const InstructorSocialProfileMain = () => {
    return (
        <>
                <div className="col-xl-9 col-lg-9 col-md-8">
                    <div className="bd-dashboard-inner">
                        <div className="bd-dashboard-title-inner">
                            <div className="d-flex justify-content-between">
                                <h4 className="bd-dashboard-title">Social Profile</h4>
                                <SettingsDropdown />
                            </div>
                        </div>
                        <div className="bd-dashboard-profile-info">
                            <div className="bd-dashboard-profile-inner">
                                <InstructorSocialProfile />
                            </div>
                        </div>
                    </div>
                </div>
        </>
    );
};

export default InstructorSocialProfileMain;