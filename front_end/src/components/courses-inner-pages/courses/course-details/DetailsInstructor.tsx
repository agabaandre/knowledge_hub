import Link from 'next/link';
import React from 'react';
import avatarImg from '../../../../../public/assets/images/avatar/avatar.webp';
import Image from 'next/image';

const DetailsInstructor = () => {
    return (
        <>
            <div className="bd-course-instructors mb-30">
                <h3 className="bd-course-details-content-title">Instructors</h3>
                <div className="bd-course-instructors-content">
                    <div className="thumb">
                        <Link href="#"><Image src={avatarImg} alt="Instructor image" /></Link>
                    </div>
                    <div className="bd-course-instructors-info">
                        <div className="mb--5">
                            <h6 className="name"><Link href="#">John Doe</Link></h6>
                            <span className="designation">Senior Full-Stack Developer, Topylo</span>
                        </div>
                        <div className="bd-course-instructors-rating mb--5">
                            <div className="icon"><i className="fas fa-star"></i></div>
                            <span>4.9 (120 reviews)</span>
                        </div>
                        <div className="bd-course-instructors-course mb-15">
                            <div className="item">
                                <i className="fas fa-desktop"></i>{" "}
                                <span>5 Courses</span>
                            </div>
                            <div className="item">
                                <i className="far fa-user-friends"></i>{" "}
                                <span>105,624 Students</span>
                            </div>
                        </div>
                        <div className="theme-social">
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
                <div className="bd-course-instructors-bio mt-15">
                    <p>With over 10 years of experience in web development, I have contributed to various
                        projects ranging from small business websites to complex enterprise applications. As a
                        Senior Full-Stack Developer at Topylo, I specialize in creating seamless user experiences
                        using modern web technologies such as React, Node.js, and Tailwind CSS. My goal is to help
                        you master the skills needed to succeed in the tech industry.</p>
                </div>
            </div>
        </>
    );
};

export default DetailsInstructor;