import CourseGridTwoMain from '@/components/courses-inner-pages/course-grid-two/CourseGridTwoMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Courses Grid - Education & Online Courses React NextJs Template",
};

const CourseGridTwo = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CourseGridTwoMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CourseGridTwo;