import ElementsBreadcrumb from '@/components/common/Breadcrumb/ElementsBreadcrumb';
import React from 'react';
import CategoryStyleOne from './CategoryStyleOne';
import CategoryStyleTwo from './CategoryStyleTwo';
import CategoryStyleThree from './CategoryStyleThree';
import CategoryStyleFour from './CategoryStyleFour';
import CategoryStyleFive from './CategoryStyleFive';
import CategoryStyleSix from './CategoryStyleSix';
import CategoryStyleSeven from './CategoryStyleSeven';

const ElementCategoriesMain = () => {
    return (
        <>
            <ElementsBreadcrumb title='Categories' subTitle='Categories style' />
            <CategoryStyleOne />
            <CategoryStyleTwo />
            <CategoryStyleThree />
            <CategoryStyleFour />
            <CategoryStyleFive />
            <CategoryStyleSix />
            <CategoryStyleSeven />
        </>
    );
};

export default ElementCategoriesMain;