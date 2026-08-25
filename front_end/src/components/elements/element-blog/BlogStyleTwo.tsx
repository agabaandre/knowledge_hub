import elementBlogData from '@/data/elements/element-blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const BlogStyleTwo = () => {
    return (
        <>
            {/* -- blog style 02 start -- */}
            <section className="bd-elements-blog section-space">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Blog Style 02</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            elementBlogData.slice(3, 6).map((item) => (
                                <div className="col-xxl-4 col-xl-4 col-lg-6 col-md-6" key={item.id}>
                                    <article className="bd-blog-wrapper style-two">
                                        <div className="bd-blog-thumb">
                                            <span className="bd-badge badge-dark">{item.date}</span>
                                            <Link href="#"><Image src={item.image} alt="image" /></Link>
                                        </div>
                                        <div className="content">
                                            <div className="bd-blog-meta">
                                                <span className="info">{item.category}</span>
                                                <span className="icon">
                                                    <Link className="border-btn" href="#">
                                                        <i className="fa-light fa-arrow-right-long"></i>
                                                    </Link>
                                                </span>
                                            </div>
                                            <h5 className="title underline"><Link href="#">{item.title}</Link></h5>
                                            <p className="description">{item.description}</p>
                                        </div>
                                    </article>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- blog style 02 end -- */}
        </>
    );
};

export default BlogStyleTwo;