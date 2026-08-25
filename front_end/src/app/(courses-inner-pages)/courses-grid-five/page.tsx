import CourseGridFiveMain from '@/components/courses-inner-pages/courses-grid-five/CourseGridFiveMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Courses Grid - Education & Online Courses React NextJs Template",
};

const CoursesGridFive = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CourseGridFiveMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CoursesGridFive;