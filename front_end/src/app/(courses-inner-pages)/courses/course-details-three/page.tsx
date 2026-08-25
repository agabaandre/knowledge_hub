import CourseDetailsThreeMain from '@/components/courses-inner-pages/courses/course-details-three/CourseDetailsThreeMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Course Details Three - Education & Online Courses React NextJs Template",
};

const CourseDetailsThree = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CourseDetailsThreeMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CourseDetailsThree;