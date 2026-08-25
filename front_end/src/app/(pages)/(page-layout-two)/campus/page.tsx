import CampusMain from '@/components/pages/page-layout-two/campus/CampusMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Campus - Education & Online Courses React NextJs Template",
};

const Campus = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <CampusMain />
                </main>
            </Wrapper>
        </>
    );
};

export default Campus;