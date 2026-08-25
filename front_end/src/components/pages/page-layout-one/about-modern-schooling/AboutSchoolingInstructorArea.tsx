import instructorsData from '@/data/instructor-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const AboutSchoolingInstructorArea = () => {
    return (
        <>
            <section className="bd-instructor-area section-space theme-bg-05 fix">
                <div className="container">
                    <div className="row gy-30 justify-content-between align-items-center section-title-space">
                        <div className="col-xl-6 col-lg-6">
                            <div className="bd-section-title-wrapper">
                                <span className="bd-section-subtitle">Team</span>
                                <h2 className="bd-section-title">Meet Our Leadership</h2>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-6">
                            <div className="bd-instructor-btn text-md-end">
                                <Link className="bd-btn btn-outline-border-primary" href="/instructor">View More</Link>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {instructorsData.slice(12, 16).map((instructor, index) => (
                            <div className="col-xl-3 col-lg-3 col-md-6 col-sm-6" key={index}>
                                <div className="bd-instructor-wrapper style-four">
                                    <div className="bd-instructor-item">
                                        <div className="bd-instructor-thumb-wrapper">
                                            <div className="bd-instructor-thumb">
                                                <Link href="/instructor/instructor-details">
                                                    <Image src={instructor.image} style={{ width: 'auto', height: 'auto' }} alt="image" />
                                                </Link>
                                            </div>
                                            <div className="bd-instructor-social theme-social has-white circle text-center">
                                                <ul className="social-icon-list">
                                                    <li className="bd-icon-1">
                                                        <Link href={instructor.socialLinks?.facebook || '#'}>
                                                            <i className="fa-brands fa-facebook-f"></i>
                                                        </Link>
                                                    </li>
                                                    <li className="bd-icon-2">
                                                        <Link href={instructor?.socialLinks?.twitter || '#'}>
                                                            <i className="fa-brands fa-x-twitter"></i>
                                                        </Link>
                                                    </li>
                                                    <li className="bd-icon-3">
                                                        <Link href={instructor?.socialLinks?.linkedin || '#'}>
                                                            <i className="fa-brands fa-linkedin-in"></i>
                                                        </Link>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div className="bd-instructor-info mt-15">
                                            <h6 className="name underline">
                                                <Link href="/instructor/instructor-details">{instructor.name}</Link>
                                            </h6>
                                            <span>{instructor.role}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
        </>
    );
};

export default AboutSchoolingInstructorArea;