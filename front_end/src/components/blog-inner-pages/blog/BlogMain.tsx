"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import BasicPagination from '@/components/elements/pagination/BasicPagination';
import React from 'react';
import BlogSidebar from '@/components/common/blog/BlogSidebar';
import blogData from '@/data/blog-data';
import Link from 'next/link';
import Image from 'next/image';
//swiper
import { Navigation, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import { useVideoModal } from '@/contextApi/VideoProvider';

const BlogMain = () => {
    const { playVideo } = useVideoModal();
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Blog Standard' />
            {/* -- blog area start -- */}
            <section className="bd-blog-area section-space">
                <div className="container">
                    <div className="row">
                        <div className="col-xxl-8 col-xl-8 col-xxl-8 col-lg-8">
                            <div className="row gy-30">
                                {blogData.slice(16, 22).map((blog, index) => (
                                    <div className="col-xl-12 col-lg-12 col-md-12" key={index}>
                                        <article className={`${blog.boxShadowClass == true ? "bd-blog bd-blog-wrapper blog-standard" : "post-details-blockquote"} `}>
                                            {blog.type === 'image' && (
                                                <div className="bd-blog-thumb">
                                                    <Link href={`/blog/blog-details/${blog.id}`}>{blog?.image && <Image src={blog.image} style={{width:"100%", height:"auto"}} alt="image" />}</Link>
                                                </div>
                                            )}
                                            {blog.type === 'slider' && (
                                                <div className="bd-postbox-slider p-relative">
                                                    <div className="p-r">
                                                        <Swiper
                                                            modules={[Navigation, Pagination]}
                                                            slidesPerView={1}
                                                            spaceBetween={30}
                                                            centeredSlides={false}
                                                            loop={true}
                                                            allowTouchMove={true}
                                                            observer={true}
                                                            pagination={{
                                                                el: ".bd-post-slider-pagination",
                                                                clickable: true,
                                                            }}
                                                            navigation={{
                                                                nextEl: ".post-navigation-next",
                                                                prevEl: ".post-navigation-prev",
                                                            }}
                                                        >
                                                            {blog?.images?.map((img, i) => (
                                                                <SwiperSlide key={i}>
                                                                    <Image src={img} alt="image" style={{width:"100%", height:"auto"}}/>
                                                                </SwiperSlide>
                                                            ))}
                                                        </Swiper>
                                                        <div className="bd-post-pagination justify-content-center">
                                                            <div className="bd-post-slider-pagination"></div>
                                                        </div>
                                                    </div>
                                                    <div className="bd-postbox-slider-navigation">
                                                        <button className="post-navigation-prev"><i
                                                            className="fa-regular fa-angle-left"></i></button>
                                                        <button className="post-navigation-next"><i
                                                            className="fa-regular fa-angle-right"></i></button>
                                                    </div>
                                                </div>
                                            )}
                                            {blog.type === 'quote' && (
                                                <blockquote className='m-0'>
                                                    <span className="icon"><i className="fa-solid fa-quote-right"></i></span>
                                                    <h3 className="title">{blog.quote}</h3>
                                                    <span className="name">{blog.authorName}</span>
                                                </blockquote>
                                            )}
                                            {blog.type === 'video' && (
                                                <div className="bd-postbox-video p-relative">
                                                    {blog?.thumbnail && <Image src={blog.thumbnail} alt="image" style={{width:"100%", height:"auto"}}/>}
                                                    <div className="bd-postbox-video-btn pos-center">
                                                        <button type='button' onClick={() => playVideo("HKk4oLIzhhM", "youtube")} className="bd-video-btn popup-video has-bg">
                                                            <span className="icon"><i className="fa-solid fa-play"></i></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            )}
                                            {blog.type === 'audio' && (
                                                <div className="bd-postbox-audio">
                                                    <iframe allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/316547873&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true"></iframe>
                                                </div>
                                            )}
                                            {blog.comments !== undefined && (
                                                <div className="bd-blog-meta-item">
                                                    <span><i className="fas fa-user"></i> by <Link href="/news">{blog.authorName}</Link></span>
                                                    <span><i className="fas fa-calendar-days"></i> {blog.date}</span>
                                                    <span><i className="fa-solid fa-comment-dots"></i>
                                                        <Link href="/blog-details">{blog.comments} {blog?.comments > 1 ? "Comments" : "Comment"}</Link>
                                                    </span>
                                                </div>
                                            )}
                                            {
                                                blog.buttonShow == true &&
                                                <div className="bd-blog-content">
                                                    <h3 className="bd-blog-title mb-15 underline">

                                                        {
                                                            blog?.buttonLink == true ?
                                                                <>
                                                                    <Link href="/blog/blog-details">{blog.title}</Link>
                                                                </>
                                                                :
                                                                <>
                                                                    {
                                                                        blog.daynamicLink == true &&
                                                                        <Link href={`/blog/blog-details/${blog.id}`}>{blog.title}</Link>
                                                                    }
                                                                </>
                                                        }
                                                    </h3>
                                                    <p>{blog.description}</p>
                                                    {
                                                        blog?.buttonLink == true ?
                                                            <>
                                                                <div className="bd-blog-btn">
                                                                    <Link href="/blog/blog-details" className="bd-btn btn-outline-primary">Read More</Link>
                                                                </div>
                                                            </>
                                                            :
                                                            <>
                                                                {
                                                                    blog.daynamicLink == true &&
                                                                    <div className="bd-blog-btn">
                                                                        <Link href={`/blog/blog-details/${blog.id}`} className="bd-btn btn-outline-primary">Read More</Link>
                                                                    </div>
                                                                }
                                                            </>
                                                    }
                                                </div>
                                            }
                                        </article>
                                    </div>
                                ))}
                            </div>
                            {/* -- pagination style -- */}
                            <BasicPagination />
                            {/* -- pagination style end -- */}
                        </div>
                        <div className="col-xxl-4 col-xl-4 col-xxl-4 col-lg-4">
                            <BlogSidebar />
                        </div>
                    </div>
                </div>
            </section>
            {/* -- blog area end -- */}
        </>
    );
};

export default BlogMain;