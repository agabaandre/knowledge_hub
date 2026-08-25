import FacultyMembersMain from '@/components/pages/page-layout-two/faculty-members/FacultyMembersMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Faculty Members - Education & Online Courses React NextJs Template",
};

const FacultyMembers = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <FacultyMembersMain />
                </main>
            </Wrapper>
        </>
    );
};

export default FacultyMembers;