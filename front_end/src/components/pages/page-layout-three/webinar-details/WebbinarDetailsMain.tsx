"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import Image from 'next/image';
import React from 'react';
import WebinarVideoImg from '../../../../../public/assets/images/event/webinar-video-thumb.webp';
import EventSidebarTicketPrice from '../event/event-details/EventSidebarTicketPrice';
import NumberOfTicketsSection from '../event/event-details/NumberOfTicketsSection';
import instructorsData from '@/data/instructor-data';
import CoursesInstructoreArea from '@/components/common/course-section/CoursesInstructoreArea';
import { useVideoModal } from '@/contextApi/VideoProvider';

const WebbinarDetailsMain = () => {
    const { playVideo } = useVideoModal();
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Webinar Details' />
            {/* -- webinar details area start -- */}
            <section className="bd-webinar-details-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-xxl-8 col-xl-8 col-lg-7 col-md-12">
                            <div className="bd-webinar-content">
                                <div className="bd-webinar-video-thumb mb-30 p-relative">
                                    <Image src={WebinarVideoImg} style={{ width: "100%", height: "auto" }} alt="Education Webinar" />
                                    <button type='button' onClick={() => playVideo("HKk4oLIzhhM", "youtube")} className="bd-video-btn pos-center popup-video has-bg">
                                        <span className="icon"><i className="fa-solid fa-play"></i></span>
                                    </button>
                                </div>
                                <div className="bd-event-details-content">
                                    <div className="bd-even-details-heading mb-30">
                                        <h2 className="bd-course-details-title">Webinar: Strategies for Advancing Your Career in
                                            Education
                                        </h2>
                                    </div>
                                    <div className="bd-event-details-content mb-30">
                                        <h3 className="bd-details-content-title">Overview</h3>
                                        <p className="description">This webinar is designed for educators at every stage of their
                                            careers.
                                            Discover the
                                            strategies that will help you grow professionally and adapt to evolving educational
                                            challenges. From
                                            classroom management techniques to leveraging educational technology, this event covers
                                            all
                                            you need to
                                            succeed in modern teaching environments.</p>
                                    </div>
                                    <div className="bd-event-details-content mb-30">
                                        <h3 className="bd-details-content-title">Learning Outcomes</h3>
                                        <div className="bd-post-details-list">
                                            <ul>
                                                <li>Master classroom management techniques to create effective learning environments
                                                </li>
                                                <li>Understand how to integrate modern educational technologies into teaching</li>
                                                <li>Enhance student engagement through innovative teaching methods</li>
                                                <li>Collaborate and network with fellow educators and experts in the field</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div className="bd-event-details-content mb-30">
                                        <h3 className="bd-details-content-title">Requirements</h3>
                                        <div className="bd-post-details-list">
                                            <ul>
                                                <li>No prior teaching experience is necessary</li>
                                                <li>A passion for advancing your skills in education</li>
                                                <li>Access to a computer with an internet connection</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div className="bd-event-details-content">
                                        <h3 className="bd-details-content-title">Speakers</h3>
                                        <div className="row g-30">
                                            {
                                                instructorsData.slice(24, 26).map((instructor) => (
                                                    <div key={instructor.id} className="col-xl-6 col-lg-6 col-md-6">
                                                        <CoursesInstructoreArea instructor={instructor} />
                                                    </div>
                                                ))
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xxl-4 col-xl-4 col-lg-5 col-md-12">
                            <div className="bd-event-sidebar-wrapper sidebar-right sidebar-sticky">
                                <EventSidebarTicketPrice />
                                <NumberOfTicketsSection />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- webinar details area end -- */}

        </>
    );
};

export default WebbinarDetailsMain;