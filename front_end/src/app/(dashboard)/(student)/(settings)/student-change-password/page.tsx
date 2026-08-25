import StudentChangePassword from '@/components/dashboard/student/student-settings/StudentChangePassword';
import Wrapper from '@/layout/DefaultWrapper';
import StudentDashboardLayout from '@/layout/StudentDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Student Change Password - Education & Online Courses React NextJs Template",
};

const ChangePassword = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <StudentDashboardLayout>
                    <StudentChangePassword />
                    </StudentDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default ChangePassword;