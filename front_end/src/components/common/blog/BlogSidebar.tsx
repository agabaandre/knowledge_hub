import Link from 'next/link';
import React from 'react';
import Image from 'next/image';
import sidebarBanner from '../../../../public/assets/images/blog/sidebar-banner.webp';
import blogData from '@/data/blog-data';

const SearchBox = () => (
    <div className="bd-blog-widget widget-search">
        <h5 className="bd-widget-title mb-20">Search</h5>
        <div className="bd-sidebar-search">
            <form className="bd-sidebar-search-form" action="#" method="get">
                <input type="text" name="s" placeholder="Search" />
                <button type="submit"><i className="far fa-search"></i></button>
            </form>
        </div>
    </div>
);

const LatestPosts = () => (
    <div className="bd-blog-widget widget-latest-posts">
        <h5 className="bd-widget-title mb-20">Latest Post</h5>
        <div className="bd-widget-posts">
            {blogData.slice(13, 16).map((item) => (
                <div className="bd-recent-post-item" key={item.id}>
                    <div className="bd-recent-post-thumb">
                        <Link href={`/blog/blog-details/${item.id}`}>
                           {item.image &&  <Image src={item.image} alt="image not found" />}
                        </Link>
                    </div>
                    <div className="bd-recent-post-content">
                        <div className="bd-recent-post-meta">
                            <span className="icon"><i className="fa-light fa-calendar-days"></i></span>
                            <span className="date">{item.date}</span>
                        </div>
                        <h6 className="bd-recent-post-title underline">
                            <Link href={`/blog/blog-details/${item.id}`}>{item.title}</Link>
                        </h6>
                    </div>
                </div>
            ))}
        </div>
    </div>
);

const categories = [
    { name: "Online Learning", count: 10 },
    { name: "Study Tips", count: 6 },
    { name: "Teaching Methods", count: 4 },
    { name: "Career Advice", count: 5 },
    { name: "Educational Resources", count: 8 },
    { name: "Skill Development", count: 9 },
    { name: "Parenting Tips", count: 3 },
    { name: "Academic Success", count: 7 },
    { name: "Distance Learning", count: 4 }
];

const BlogCategories = () => (
    <div className="bd-blog-widget widget_categories">
        <h5 className="bd-widget-title mb-20">Categories</h5>
        <ul className="bd-widget-list">
            {categories.map((cat, index) => (
                <li className="underline" key={index}>
                    <Link href="/blog/blog-details">{cat.name}</Link> ({cat.count})
                </li>
            ))}
        </ul>
    </div>
);

const BlogTags = () => {
    const tags = ["Learning", "Motivation", "Education", "Data Science", "Tips", "Course"];

    return (
        <div className="bd-blog-widget widget_tag_cloud">
            <h5 className="bd-widget-title mb-20">Tags</h5>
            <div className="tagcloud">
                {tags.map((tag, index) => (
                    <Link href="/blog/blog-details" key={index}>{tag}</Link>
                ))}
            </div>
        </div>
    );
};

const SidebarPromotion = () => (
    <div className="bd-sidebar-promotion">
        <Link href="/sign-up" className="thumb">
            <Image src={sidebarBanner} alt="Sidebar Banner"/>
        </Link>
    </div>
);

const BlogSidebar = () => (
    <aside className="bd-blog-sidebar sidebar-right sidebar-sticky">
        <SearchBox />
        <LatestPosts />
        <BlogCategories />
        <BlogTags />
        <SidebarPromotion />
    </aside>
);

export default BlogSidebar;
