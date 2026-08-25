import elementBlogData from '@/data/elements/element-blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const BlogStyleTen = () => {
    return (
        <>
            {/* -- blog style 10 start -- */}
            <section className="bd-elements-blog section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Blog Style 10</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            elementBlogData.slice(24, 27).map((item) => (
                                <div className="col-xl-4 col-lg-6 col-md-6" key={item.id}>
                                    <div className="bd-blog-wrapper style-ten">
                                        <div className="bd-blog-thumb-wrapper">
                                            <div className="bd-blog-thumb">
                                                <Link href="#"><Image src={item.image} alt="image" /></Link>
                                            </div>
                                            <div className="bd-blog-badge-circle">
                                                <span className="bd-circle-badge primary">
                                                    {item.date}<span className="subtitle">Aug</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div className="bd-blog-content">
                                            <div className="bd-blog-meta-list mb-10">
                                                <div className="bd-blog-meta-item has-separator-black">
                                                    <span className="meta-icon">
                                                        <i className="fa-solid fa-user"></i>
                                                    </span>
                                                    <span className="meta-text"><Link className="meta-author" href="#">{item.authorName}</Link></span>
                                                </div>
                                                {item.comment !== undefined && (
                                                <div className="bd-blog-meta-item">
                                                    <span className="meta-icon">
                                                        <i className="fa-light fa-comments"></i>
                                                    </span>
                                                    <span className="meta-text">{item?.comment} {item?.comment > 1 ? "Comments" : "Comment"}</span>
                                                </div>
                                                )}
                                            </div>
                                            <h5 className="title underline mb-25">
                                                <Link href="#">{item.title}</Link>
                                            </h5>
                                            <Link className="bd-btn btn-outline-primary btn-small" href="#">Read more</Link>
                                        </div>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- blog style 10 end -- */}
        </>
    );
};

export default BlogStyleTen;