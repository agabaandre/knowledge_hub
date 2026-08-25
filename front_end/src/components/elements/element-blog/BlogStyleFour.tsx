import elementBlogData from '@/data/elements/element-blog-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const BlogStyleFour = () => {
    return (
        <>
            {/* -- blog style 04 start -- */}
            <section className="bd-elements-blog section-space">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Blog Style 04</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            elementBlogData.slice(8, 11).map((item) => (
                                <div className="col-xxl-4 col-xl-4 col-lg-6 col-md-6" key={item.id}>
                                    <article className="bd-blog-wrapper style-four">
                                        <div className="bd-blog-thumb">
                                            <Link href="#">
                                                <Image src={item.image} alt={item.title} />
                                            </Link>
                                        </div>
                                        <div className="bd-blog-content">
                                            <div className="bd-blog-meta-list">
                                                <div className="bd-blog-meta-item has-separator-black">
                                                    <span className="meta-icon"><i className="fa-solid fa-user"></i></span>
                                                    <span className="meta-text"><Link className="meta-author" href="#">{item.authorName}</Link></span>
                                                </div>
                                                <div className="bd-blog-meta-item">
                                                    <span className="meta-icon"><i className="fa-sharp fa-light fa-calendar-days"></i></span>
                                                    <span className="meta-text"><Link href="#">{item.date}</Link></span>
                                                </div>
                                            </div>
                                            <h5 className="title underline">
                                                <Link href="#">{item.title}</Link>
                                            </h5>
                                            <p>{item.description}</p>
                                            <div className="bd-blog-btn">
                                                <div className="icon-text-btn p-relative">
                                                    <Link href="#">
                                                        <span>Read More</span>{" "}
                                                        <i>
                                                            <svg width="18" height="14" viewBox="0 0 18 14" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M11.2871 1L17 6.71285L11.2871 12.4257" stroke="currentColor"
                                                                    strokeWidth="1.5" strokeMiterlimit="10" strokeLinecap="round"
                                                                    strokeLinejoin="round"></path>
                                                                <path d="M1 6.71313H16.8397" stroke="currentColor" strokeWidth="1.5"
                                                                    strokeMiterlimit="10" strokeLinecap="round" strokeLinejoin="round"></path>
                                                            </svg>
                                                        </i>
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- blog style 04 end -- */}
        </>
    );
};

export default BlogStyleFour;