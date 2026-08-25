import ScholarshipFinancialDetailsMain from '@/components/pages/page-layout-two/scholarships-financial-aid-details/ScholarshipFinancialDetailsMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Scholarship Financial Details - Education & Online Courses React NextJs Template",
};

const ScholarshipFinancialDetails = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <ScholarshipFinancialDetailsMain />
                </main>
            </Wrapper>
        </>
    );
};

export default ScholarshipFinancialDetails;