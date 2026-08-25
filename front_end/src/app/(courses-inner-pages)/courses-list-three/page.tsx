import CourseListThreeMain from '@/components/courses-inner-pages/courses-list-three/CourseListThreeMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Courses List Three - Education & Online Courses React NextJs Template",
};

const CourseListThree = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CourseListThreeMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CourseListThree;