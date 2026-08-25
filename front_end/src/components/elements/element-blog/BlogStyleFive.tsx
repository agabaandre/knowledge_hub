import elementBlogData from '@/data/elements/element-blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const BlogStyleFive = () => {
    return (
        <>
            {/* -- blog style 05 start -- */}
            <section className="bd-elements-blog section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Blog Style 05</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            elementBlogData.slice(11, 14).map((item) => (
                                <div className="col-xl-4 col-lg-6 col-md-6" key={item.id}>
                                    <article className="bd-blog-wrapper style-five">
                                        <div className="bd-blog-thumb">
                                            <Link href="#"><Image style={{width:"100%", height:"auto"}} src={item.image} alt="img"  priority/></Link>
                                        </div>
                                        <div className="bd-blog-badge">
                                            <Link href="#" className={`bd-badge ${item.badgeColor}`}>{item.badge}</Link>
                                        </div>
                                        <div className="bd-blog-content">
                                            <div className="bd-blog-meta-list mb-10">
                                                <div className="bd-blog-meta-item">
                                                    <span className="meta-icon">
                                                        <i className="fa-solid fa-user"></i>
                                                    </span>
                                                    <span className="meta-text"><Link className="meta-author" href="#">{item.authorName}</Link></span>
                                                </div>
                                                <div className="bd-blog-meta-item">
                                                    <span className="meta-icon">
                                                        <i className="fa-sharp fa-light fa-calendar-days"></i>
                                                    </span>
                                                    <span className="meta-text"><Link href="#">{item.date}</Link></span>
                                                </div>
                                            </div>
                                            <h5 className="title underline"><Link href="#">{item.title}</Link></h5>
                                            <div className="bd-blog-btn">
                                                <Link className="bd-half-outline-btn" href="#"><span className="text">Read
                                                    More</span></Link>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- blog style 05 end -- */}
        </>
    );
};

export default BlogStyleFive;