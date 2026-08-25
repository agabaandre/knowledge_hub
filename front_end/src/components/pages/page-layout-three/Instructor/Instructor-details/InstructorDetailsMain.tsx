
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import React from 'react'
import Image from 'next/image';
import Link from 'next/link';
import InstructorProgress from './InstructorProgress';
import ExperienceSection from './ExperienceSection';
import MyCoursesSection from './MyCoursesSection';
import instructorsData from '@/data/instructor-data';

const InstructorDetailsMain = ({ id }: { id: number }) => {
    const instructor = instructorsData.find((item) => item.id == id);

    return (
        <>
            <Breadcrumbs breadcrumbTitle='Instructor Details' />
            {/* -- instructor-details area start -- */}
            <section className="bd-instructor-details-area section-space">
                <div className="container">
                    <div className="row g-30">
                        <div className="col-xl-4 col-lg-4 col-md-12">
                            <div className="bd-instructor-details-info sidebar-left sidebar-sticky">
                                <div className="bd-instructor-details-thumb mb-20">
                                    {
                                        instructor?.image && <Image src={instructor?.image} alt="Alex Morgan" />
                                    }
                                </div>
                                <div className="bd-instructor-info">
                                    <h3 className="name mb--5">{instructor?.name}</h3>
                                    <span className="designation mb--5 d-block">{instructor?.title}</span>
                                    <div className="bd-instructor-rating mb-15">
                                        <div className="rating-star rating-spacing-2">
                                            <i className="fas fa-star"></i>
                                            <i className="fas fa-star"></i>
                                            <i className="fas fa-star"></i>
                                            <i className="fas fa-star"></i>
                                            <i className="fas fa-star"></i>
                                        </div>
                                        <span className="rating-count"> (25 Reviews)</span>
                                    </div>
                                    <div className="bd-instructor-info-list mb-20">
                                        <ul>
                                            <li><Link href="mailto:alex.morgan@example.com"><i className="fal fa-envelope"></i>
                                                alex.morgan@example.com</Link></li>
                                            <li><Link href="tel:+123456789"><i className="far fa-phone-alt"></i> +(123) 456-789</Link>
                                            </li>
                                            <li><Link href="#"><i className="far fa-map-marker-alt"></i> 123 Learning St, Knowledge City, KC88 7ZX</Link></li>
                                        </ul>
                                    </div>
                                    <div className="theme-social">
                                        <ul className="social-icon-list">
                                            <li><Link href="https://www.facebook.com/" target="_blank"><i className="fa-brands fa-facebook-f"></i></Link></li>
                                            <li><Link href="https://x.com/" target="_blank"><i className="fa-brands fa-x-twitter"></i></Link></li>
                                            <li><Link href="https://www.linkedin.com/feed/" target="_blank"><i className="fa-brands fa-linkedin-in"></i></Link></li>
                                            <li><Link href="https://www.instagram.com/" target="_blank"><i className="fa-brands fa-instagram"></i></Link></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-8 col-lg-8 col-md-12">
                            <div className="bd-instructor-details-content">
                                <div className="bd-instructor-feature-box mb-30">
                                    <h3 className="bd-instructor-details-title">Biography</h3>
                                    <p className="bd-instructor-details-desc">Alex Morgan is a Data Science Expert and an
                                        instructor with over 8 years of
                                        experience in the industry. He has trained thousands of students through his online
                                        courses, helping them excel in fields such as Data Science, Machine Learning, and
                                        Artificial Intelligence. As
                                        the founder of LearnDS, {`Alex's`} mission is to provide top-quality education at
                                        affordable prices, breaking barriers
                                        to entry in the tech world.
                                    </p>
                                </div>
                                <div className="bd-instructor-feature-box mb-30">
                                    <h3 className="bd-instructor-details-title">Skills</h3>
                                    <InstructorProgress />
                                </div>
                                <ExperienceSection />
                                <div className="bd-instructor-feature-box">
                                    <h3 className="bd-instructor-details-title">My Courses</h3>
                                    <MyCoursesSection />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- instructor-details area end -- */}
        </>
    );
};

export default InstructorDetailsMain;