"use client"
import BlogSidebar from '@/components/common/blog/BlogSidebar';
import blogData from '@/data/blog-data';
import Link from 'next/link';
import React from 'react';
import BlogBreadcrumb from './BlogBreadcrumb';
import PostboxContent from './PostboxContent';
import Image from 'next/image';
import PostboxAuthor from './PostboxAuthor';
import PostboxComment from './PostboxComment';
import SimilarBlogItem from './SimilarBlogItem';
import BlogCommentForm from '@/form/BlogCommentForm';


const BlogDetailsMain = ({ blogId }: { blogId: number }) => {
    const blog = blogData.find((item) => item.id == blogId);
    return (
        <>
            <BlogBreadcrumb blog={blog} />
            {/* -- postbox area start -- */}
            <section className="bd-postbox-area section-space">
                <div className="container">
                    <div className="row">
                        <div className="col-xxl-8 col-xl-8 col-xxl-8 col-lg-8">
                            <div className="bd-postbox-wrapper">
                                <div className="bd-blog-feature-thumb">{blog?.image && <Image src={blog?.image} style={{width:"100%", height:"auto"}} alt="blog feature image" />}</div>
                                <PostboxContent />
                                <div className="bd-postbox-navigation">
                                    <div className="row gy-30 align-items-center">
                                        <div className="col-md-6">
                                            <div className="bd-postbox-more-left">
                                                <div className="bd-postbox-more-link">
                                                    <Link href="/blog/blog-details">
                                                        <span className="mr-10">
                                                            <i className="fa-sharp fa-solid fa-arrow-left"></i>
                                                        </span>
                                                        Previous
                                                    </Link>
                                                </div>
                                                <h5 className="title underline"><Link href="/blog/blog-details">The Evolution of
                                                    Online Education</Link>
                                                </h5>
                                            </div>
                                        </div>
                                        <div className="col-md-6">
                                            <div className="bd-postbox-more-right text-md-end">
                                                <div className="bd-postbox-more-link">
                                                    <Link href="/blog/blog-details">
                                                        Next
                                                        <span className="ml-10">
                                                            <i className="fa-sharp fa-solid fa-arrow-right"></i>
                                                        </span>
                                                    </Link>
                                                </div>
                                                <h5 className="title underline"><Link href="/blog/blog-details">The Rise of Virtual
                                                    Classrooms</Link>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <PostboxAuthor />
                                <div className="bd-blog-postbox-comment mb-30">
                                    <h4 className="bd-section-title mb-30">3 Comments</h4>
                                    <PostboxComment />
                                </div>
                                <div className="bd-blog-postbox-comment-form">
                                    <h4 className="bd-section-title mb-30">Leave a Comment</h4>
                                    <div className="bd-review-form">
                                        <div className="bd-review-form-rating mb-15">
                                            <p>Your email address will not be published. Required fields are marked
                                                *</p>
                                        </div>
                                        <BlogCommentForm />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xxl-4 col-xl-4 col-xxl-4 col-lg-4">
                            <BlogSidebar />
                        </div>
                    </div>
                </div>
            </section>
            {/* -- postbox area end -- */}

            {/* -- similar articles area start -- */}
            <section className="bd-similar-articles-area section-space primary-bg">
                <div className="container">
                    <div className="row gy-30 justify-content-between align-items-center section-title-space">
                        <div className="col-xl-7 col-lg-8 col-md-7">
                            <div className="bd-section-title-wrapper">
                                <h2 className="bd-section-title">Similar Articles</h2>
                            </div>
                        </div>
                        <div className="col-xl-2 col-lg-4 col-md-5">
                            <div className="bd-blog-slider-navigation d-flex justify-content-md-end gap-15">
                                <button className="blog-navigation-prev"><i className="fa-regular fa-angle-left"></i></button>
                                <button className="blog-navigation-next"><i className="fa-regular fa-angle-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <SimilarBlogItem />
                </div>
            </section>
            {/* -- similar articles area end -- */}
        </>
    );
};

export default BlogDetailsMain;