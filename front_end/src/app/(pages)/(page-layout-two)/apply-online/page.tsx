import ApplyOnlineMain from '@/components/pages/page-layout-two/apply-online/ApplyOnlineMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Apply Online - Education & Online Courses React NextJs Template",
};

const ApplyOnline = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <ApplyOnlineMain />
                </main>
            </Wrapper>
        </>
    );
};

export default ApplyOnline;