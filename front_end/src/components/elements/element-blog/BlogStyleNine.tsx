import elementBlogData from '@/data/elements/element-blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const BlogStyleNine = () => {
    return (
        <>
            {/* -- blog style 09 start -- */}
            <section className="bd-elements-blog section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Blog Style 09</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            elementBlogData.slice(22, 24).map((item) => (
                                <div className="col-xl-6 col-lg-6" key={item.id}>
                                    <div className="bd-blog-wrapper style-nine">
                                        <div className="bd-blog-thumb">
                                            <Link href="#"><Image style={{ width: "100%", height: "auto" }} src={item.image} alt="image" /></Link>
                                        </div>
                                        <div className="bd-blog-content">
                                            <Link className={`bd-badge ${item.badgeColor} mb-15`} href="#">{item.badge}</Link>
                                            <h4 className="title underline white-text mb-20">
                                                <Link href="#">{item.title}</Link>
                                            </h4>
                                            <div className="bd-blog-meta-list">
                                                <div className="bd-blog-meta-item has-separator">
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
                                                    <span className="meta-text">{item.comment} {item?.comment > 1 ? "Comments" : "Comment"}</span>
                                                </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- blog style 09 end -- */}
        </>
    );
};

export default BlogStyleNine;