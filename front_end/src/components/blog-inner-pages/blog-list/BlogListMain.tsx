import BlogListSingleCard from '@/components/common/blog/BlogListSingleCard';
import BlogSidebar from '@/components/common/blog/BlogSidebar';
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import BasicPagination from '@/components/elements/pagination/BasicPagination';
import blogData from '@/data/blog-data';
import React from 'react';

const BlogListMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Blog List' />
            {/* -- blog list area start -- */}
            <section className="bd-blog-list-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-xxl-8 col-xl-8 col-xxl-8 col-lg-8">
                            <div className="row gy-30">
                                {
                                    blogData.slice(22, 30).map((blog) => (
                                        <BlogListSingleCard blog={blog} key={blog.id} />
                                    ))
                                }

                            </div>
                            {/* -- pagination style -- */}
                            <BasicPagination />
                            {/* -- pagination style end -- */}
                        </div>
                        <div className="col-xxl-4 col-xl-4 col-xxl-4 col-lg-4">
                            <BlogSidebar />
                        </div>
                    </div>
                </div>
            </section>
            {/* -- blog list area end -- */}
        </>
    );
};

export default BlogListMain;