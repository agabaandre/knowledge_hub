import ElementsBreadcrumb from '@/components/common/Breadcrumb/ElementsBreadcrumb';
import React from 'react';
import BlogStyleOne from './BlogStyleOne';
import BlogStyleTwo from './BlogStyleTwo';
import BlogStyleThree from './BlogStyleThree';
import BlogStyleFour from './BlogStyleFour';
import BlogStyleFive from './BlogStyleFive';
import BlogStyleSix from './BlogStyleSix';
import BlogStyleSeven from './BlogStyleSeven';
import BlogStyleEight from './BlogStyleEight';
import BlogStyleNine from './BlogStyleNine';
import BlogStyleTen from './BlogStyleTen';
import BlogStyleEleven from './BlogStyleEleven';

const ElementBlogMain = () => {
    return (
        <>
            <ElementsBreadcrumb title="Blog" subTitle="Blog style" />
            <div className='section-space-top'></div>
            <BlogStyleOne />
            <BlogStyleTwo />
            <BlogStyleThree />
            <BlogStyleFour />
            <BlogStyleFive/>
            <BlogStyleSix/>
            <BlogStyleSeven/>
            <BlogStyleEight/>
            <BlogStyleNine/>
            <BlogStyleTen/>
            <BlogStyleEleven/>
        </>
    );
};

export default ElementBlogMain;