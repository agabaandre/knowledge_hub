import ElementCoursesMain from '@/components/elements/element-courses/ElementCoursesMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Courses - Education & Online Courses React NextJs Template",
};

const ElementCourses = () => {
    return (
        <>
            <Wrapper>
                <main className='main-area'>
                    <ElementCoursesMain />
                </main>
            </Wrapper>
        </>
    );
};

export default ElementCourses;