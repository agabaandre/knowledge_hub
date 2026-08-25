import BlogDetailsMain from '@/components/blog-inner-pages/blog/blog-details/BlogDetailsMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Blog Details - Education & Online Courses React NextJs Template",
};

interface PageProps {
    params: Promise<{ blogId: number }>;
}
const Blog = async (props: PageProps) => {
    const resolvedParams = await props.params;
    const { blogId } = resolvedParams;
    return (
        <>
            <Wrapper>
                <main>
                    <BlogDetailsMain blogId={blogId} />
                </main>
            </Wrapper>
        </>
    );
};

export default Blog;


