import TermsConditionsMain from '@/components/pages/page-layout-one/terms-conditions/TermsConditionsMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Terms And Conditions - Education & Online Courses React NextJs Template",
};

const TermsConditions = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <TermsConditionsMain />
                </main>
            </Wrapper>
        </>
    );
};

export default TermsConditions;