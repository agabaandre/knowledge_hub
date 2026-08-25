"use client"
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import courseVideoImg from '../../../../../public/assets/images/course/course-video.webp';
import CommonCourseFeature from '@/components/common/course-details/CommonCourseFeature';
import { useVideoModal } from '@/contextApi/VideoProvider';

const CourseSidebarWidgetTwo: React.FC = () => {
    const { playVideo } = useVideoModal();
    return (
        <>
            <div className="bd-course-sidebar-widget sidebar-right sidebar-sticky course-sidebar-top">
                {/* Course Thumbnail with Video Button */}
                <div className="bd-course-sidebar-widget-thumb mb-20 p-relative">
                    <Image src={courseVideoImg} style={{ width: '100%', height: "auto" }} alt="Course video thumbnail" />
                    <div className="thumb-btn">
                        <button type='button' onClick={() => playVideo("HKk4oLIzhhM", "youtube")} className="bd-video-btn popup-video has-bg">
                            <span className="icon"><i className="fa-solid fa-play"></i></span>
                        </button>
                    </div>
                    <div className="bd-course-badge">
                        <Link className="bd-badge badge-danger" href="#">15% Off</Link>
                    </div>
                </div>

                {/* Course Category and Wishlist Button */}
                <div className="bd-course-sidebar-widget-cat d-flex-between mb-20">
                    <Link className="bd-badge badge-outline-primary badge-transparent" href="#">Marketing</Link>
                    <button className="bd-like-btn" type="button"><i className="fa-regular fa-heart"></i></button>
                </div>

                {/* Course Pricing and Discount Timer */}
                <div className="bd-course-sidebar-widget-price mb-20">
                    <div className="bd-course-price">
                        <span className="current-price">$1925.00</span>
                        <span className="old-price">$1925.00</span>
                    </div>
                    <div className="bd-discount-time">
                        <span><i className="fa-light fa-clock"></i> 3 Days left at this price!</span>
                    </div>
                </div>
                {/* Purchase Buttons */}
                <div className="bd-course-sidebar-widget-btn mb-20">
                    <Link href="/cart" className="bd-btn btn-primary w-100 mb-15">Add to cart</Link>
                    <Link href="/checkout" className="bd-btn btn-outline-primary w-100">Buy Now</Link>
                </div>
                <CommonCourseFeature />
            </div>
        </>
    );
};

export default CourseSidebarWidgetTwo;