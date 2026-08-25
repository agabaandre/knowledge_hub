import InstructorAnnouncementMain from '@/components/dashboard/instructor/instructor-announcement/InstructorAnnouncementMain';
import Wrapper from '@/layout/DefaultWrapper';
import InstructorDashboardLayout from '@/layout/InstructorDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Instructor Announcement - Education & Online Courses React NextJs Template",
};

const InstructorAnnouncement = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <InstructorDashboardLayout>
                        <InstructorAnnouncementMain />
                    </InstructorDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default InstructorAnnouncement;