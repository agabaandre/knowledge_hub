import blogData from '@/data/blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const KindergartenBlogArea = () => {
    return (
        <section className="bd-blog-area section-space">
            <div className="container">
                <div className="row">
                    <div className="bd-section-title-wrapper section-title-space text-center">
                        <span className="bd-section-subtitle">Latest News</span>
                        <h2 className="bd-section-title">Our Latest Articles</h2>
                    </div>
                </div>
                <div className="row gy-30">
                    {blogData.slice(36, 39).map(item => (
                        <div className="col-xl-4 col-lg-6 col-md-6" key={item.id}>
                            <article className="bd-blog-wrapper style-eight">
                                <div className="bd-blog-thumb">
                                    <Link href={`/blog/blog-details/${item.id}`}>
                                        {item.image && <Image src={item.image} alt="image" />}
                                    </Link>
                                </div>
                                <div className="bd-blog-content">
                                    <div className="date">
                                        <span>{item.date}</span>
                                    </div>
                                    <div className="bd-blog-meta-list">
                                        <div className="bd-blog-meta-item">
                                            <span className="meta-icon">
                                                <i className="fa-solid fa-user"></i>
                                            </span>
                                            <span className="meta-text">
                                                <Link className="meta-author" href={`/blog/blog-details/${item.id}`}>{item.authorName}</Link>
                                            </span>
                                        </div>
                                        {item.comments !== undefined && (
                                        <div className="bd-blog-meta-item">
                                            <span className="meta-icon">
                                                <i className="icon-comment"></i>
                                            </span>
                                            <span className="meta-text">
                                                <Link href={`/blog/blog-details/${item.id}`}>{item.comments} {item?.comments > 1 ? "Comments" : "Comment"}</Link>
                                            </span>
                                        </div>
                                        )}
                                    </div>
                                    <h5 className="bd-blog-title underline">
                                        <Link href={`/blog/blog-details/${item.id}`}>{item.title}</Link>
                                    </h5>
                                    <p>{item.description}</p>
                                </div>
                            </article>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default KindergartenBlogArea;
