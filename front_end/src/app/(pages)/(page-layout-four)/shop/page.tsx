import ShopMain from '@/components/pages/page-layout-four/shop/ShopMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Shop - Education & Online Courses React NextJs Template",
};

const Shop = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <ShopMain />
                </main>
            </Wrapper>
        </>
    );
};

export default Shop;