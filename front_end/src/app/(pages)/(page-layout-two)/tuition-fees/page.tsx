import TuitionFeesMain from '@/components/pages/page-layout-two/tuition-fees/TuitionFeesMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Tution Fees - Education & Online Courses React NextJs Template",
};

const TutionFees = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <TuitionFeesMain />
                </main>
            </Wrapper>
        </>
    );
};

export default TutionFees;