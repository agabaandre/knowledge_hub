import InstructorSocialProfileMain from '@/components/dashboard/instructor/instructor-settings/InstructorSocialProfileMain';
import Wrapper from '@/layout/DefaultWrapper';
import InstructorDashboardLayout from '@/layout/InstructorDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Instructor Social Profile - Education & Online Courses React NextJs Template",
};

const InstructorSocialProfile = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <InstructorDashboardLayout>
                    <InstructorSocialProfileMain />
                    </InstructorDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default InstructorSocialProfile;