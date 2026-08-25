import ShopDetailsTwoMain from '@/components/pages/page-layout-four/shop/shop-details-two/ShopDetailsTwoMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Shop Details - Education & Online Courses React NextJs Template",
};

const ShopDetailsTwo = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <ShopDetailsTwoMain />
                </main>
            </Wrapper>
        </>
    );
};

export default ShopDetailsTwo;