import InstructorDashboardBreadcrumb from '@/components/common/Breadcrumb/InstructorDashboardBreadcrumb';
import React, { ReactNode } from 'react';
import InstructorSidebarMenu from './sidebar/InstructorSidebarMenu';

interface DashboardLayoutProps {
    children: ReactNode;
}
const InstructorDashboardLayout = ({ children }: DashboardLayoutProps) => {
    return (
        <>
            <InstructorDashboardBreadcrumb />
            <div className="bd-dashboard-area section-space-bottom">
                <div className="container">
                    <div className="bd-dashboard-main">
                        <div className="row gy-30">
                            <InstructorSidebarMenu />
                            {children}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default InstructorDashboardLayout;