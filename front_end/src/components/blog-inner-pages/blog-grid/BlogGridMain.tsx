import BlogSidebar from '@/components/common/blog/BlogSidebar';
import BlogSingleCard from '@/components/common/blog/BlogSingleCard';
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import BasicPagination from '@/components/elements/pagination/BasicPagination';
import blogData from '@/data/blog-data';
import React from 'react';

const BlogGridMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Blog Grid' />
            {/* -- course list area start -- */}
            <section className="bd-course-list-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-xxl-8 col-xl-8 col-xxl-8 col-lg-8">
                            <div className="row gy-30">
                                {
                                    blogData.slice(22, 30).map((item) => (
                                        <div className="col-xxl-6 col-xl-6 col-lg-6 col-md-6" key={item.id}>
                                            <BlogSingleCard item={item} />
                                        </div>
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
            {/* -- course list area end -- */}
        </>
    );
};

export default BlogGridMain;