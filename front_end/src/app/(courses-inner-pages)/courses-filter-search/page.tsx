import CourseFilterSearchMain from '@/components/courses-inner-pages/courses-filter-search/CourseFilterSearchMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Search Filter - Education & Online Courses React NextJs Template",
};

const CourseFilterSearch = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CourseFilterSearchMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CourseFilterSearch;