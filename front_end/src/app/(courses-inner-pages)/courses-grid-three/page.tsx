import CourseGridThreeMain from '@/components/courses-inner-pages/course-grid-three/CourseGridThreeMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Courses Grid - Education & Online Courses React NextJs Template",
};

const CourseGridThree = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CourseGridThreeMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CourseGridThree;