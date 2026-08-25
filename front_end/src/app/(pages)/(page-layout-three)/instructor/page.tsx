import InstructorMainArea from '@/components/pages/page-layout-three/Instructor/InstructorMainArea';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Instructor - Education & Online Courses React NextJs Template",
};

const Instructor = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <InstructorMainArea />
                </main>
            </Wrapper>
        </>
    );
};

export default Instructor;