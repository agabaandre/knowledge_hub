import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import eventDetailsThumb from '../../../../../../public/assets/images/event/event-details-thumb.webp';
import React from 'react';
import Image from 'next/image';
import EventDetailsLocation from './EventDetailsLocation';
import EventSidebarTicketPrice from './EventSidebarTicketPrice';
import NumberOfTicketsSection from './NumberOfTicketsSection';
import sopnsorLogo from '../../../../../../public/assets/images/brand/sopnsor-logo-1.webp';
import sopnsorLogoTwo from '../../../../../../public/assets/images/brand/sopnsor-logo-2.webp';
import EventSpeakerArea from './EventSpeakerArea';
import UpcomingEventArea from './UpcomingEventArea';
import { eventData } from '@/data/events-data';

const EventDetailsMain = ({ id }: { id: number }) => {
    const event = eventData.find((item) => item.id == id);
    return (
        <>
            <Breadcrumbs breadcrumbTitle={`Masterclass: ${event?.title}`} flexClass="justify-content-start" textPosition="text-start"/>
            {/* -- event details area start -- */}
            <section className="bd-event-details-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-xl-12">
                            <div className="bd-event-main-thumb">
                                <Image src={eventDetailsThumb} alt="image" />
                            </div>
                        </div>
                        <div className="col-xxl-8 col-xl-8 col-lg-7 col-md-12">
                            <div className="bd-even-details-content">
                                <div className="bd-even-details-heading mb-30">
                                    <h2 className="bd-course-details-title">{`Masterclass: ${event?.title}`}</h2>
                                </div>
                                <div className="bd-event-details-content mb-30">
                                    <h3 className="bd-details-content-title">Overview</h3>
                                    <p className="description">Join this masterclass to explore the pathways to a thriving career in
                                        education.
                                        From classroom management to leveraging technology for effective teaching, this event covers
                                        the essentials for aspiring and current educators.
                                        Learn from experienced professionals and gain practical insights to enhance your teaching
                                        strategies.</p>
                                </div>
                                <div className="bd-event-details-content mb-30">
                                    <h3 className="bd-details-content-title">Learning Outcomes</h3>
                                    <div className="bd-post-details-list">
                                        <ul>
                                            <li>Develop key teaching skills and strategies</li>
                                            <li>Understand modern teaching tools and technologies</li>
                                            <li>Enhance your classroom management capabilities</li>
                                            <li>Network with fellow educators and industry experts</li>
                                        </ul>
                                    </div>
                                </div>
                                <div className="bd-event-details-content mb-30">
                                    <h3 className="bd-details-content-title">Requirements</h3>
                                    <div className="bd-post-details-list">
                                        <ul>
                                            <li>No prior teaching experience required</li>
                                            <li>A strong interest in the education field</li>
                                            <li>Access to a computer with an internet connection</li>
                                        </ul>
                                    </div>
                                </div>
                                <EventDetailsLocation />
                            </div>
                        </div>
                        <div className="col-xxl-4 col-xl-4 col-lg-5 col-md-12">
                            <div className="bd-event-sidebar-wrapper bd-event-sidebar-top sidebar-sticky">
                                <EventSidebarTicketPrice />
                                <NumberOfTicketsSection />
                                <div className="bd-event-sidebar">
                                    <h6 className="title mb-15">Sponsored by</h6>
                                    <div className="bd-event-sponsor">
                                        <Image src={sopnsorLogo} alt="image" />
                                        <Image src={sopnsorLogoTwo} alt="image" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- event details area end -- */}
            {/* -- event speaker area start -- */}
            <EventSpeakerArea />
            {/* -- event speaker area end -- */}
            <UpcomingEventArea />
        </>
    );
};

export default EventDetailsMain;