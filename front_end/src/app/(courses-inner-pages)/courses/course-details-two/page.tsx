import CourseDetailsTwoMain from '@/components/courses-inner-pages/courses/course-details-two/CourseDetailsTwoMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Course Details Two - Education & Online Courses React NextJs Template",
};

const CourseDetailsTwo = () => {
    return (
        <>
            <Wrapper>
                <main className="main-area">
                    <CourseDetailsTwoMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CourseDetailsTwo;