import elementBlogData from '@/data/elements/element-blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const BlogStyleOne = () => {
    return (
        <>
            {/* -- blog style 01 start -- */}
            <section className="bd-elements-blog section-space primary-bg">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Blog Style 01</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            elementBlogData.slice(0, 3).map((item) => (
                                <div className="col-xl-4 col-lg-6 col-md-6" key={item.id}>
                                    <div className="bd-blog-wrapper style-one">
                                        <div className="bd-blog-thumb overflow-hidden">
                                            <Link href="#"><Image src={item.image} alt="image" /></Link>
                                        </div>
                                        <div className="bd-blog-content">
                                            <div className="d-flex-between mb-10">
                                                <Link className="bd-badge badge-secondary" href="#">iStudy</Link>
                                                <div className="bd-blog-meta-list">
                                                    <div className="bd-blog-meta-item">
                                                        <span className="meta-icon">
                                                            <i className="fa-sharp fa-light fa-calendar-days"></i>
                                                        </span>
                                                        <span className="meta-text"><Link href="#">{item.date}</Link></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5 className="title underline">
                                                <Link href="#">{item.title}</Link>
                                            </h5>
                                            <div className="d-flex-between">
                                                <div className="bd-blog-meta-list">
                                                    <div className="bd-blog-meta-item">
                                                        <span className="meta-icon">
                                                            <i className="fa-solid fa-user"></i>
                                                        </span>
                                                        <span className="meta-text"><Link className="meta-author fw-6" href="#">{item.authorName}</Link></span>
                                                    </div>
                                                </div>
                                                <div className="bd-blog-btn">
                                                    <Link className="bd-text-btn" href="#">Read More
                                                        <span className="box-icon">
                                                            <i className="fa-regular fa-angle-right first-icon"></i>
                                                            <i className="fa-regular fa-angle-right second-icon"></i>
                                                        </span>
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- blog style 01 end -- */}
        </>
    );
};

export default BlogStyleOne;