import elementBlogData from '@/data/elements/element-blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const BlogStyleEleven = () => {
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
                                        <h2 className="bd-elements-title small">Blog Style 11</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            elementBlogData.slice(27, 30).map((item) => (
                                <div className="col-xl-4 col-lg-6 col-md-6" key={item.id}>
                                    <article className="bd-blog-wrapper style-eleven">
                                        <div className="bd-blog-thumb-wrapper">
                                            <div className="bd-blog-thumb">
                                                <Link href="#">
                                                    <Image style={{width:"100%", height:"auto"}} src={item.image} alt="blog image" />
                                                </Link>
                                            </div>
                                            <div className="bd-blog-badge bottom">
                                                <span className="bd-badge badge-primary radius-0">{item.date}</span>
                                            </div>
                                        </div>
                                        <div className="bd-blog-content">
                                            <div className="bd-blog-meta-list mb-15">
                                                <div className="bd-blog-meta-item has-separator">
                                                    <span className="meta-icon">
                                                        <i className="fa-solid fa-user"></i>
                                                    </span>
                                                    <span className="meta-text"><Link className="meta-author" href="#">David
                                                        William</Link></span>
                                                </div>
                                                <div className="bd-blog-meta-item">
                                                    <span className="meta-icon">
                                                        <i className="fa-sharp fa-light fa-calendar-days"></i>
                                                    </span>
                                                    <span className="meta-text"><Link href="#">Feb 25 2024</Link></span>
                                                </div>
                                            </div>
                                            <h4 className="bd-blog-title underline"><Link href="#">Tips to Understand Your Child
                                                Better - Parents Guide</Link></h4>
                                        </div>
                                    </article>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- blog style 11 end -- */}
        </>
    );
};

export default BlogStyleEleven;