import CourseGridFourMain from '@/components/courses-inner-pages/courses-grid-four/CourseGridFourMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Courses Grid - Education & Online Courses React NextJs Template",
};

const CoursesGridFour = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CourseGridFourMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CoursesGridFour;