import elementBlogData from '@/data/elements/element-blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const BlogStyleThree = () => {
    return (
        <>
            {/* -- blog style 03 start -- */}
            <section className="bd-elements-blog section-space primary-bg">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Blog Style 03</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            elementBlogData.slice(6, 8).map((item) => (
                                <div className="col-xl-6 col-lg-6 col-md-6" key={item.id}>
                                    <div className="bd-blog-wrapper style-three">
                                        <div className="bd-blog-thumb">
                                            <Link href="#">
                                                <Image src={item.image} alt="image" />
                                            </Link>
                                            <div className="bd-blog-badge">
                                                <Link href="#" className="bd-badge badge-primary">{item.badge}</Link>
                                            </div>
                                        </div>
                                        <div className="bd-blog-content">
                                            <div className="bd-blog-meta-list mb-15">
                                                <div className="bd-blog-meta-item has-separator">
                                                    <span className="meta-icon"><i className="fa-light fa-calendar"></i></span>
                                                    <span> {item.date} </span>
                                                </div>
                                                {item.comment !== undefined && (
                                                <div className="bd-blog-meta-item">
                                                    <span className="meta-icon"><i className="fa-light fa-comment"></i></span>
                                                    <span>{item.comment} {item?.comment > 1 ? "Comments" : "Comment"}</span>
                                                </div>
                                                )}
                                            </div>
                                            <h5 className="title underline"><Link href="#">{item.title}</Link></h5>
                                            <p>{item.description}</p>
                                            <div className="bd-blog-btn">
                                                <Link className="bd-text-btn" href="#">Read More
                                                    <span className="box-icon">
                                                        <i className="fa-regular fa-arrow-right-long first-icon"></i>
                                                        <i className="fa-regular fa-arrow-right-long second-icon"></i>
                                                    </span>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- blog style 03 end -- */}
        </>
    );
};

export default BlogStyleThree;