import InstructorMyQuizMain from '@/components/dashboard/instructor/instructor-my-quiz/InstructorMyQuizMain';
import Wrapper from '@/layout/DefaultWrapper';
import InstructorDashboardLayout from '@/layout/InstructorDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Instructor Quiz Attempts - Education & Online Courses React NextJs Template",
};

const InstructorMyQuiz = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <InstructorDashboardLayout>
                    <InstructorMyQuizMain />
                    </InstructorDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default InstructorMyQuiz;