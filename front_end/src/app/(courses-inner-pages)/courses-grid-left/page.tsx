import CourseGridLeftMain from '@/components/courses-inner-pages/course-grid-left/CourseGridLeftMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Courses Grid Left - Education & Online Courses React NextJs Template",
};

const CourseGridLeft = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CourseGridLeftMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CourseGridLeft;