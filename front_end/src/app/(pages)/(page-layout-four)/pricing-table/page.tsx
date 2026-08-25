import PricingTableMain from '@/components/pages/page-layout-four/pricing-table/PricingTableMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Pricing Table - Education & Online Courses React NextJs Template",
};

const PricingPage = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <PricingTableMain />
                </main>
            </Wrapper>
        </>
    );
};

export default PricingPage;