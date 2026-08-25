"use client"
import Image from 'next/image';
import React from 'react';
import courseVideoImg from '../../../../../public/assets/images/course/course-video.webp';
import { ICourse } from '@/interFace/interFace';
import { useDispatch } from 'react-redux';
import { cart_product } from '@/redux/slices/cartSlice';
import { wishlist_product } from '@/redux/slices/wishlistSlice';
import { useVideoModal } from '@/contextApi/VideoProvider';
interface ICourseProps {
    course: ICourse
}

const CourseSidebarWidget = ({ course }: ICourseProps) => {
    const { playVideo } = useVideoModal();
    const dispatch = useDispatch();

    const handleAddToCart = (product: ICourse) => {
        if (product) {
            dispatch(cart_product(product))
        }
    }
    const handleAddToWishlist = (product: ICourse) => {
        if (product) {
            dispatch(wishlist_product(product))
        }
    }
    
    return (
        <>
            <div className="bd-course-sidebar-widget sidebar-right sidebar-sticky">
                <div className="bd-course-sidebar-widget-thumb mb-20 p-relative">
                    <Image style={{ width: "100%", height: "auto" }} src={courseVideoImg} alt="image" priority/>
                    <div className="thumb-btn">
                        <button type='button' onClick={() => playVideo("HKk4oLIzhhM", "youtube")} className="bd-video-btn popup-video has-bg">
                            <span className="icon"><i className="fa-solid fa-play"></i></span>
                        </button>
                    </div>
                </div>
                <div className="bd-course-sidebar-widget-price mb-20">
                    <div className="bd-course-price">
                        <span className="current-price">{`$${course.price ? course.price : 1525}.00`} </span>
                        <span className="old-price">{`$${course.discount ? course.discount : 1925}.00`}</span>
                    </div>
                </div>
                <div className="bd-course-sidebar-widget-list mb-20">
                    <ul>
                        <li>
                            <div className="icon">
                                <i className="fas fa-filter"></i>
                                <span>Level</span>
                            </div>
                            <div className="video-corse-info">
                                <span>Beginners</span>
                            </div>
                        </li>
                        <li>
                            <div className="icon">
                                <i className="fas fa-desktop"></i>
                                <span>Lectures</span>
                            </div>
                            <div className="video-corse-info">
                                <span>8 Lectures</span>
                            </div>
                        </li>
                        <li>
                            <div className="icon">
                                <i className="far fa-clock"></i>
                                <span>Duration</span>
                            </div>
                            <div className="video-corse-info">
                                <span>1h 30m 12s</span>
                            </div>
                        </li>
                        <li>
                            <div className="icon">
                                <i className="fas fa-th-list"></i>
                                <span>Category</span>
                            </div>
                            <div className="video-corse-info">
                                <span>Data Science</span>
                            </div>
                        </li>
                        <li>
                            <div className="icon">
                                <i className="fas fa-globe"></i>
                                <span>Language</span>
                            </div>
                            <div className="video-corse-info">
                                <span>English</span>
                            </div>
                        </li>
                        <li>
                            <div className="icon">
                                <i className="fas fa-bookmark"></i>
                                <span>Access</span>
                            </div>
                            <div className="video-corse-info">
                                <span>Full Lifetime</span>
                            </div>
                        </li>
                        <li>
                            <div className="icon">
                                <i className="fas fa-award"></i>
                                <span>Certificate</span>
                            </div>
                            <div className="video-corse-info">
                                <span>Yes</span>
                            </div>
                        </li>
                        <li>
                            <div className="icon">
                                <i className="fas fa-file-alt"></i>
                                <span>Recourse</span>
                            </div>
                            <div className="video-corse-info">
                                <span>5 Downloadable Files</span>
                            </div>
                        </li>
                    </ul>
                </div>
                <div className="bd-course-sidebar-widget-btn d-flex-between flex-wrap gap-15">
                    <button onClick={() => handleAddToCart(course)} className="bd-btn btn-primary w-100"><span className="left-icon"><i
                        className="fal fa-shopping-cart"></i></span> Add to
                        cart</button>
                    <button onClick={() => handleAddToWishlist(course)} className="bd-btn btn-outline-primary w-100"><span className="left-icon"><i
                        className="far fa-heart"></i></span> Add to Wishlist</button>
                </div>
            </div>
        </>
    );
};

export default CourseSidebarWidget;