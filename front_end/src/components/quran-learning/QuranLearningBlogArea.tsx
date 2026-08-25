import React from "react";
import Link from "next/link";
import Image from "next/image";
import blogData from "@/data/blog-data";

const QuranLearningBlogArea: React.FC = () => {
    const slicedBlogs = blogData.slice(39, 42);

    // Islamic Knowledge badge filter
    const islamicKnowledgeBlog = slicedBlogs.find(blog => blog.badge === "Islamic Knowledge");
    // Other blogs excluding Islamic Knowledge
    const otherBlogs = slicedBlogs.filter(blog => blog.badge !== "Islamic Knowledge");

    return (
        <section className="bd-blog-area section-space">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-xl-7">
                        <div className="bd-section-title-wrapper section-title-space text-center">
                            <span className="bd-section-subtitle">Islamic Insights</span>
                            <h2 className="bd-section-title">Discover Insights and Reflections</h2>
                        </div>
                    </div>
                </div>
                <div className="row gy-30">
                    {/* Only show the Islamic Knowledge blog if it exists */}
                    {islamicKnowledgeBlog && (
                        <div key={islamicKnowledgeBlog.id} className="col-xl-6">
                            <div className="bd-blog-wrapper style-nine">
                                <div className="bd-blog-thumb">
                                    <Link href={`/blog/blog-details/${islamicKnowledgeBlog.id}`}>
                                        {islamicKnowledgeBlog.image && (
                                            <Image
                                                style={{ width: "100%", height: "auto" }}
                                                src={islamicKnowledgeBlog.image}
                                                alt="image"
                                            />
                                        )}
                                    </Link>
                                </div>
                                <div className="bd-blog-content">
                                    <Link className="bd-badge badge-primary mb-15" href="#">
                                        {islamicKnowledgeBlog.badge}
                                    </Link>
                                    <h4 className="title underline white-text mb-20">
                                        <Link href={`/blog/blog-details/${islamicKnowledgeBlog.id}`}>
                                            {islamicKnowledgeBlog.title}
                                        </Link>
                                    </h4>
                                    <div className="bd-blog-meta-list">
                                        <div className="bd-blog-meta-item has-separator">
                                            <span className="meta-icon">
                                                <i className="fa-solid fa-user"></i>
                                            </span>
                                            <span className="meta-text">
                                                <Link
                                                    className="meta-author"
                                                    href={`/blog/blog-details/${islamicKnowledgeBlog.id}`}
                                                >
                                                    {islamicKnowledgeBlog.authorName}
                                                </Link>
                                            </span>
                                        </div>
                                        <div className="bd-blog-meta-item">
                                            <span className="meta-icon">
                                                <i className="fa-sharp fa-light fa-calendar-days"></i>
                                            </span>
                                            <span className="meta-text">
                                                <Link href={`/blog/blog-details/${islamicKnowledgeBlog.id}`}>
                                                    {islamicKnowledgeBlog.date}
                                                </Link>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Display other blogs */}
                    <div className="col-xl-6">
                        <div className="bd-blog-column">
                            {otherBlogs.map((blog) => (
                                <article key={blog.id} className="bd-blog-wrapper style-six">
                                    <div className="bd-blog-thumb">
                                        <Link href={`/blog/blog-details/${blog.id}`}>
                                            {blog.image && <Image src={blog.image} alt="image" />}
                                        </Link>
                                    </div>
                                    <div className="bd-blog-content">
                                        <Link className="bd-badge badge-primary mb-15" href="#">
                                            {blog.badge}
                                        </Link>
                                        <h5 className="blog-title mb-15 underline">
                                            <Link href={`/blog/blog-details/${blog.id}`}>{blog.title}</Link>
                                        </h5>
                                        <div className="bd-blog-meta-list">
                                            <div className="bd-blog-meta-item">
                                                <span className="meta-icon">
                                                    <i className="fa-solid fa-user"></i>
                                                </span>
                                                <span className="meta-text">
                                                    <Link className="meta-author" href={`/blog/blog-details/${blog.id}`}>
                                                        {blog.authorName}
                                                    </Link>
                                                </span>
                                            </div>
                                            <div className="bd-blog-meta-item">
                                                <span className="meta-icon">
                                                    <i className="fa-sharp fa-light fa-calendar-days"></i>
                                                </span>
                                                <span className="meta-text">
                                                    <Link href={`/blog/blog-details/${blog.id}`}>{blog.date}</Link>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default QuranLearningBlogArea;
