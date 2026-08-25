import CreateCourseMain from '@/components/courses-inner-pages/create-course/CreateCourseMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Create New Courses - Education & Online Courses React NextJs Template",
};

const CreateCourse = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CreateCourseMain />
                </main>
            </Wrapper>
        </>
    );
};

export default CreateCourse;