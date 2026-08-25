import React from 'react';
import SettingsDropdown from './SettingsDropdown';
import InstructorForm from '@/form/dashboard/instructor/instructor-form';

const InstructorSettingsMain: React.FC = () => {
    return (
            <div className="col-xl-9 col-lg-9 col-md-8">
                <div className="bd-dashboard-inner">
                    <div className="bd-dashboard-title-inner">
                        <div className="d-flex justify-content-between">
                            <h4 className="bd-dashboard-title">Instructor Settings</h4>
                            <SettingsDropdown />
                        </div>
                    </div>
                    <div className="bd-dashboard-profile-info">
                       <InstructorForm/>
                    </div>
                </div>
            </div>
    );
};

export default InstructorSettingsMain;