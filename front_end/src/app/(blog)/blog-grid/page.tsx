import BlogGridMain from '@/components/blog-inner-pages/blog-grid/BlogGridMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Blog List - Education & Online Courses React NextJs Template",
};

const BlogGrid = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <BlogGridMain />
                </main>
            </Wrapper>
        </>
    );
};

export default BlogGrid;