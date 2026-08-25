import StudentDashboardAnalytics from '@/components/dashboard/student/analytics/StudentDashboardAnalytics';
import Wrapper from '@/layout/DefaultWrapper';
import StudentDashboardLayout from '@/layout/StudentDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Student Analytics - Education & Online Courses React NextJs Template",
};

const StudentAnalytics = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <StudentDashboardLayout>
                    <StudentDashboardAnalytics />
                    </StudentDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default StudentAnalytics;