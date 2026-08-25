import { IBlog } from '@/interFace/interFace';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
interface IBlogListProps {
    blog: IBlog;
}

const BlogListSingleCard = ({ blog }: IBlogListProps) => {
    return (
        <>
            <div className="col-xl-12 col-lg-12 col-md-12">
                <div className="bd-blog-wrapper style-three">
                    <div className="bd-blog-thumb">
                        <Link href={`/blog/blog-details/${blog.id}`}>
                            {blog.image && <Image src={blog.image} alt="image" />}
                        </Link>
                        <div className="bd-blog-badge">
                            <Link href="/blog" className="bd-badge badge-primary">{blog.badge}</Link>
                        </div>
                    </div>
                    <div className="bd-blog-content">
                        <div className="bd-blog-meta-list mb-15">
                            <div className="bd-blog-meta-item has-separator">
                                <span className="meta-icon"><i className="fa-light fa-calendar"></i></span>
                                <span> {blog.date} </span>
                            </div>
                            {blog.comments !== undefined && (
                            <div className="bd-blog-meta-item">
                                <span className="meta-icon"><i className="fa-light fa-comment"></i></span>
                                <span>{blog.comments} {blog?.comments > 1 ? "Comments" : "Comment"}</span>
                            </div>
                            )}
                        </div>
                        <h5 className="title underline"><Link href={`/blog/blog-details/${blog.id}`}>{blog.title}</Link></h5>
                        <p>{blog.description}</p>
                        <div className="bd-blog-btn">
                            <Link className="bd-text-btn" href={`/blog/blog-details/${blog.id}`}>Read More
                                <span className="box-icon">
                                    <i className="fa-regular fa-arrow-right-long first-icon"></i>
                                    <i className="fa-regular fa-arrow-right-long second-icon"></i>
                                </span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default BlogListSingleCard;