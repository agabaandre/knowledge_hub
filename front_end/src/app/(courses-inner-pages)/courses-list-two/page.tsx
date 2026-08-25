import CourseListTwoMain from '@/components/courses-inner-pages/courses-list-two/CourseListTwoMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Courses List Two - Education & Online Courses React NextJs Template",
};

const CourseListTwo = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CourseListTwoMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CourseListTwo;