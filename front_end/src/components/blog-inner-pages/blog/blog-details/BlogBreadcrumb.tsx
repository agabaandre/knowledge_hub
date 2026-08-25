import Link from 'next/link';
import React from 'react';
import BlogDetailsBanner from '../../../../../public/assets/images/blog/blog-details-banner.webp';
import avatarImg from '../../../../../public/assets/images/avatar/avatar.webp';
import dotShapeImg from '../../../../../public/assets/images/shape/inner-dots-shape.webp';
import Image from 'next/image';
import { IBlog } from '@/interFace/interFace';
interface IBlogProps {
    blog: IBlog | undefined
}
const BlogBreadcrumb = ({ blog }: IBlogProps) => {
    return (
        <>
            {/* -- blog details breadcrumb area start -- */}
            <section className="bd-blog-details-breadcrumb-area bd-blog-details-breadcrumb-bg section-space" style={{ backgroundImage: `url(${BlogDetailsBanner.src})` }}>
                <div className="container">
                    <div className="row">
                        <div className="col-lg-9 col-md-9">
                            <div className="bd-blog-details-breadcrumb-content">
                                <div className="bd-blog-details-breadcrumb-tag">
                                    <Link className="bd-badge badge-primary" href="/blog/blog-details">{blog?.badge ? blog?.badge : "Education Insights"}</Link>
                                </div>
                                <h1 className="bd-blog-details-breadcrumb-title">{blog?.title}</h1>
                                <div className="bd-blog-details-breadcrumb-meta">
                                    <div className="bd-blog-meta-item has-separator">
                                        <span className="meta-thumb">
                                            <Image src={avatarImg} alt="avatar" />
                                        </span>
                                        <span className="meta-text"><Link className="meta-author" href="/blog/blog-details">Sarah
                                            Collins</Link></span>
                                    </div>
                                    <div className="bd-blog-meta-item has-separator">
                                        <span className="meta-icon">
                                            <i className="fa-sharp fa-light fa-calendar-days"></i>
                                        </span>
                                        <span className="meta-text"><Link href="/blog/blog-details">{blog?.date ? blog?.date : "October 15, 2024"}</Link></span>
                                    </div>
                                    <div className="bd-blog-meta-item">
                                        <span className="meta-icon">
                                            <i className="fa-light fa-comments"></i>
                                        </span>
                                        <span className="meta-text"><Link href="/blog/blog-details">3 Comments</Link></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="inner-shape-dots">
                    <Image src={dotShapeImg} alt="" />
                </div>
            </section>
            {/* -- blog details breadcrumb area end -- */}
        </>
    );
};

export default BlogBreadcrumb;