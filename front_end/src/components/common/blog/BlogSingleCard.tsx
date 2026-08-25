import { IBlog } from '@/interFace/interFace';
import BlogArrowSvg from '@/svg/BlogArrowSvg';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

interface propsType {
    item: IBlog;
}
const BlogSingleCard = ({ item }: propsType) => {
    return (
        <>
            {/* -- blog area start -- */}
            <article className="bd-blog-wrapper style-four">
                <div className="bd-blog-thumb">
                    <Link href={`/blog/blog-details/${item.id}`}>
                        {item.image && <Image src={item.image} alt="images" />}
                    </Link>
                </div>
                <div className="bd-blog-content">
                    <div className="bd-blog-meta-list">
                        <div className="bd-blog-meta-item has-separator-black">
                            <span className="meta-icon">
                                <i className="fa-solid fa-user"></i>
                            </span>
                            <span className="meta-text"><Link className="meta-author"
                                href={`/blog/blog-details/${item.id}`}>{item.authorName}</Link></span>
                        </div>
                        <div className="bd-blog-meta-item">
                            <span className="meta-icon">
                                <i className="fa-sharp fa-light fa-calendar-days"></i>
                            </span>
                            <span className="meta-text">
                                <Link href={`/blog/blog-details/${item.id}`}>{item.date}</Link></span>
                        </div>
                    </div>
                    <h5 className="title underline">
                        <Link href={`/blog/blog-details/${item.id}`}>{item.title}</Link>
                    </h5>
                    <p>{item.description}</p>
                    <div className="">
                        <div className="icon-text-btn p-relative">
                            <Link href={`/blog/blog-details/${item.id}`}>
                                <span>Read More</span>{" "}
                                <BlogArrowSvg/>
                            </Link>
                        </div>
                    </div>
                </div>
            </article>
            {/* -- blog area end -- */}
        </>
    );
};

export default BlogSingleCard;