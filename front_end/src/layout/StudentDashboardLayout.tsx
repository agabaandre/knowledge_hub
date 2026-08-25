import React, { ReactNode } from 'react';
import DashboardSidebarMenu from './sidebar/DashboardSidebarMenu';
import StudentDashboardBreadcrumb from '@/components/common/Breadcrumb/StudentDashboardBreadcrumb';

interface DashboardLayoutProps {
    children: ReactNode;
}

const StudentDashboardLayout: React.FC<DashboardLayoutProps> = ({ children }) => {
    return (
        <>
            <StudentDashboardBreadcrumb />
            {/* -- Start student Dashboard Area -- */}
            <div className="bd-dashboard-area section-space-bottom">
                <div className="container">
                    <div className="bd-dashboard-main">
                        <div className="row gy-30">
                            <DashboardSidebarMenu />
                            {children}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default StudentDashboardLayout;
