import CoursesMain from '@/components/courses-inner-pages/courses/CoursesMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Course Grid Right - Education & Online Courses React NextJs Template",
};

const Courses = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CoursesMain />
                </main>
            </Wrapper>
        </>
    );
};

export default Courses;