import CourseListOneMain from '@/components/courses-inner-pages/courses-list-one/CourseListOneMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Courses List One - Education & Online Courses React NextJs Template",
};

const CourseListOne = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CourseListOneMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CourseListOne;