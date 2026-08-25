import CourseGridMain from '@/components/courses-inner-pages/courses-grid/CourseGridMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Courses Grid - Education & Online Courses React NextJs Template",
};

const CourseGrid = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CourseGridMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CourseGrid;