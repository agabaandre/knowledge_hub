import StudentSocialProfileMain from '@/components/dashboard/student/student-settings/StudentSocialProfileMain';
import Wrapper from '@/layout/DefaultWrapper';
import StudentDashboardLayout from '@/layout/StudentDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Student Social Profile - Education & Online Courses React NextJs Template",
};

const StudentSocialProfile = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <StudentDashboardLayout>
                    <StudentSocialProfileMain />
                    </StudentDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default StudentSocialProfile;