"use client";
import React, { useState, useEffect } from 'react';
import LogoImg from '../../../../public/assets/images/logo/logo.svg';
import lessonPrevImg from '../../../../public/assets/images/course/lesson-prev.webp';
import Image from 'next/image';
import LessonTabSection from './LessonTabSection';
import LessonAccordionItem from './LessonAccordionItem';
import courseContentData from '@/data/header-menu/course-content-data';
import { Tab } from 'bootstrap';
import Link from 'next/link';

const CoursesLassonMain = () => {
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [, setIsCollapsed] = useState(false);

    useEffect(() => {
        // Initialize Bootstrap Tabs, checking if the element exists
        const tabElement = document.querySelector('#pills-tabTwo');
        if (tabElement) {
            new Tab(tabElement).show();
        }

        const handleResize = () => {
            if (window.innerWidth > 0 && window.innerWidth <= 1199) {
                setIsCollapsed(true);
            } else {
                setIsCollapsed(false);
            }
        };

        handleResize(); 
        window.addEventListener('resize', handleResize);

        return () => {
            window.removeEventListener('resize', handleResize);
        };
    }, []);

    const toggleSidebar = () => {
        setIsSidebarOpen((prev) => !prev);
    };

    const closeSidebar = () => {
        setIsSidebarOpen(false);
    };

    return (
        <>
            {/* -- course lesson area start -- */}
            <section className="bd-lesson-area p-relative">
                <div className="bd-lesson-wrapper">
                    <div className={`bd-lesson-content ${isSidebarOpen ? 'collapsed' : ''}`}>
                        <div className="bd-lesson-logo">
                            <Link href="/">
                                <Image src={LogoImg} alt="logo" />
                            </Link>
                        </div>
                        <h2 className="title">Course Content</h2>
                        <div className="accordion-common-style accordion-transparent">
                            <div className="accordion" id="accordionExample">
                                {courseContentData.map((section, index) => (
                                    <LessonAccordionItem
                                        key={section.id}
                                        section={section}
                                        index={index}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>
                    <div className={`app-offcanvas-overlay ${isSidebarOpen ? 'overlay-open' : ''}`} onClick={closeSidebar}></div>
                    <div className={`bd-lesson-player ${isSidebarOpen ? 'collapsed' : ''}`}>
                        <div className="bd-lesson-video-wrap">
                            <div className="bd-lesson-video-title-wrap">
                                <div className="bd-lesson-video-title-wrap-left">
                                    <button type='button' onClick={toggleSidebar}>
                                        <i className="fa-solid fa-arrow-left"></i>
                                    </button>
                                    <span>Complete Guide to Web Development: Beginner to Advanced</span>
                                </div>
                                <div className="bd-lesson-video-title-wrap-right">
                                    <Link href="/">
                                        <i className="fas fa-times"></i>
                                    </Link>
                                </div>
                            </div>
                            <Image src={lessonPrevImg} style={{width:"100%", height:"auto"}} alt="Lesson Preview" priority/>
                        </div>
                        <div className="bd-lesson-about">
                            <div className="bd-lesson-next-prev-button">
                                <button className="prev-button"><i className="fa-solid fa-arrow-left"></i></button>
                                <button className="next-button" title="Setting up your development environment">
                                    <i className="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                            <LessonTabSection />
                        </div>
                    </div>
                </div>
            </section>
            {/* -- course lesson area end -- */}
        </>
    );
};

export default CoursesLassonMain;

