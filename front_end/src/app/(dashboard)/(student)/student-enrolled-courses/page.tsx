import StudentEnrolledCoursesMain from '@/components/dashboard/student/student-enrolled-courses/StudentEnrolledCoursesMain';
import Wrapper from '@/layout/DefaultWrapper';
import StudentDashboardLayout from '@/layout/StudentDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Student Enrolled Courses - Education & Online Courses React NextJs Template",
};

const StudentEnrolledCourses = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <StudentDashboardLayout>
                    <StudentEnrolledCoursesMain />
                    </StudentDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default StudentEnrolledCourses;