import AnnouncementsMain from '@/components/dashboard/student/announcement/AnnouncementsMain';
import Wrapper from '@/layout/DefaultWrapper';
import StudentDashboardLayout from '@/layout/StudentDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Student Assignment - Education & Online Courses React NextJs Template",
};

const StudentAnnouncements = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <StudentDashboardLayout>
                    <AnnouncementsMain />
                    </StudentDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default StudentAnnouncements;