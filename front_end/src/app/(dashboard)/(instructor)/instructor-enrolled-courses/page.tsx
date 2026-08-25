import InstructorEnrolledCoursesMain from '@/components/dashboard/instructor/Instructor-enrolled-courses/InstructorEnrolledCoursesMain';
import Wrapper from '@/layout/DefaultWrapper';
import InstructorDashboardLayout from '@/layout/InstructorDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Instructor Enrolled Courses - Education & Online Courses React NextJs Template",
};

const InstructorEnrolledCourse = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <InstructorDashboardLayout>
                    <InstructorEnrolledCoursesMain />
                    </InstructorDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default InstructorEnrolledCourse;