import WishlistMain from '@/components/pages/page-layout-four/wishlist/WishlistMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Wishlist - Education & Online Courses React NextJs Template",
};

const Wishlist = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <WishlistMain />
                </main>
            </Wrapper>
        </>
    );
};

export default Wishlist;