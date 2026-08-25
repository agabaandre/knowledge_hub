import BlogMain from '@/components/blog-inner-pages/blog/BlogMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Blog Standard - Education & Online Courses React NextJs Template",
};

const Blog = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <BlogMain />
                </main>
            </Wrapper>
        </>
    );
};

export default Blog;