import InstructorUploadPhotoMain from '@/components/dashboard/instructor/instructor-settings/InstructorUploadPhotoMain';
import Wrapper from '@/layout/DefaultWrapper';
import InstructorDashboardLayout from '@/layout/InstructorDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Instructor Upload Photo - Education & Online Courses React NextJs Template",
};

const InstructorUploadPhoto = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <InstructorDashboardLayout>
                        <InstructorUploadPhotoMain />
                    </InstructorDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default InstructorUploadPhoto;