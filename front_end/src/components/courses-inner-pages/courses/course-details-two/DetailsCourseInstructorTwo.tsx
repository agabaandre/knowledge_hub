import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import avatarImg from '../../../../../public/assets/images/avatar/avatar.webp';

const DetailsCourseInstructorTwo = () => {
    return (
        <>
            <div className="bd-course-feature-box mt-30" id="intructor">
                <h3 className="bd-course-details-content-title">Course Instructor</h3>
                <div className="bd-course-instructors-content style-two">
                    <div className="thumb">
                        <Link href="#"><Image src={avatarImg} alt="Instructor image" /></Link>
                    </div>
                    <div className="bd-course-instructors-info mb-0">
                        <div className="mb-10">
                            <h6 className="name"><Link href="#">Jane Smith</Link></h6>
                            <span className="designation">Lead Graphic Designer, Topylo</span>
                        </div>
                        <div className="meta">
                            <div className="bd-course-instructors-rating">
                                <div className="icon"><i className="fas fa-star"></i></div>
                                <span>4.8 (250 reviews)</span>
                            </div>
                            <div className="bd-course-instructors-course">
                                <div className="item">
                                    <i className="fas fa-paint-brush"></i>{" "}
                                    <span>8 Courses</span>
                                </div>
                                <div className="item">
                                    <i className="far fa-user-friends"></i>{" "}
                                    <span>50,000 Students</span>
                                </div>
                            </div>
                        </div>
                        <div className="bd-course-instructors-bio mt-10 mb-15">
                            <p>Jane Smith is a highly experienced graphic designer with over 10 years of experience
                                in branding,
                                web design, and visual identity. She has worked with top brands across industries.
                            </p>
                        </div>
                        <div className="theme-social circle">
                            <ul className="social-icon-list">
                                <li><Link href="https://www.facebook.com/" target="_blank"><i className="fa-brands fa-facebook-f"></i></Link></li>
                                <li><Link href="https://x.com/" target="_blank"><i className="fa-brands fa-x-twitter"></i></Link></li>
                                <li><Link href="https://www.linkedin.com/feed/" target="_blank"><i className="fa-brands fa-linkedin-in"></i></Link></li>
                                <li><Link href="https://www.instagram.com/" target="_blank"><i className="fa-brands fa-instagram"></i></Link></li>
                                <li><Link href="https://www.behance.net/" target="_blank"><i className="fa-brands fa-behance"></i></Link></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default DetailsCourseInstructorTwo;