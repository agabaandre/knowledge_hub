import ElementBlogMain from '@/components/elements/element-blog/ElementBlogMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Blog Elements - Education & Online Courses React NextJs Template",
};

const ElementBlog = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <ElementBlogMain />
                </main>
            </Wrapper>
        </>
    );
};

export default ElementBlog;