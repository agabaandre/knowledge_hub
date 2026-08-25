import CoursesFilterCategoryMain from '@/components/courses-inner-pages/courses-filter-category/CoursesFilterCategoryMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Filter Category - Education & Online Courses React NextJs Template",
};

const CoursesFilterCategory = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CoursesFilterCategoryMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CoursesFilterCategory;