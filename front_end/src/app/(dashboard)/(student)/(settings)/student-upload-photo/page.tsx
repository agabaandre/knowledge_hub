import StudentUploadPhotoMain from '@/components/dashboard/student/student-settings/StudentUploadPhotoMain';
import Wrapper from '@/layout/DefaultWrapper';
import StudentDashboardLayout from '@/layout/StudentDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Student Change Photo - Education & Online Courses React NextJs Template",
};

const StudentUploadPhoto = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <StudentDashboardLayout>
                        <StudentUploadPhotoMain />
                    </StudentDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default StudentUploadPhoto;