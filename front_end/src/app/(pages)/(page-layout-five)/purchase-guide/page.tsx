import PurchaseGuideMain from '@/components/pages/page-layout-five/purchase-guide/PurchaseGuideMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Purchase Guide - Education & Online Courses React NextJs Template",
};

const PurchaseGuide = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <PurchaseGuideMain />
                </main>
            </Wrapper>
        </>
    );
};

export default PurchaseGuide;