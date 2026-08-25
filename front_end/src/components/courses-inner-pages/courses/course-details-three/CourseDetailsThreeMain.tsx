'use client';

import CourseDetailsBreadcrumb from '@/components/common/Breadcrumb/CourseDetailsBreadcrumb';
import React, { useEffect, useState } from 'react';
import Link from 'next/link';
import CourseCommonDetailsContent from '../course-details-two/CourseCommonDetailsContent';
import CourseContentDropdown from '../course-details-two/CourseContentDropdown';
import WhatYouLearnTwo from '../course-details-two/WhatYouLearnTwo';
import DetailsCourseInstructorTwo from '../course-details-two/DetailsCourseInstructorTwo';
import CourseRating from '../course-details-two/CourseRating';
import CommonCourseReview from '@/components/common/course-details/CommonCourseReview';
import CommonCourseFeature from '@/components/common/course-details/CommonCourseFeature';
import CourseReviewForm from '@/form/CourseReviewForm';

const sections = [
    { id: 'overview', label: 'Summary' },
    { id: 'courseContent', label: 'Content' },
    { id: 'details', label: 'Outline' },
    { id: 'intructor', label: 'Instructor' },
    { id: 'review', label: 'Review' },
];

const CourseDetailsThreeMain = () => {
    const [activeSection, setActiveSection] = useState('overview');

    useEffect(() => {
        const handleScroll = () => {
            let currentSection = 'overview';
            sections.forEach(({ id }) => {
                const sectionElement = document.getElementById(id);
                if (sectionElement) {
                    const { top } = sectionElement.getBoundingClientRect();
                    if (top < 150) currentSection = id;
                }
            });
            setActiveSection(currentSection);
        };

        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const handleClick = (id:string) => {
        const section = document.getElementById(id);
        if (section) {
            window.scrollTo({
                top: section.offsetTop - 80,
                behavior: 'smooth',
            });
        }
    };

    return (
        <>
            <CourseDetailsBreadcrumb subtitle='Courses Details 03'
                courseBreadcrumbBuyShow={true}
                description="Learn the essential principles and techniques of graphic design. Master typography, layout, color theory, and visual branding to create stunning designs." />

            <section className="bd-course-details-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-xl-8 col-lg-8">
                            <div className="bd-course-menu mb-30">
                                <nav className="bd-course-menu-nav">
                                    <ul>
                                        {sections.map(({ id, label }) => (
                                            <li key={id} className={activeSection === id ? 'active' : ''}>
                                                <Link href={`#${id}`} onClick={(e) => {
                                                    e.preventDefault();
                                                    handleClick(id);
                                                }}>
                                                    {label}
                                                </Link>
                                            </li>
                                        ))}
                                    </ul>
                                </nav>
                            </div>
                            <div className="course-details-content">
                                <div id="overview"><CourseCommonDetailsContent /></div>
                                <div id="courseContent"><CourseContentDropdown /></div>
                                <div id="details"><WhatYouLearnTwo /></div>
                                <div id="intructor"><DetailsCourseInstructorTwo /></div>
                                <div id="review"><CourseRating /></div>
                                <div className="bd-course-feature-box mt-30 mb-30">
                                    <h3 className="bd-course-details-content-title">Course Review</h3>
                                    <CommonCourseReview />
                                </div>
                                <div className="bd-course-feature-box">
                                    <h3 className="bd-course-details-content-title">Write a Review</h3>
                                    <div className="bd-review-form">
                                        <div className="bd-review-form-rating mb-15">
                                            <p>Your email address will not be published. Required fields are marked *</p>
                                            <div className="bd-ratings-wrapper bd-ratings-wrapper-two rating-spacing-2">
                                                <i className="fa fa-star"></i>
                                                <i className="fa fa-star"></i>
                                                <i className="fa fa-star"></i>
                                                <i className="fa fa-star"></i>
                                                <i className="fa fa-star"></i>
                                                <input type="hidden" name="rating" value="5" />
                                            </div>
                                        </div>
                                        <CourseReviewForm />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-4 col-lg-4">
                            <div className="bd-course-sidebar-widget sidebar-sticky">
                                <CommonCourseFeature />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default CourseDetailsThreeMain;