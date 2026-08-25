import ElementCategoriesMain from '@/components/elements/element-categories/ElementCategoriesMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Categories - Education & Online Courses React NextJs Template",
};

const ElementCategories = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <ElementCategoriesMain />
                </main>
            </Wrapper>
        </>
    );
};

export default ElementCategories;