import OnlineCourseMain from '@/components/online-course/OnlineCourseMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Online Courses - Education & Online Courses React NextJs Template",
};

const OnlineCourse = () => {
    return (
        <>
            <Wrapper>
                <main className="main-area">
                    <OnlineCourseMain />
                </main>
            </Wrapper>
        </>
    );
};

export default OnlineCourse;