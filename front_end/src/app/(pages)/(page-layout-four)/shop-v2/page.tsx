import ShopTwoMain from '@/components/pages/page-layout-four/shop-v2/ShopTwoMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Shop V2 - Education & Online Courses React NextJs Template",
};

const ShopTwo = () => {
    return (
        <>
            <Wrapper>
                <ShopTwoMain />
            </Wrapper>
        </>
    );
};

export default ShopTwo;