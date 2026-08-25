import elementBlogData from '@/data/elements/element-blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const BlogStyleSeven = () => {
    return (
        <>
            {/* -- blog style 07 start -- */}
            <section className="bd-elements-blog section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Blog Style 07</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            elementBlogData.slice(16, 19).map((item) => (
                                <div className="col-xl-4 col-lg-6 col-md-6" key={item.id}>
                                    <article className="bd-blog-wrapper style-seven position-relative">
                                        <div className="bd-blog-thumb">
                                            <Link href="#"><Image src={item.image} alt="img" /></Link>
                                        </div>
                                        <div className="bd-blog-badge">
                                            <Link href="#" className={`bd-badge ${item.badgeColor}`}>Education</Link>
                                        </div>
                                        <div className="bd-blog-content">
                                            <div className="bd-blog-meta-list mb-15">
                                                <div className="bd-blog-meta-item">
                                                    <span className="meta-icon">
                                                        <i className="fa-solid fa-user"></i>
                                                    </span>
                                                    <span className="meta-text"><Link className="meta-author" href="#">Emily
                                                        Johnson</Link></span>
                                                </div>
                                                <div className="bd-blog-meta-item">
                                                    <span className="meta-icon">
                                                        <i className="fa-sharp fa-light fa-calendar-days"></i>
                                                    </span>
                                                    <span className="meta-text"><Link href="#">Jan 25 2024</Link></span>
                                                </div>
                                            </div>
                                            <h5 className="title mb-25 underline"><Link href="#">5 Ways to Enhance Online Learning
                                                Experiences</Link></h5>
                                            <Link className="bd-btn btn-outline-primary btn-small" href="#">Read more</Link>
                                        </div>
                                    </article>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- blog style 07 end --   */}
        </>
    );
};

export default BlogStyleSeven;