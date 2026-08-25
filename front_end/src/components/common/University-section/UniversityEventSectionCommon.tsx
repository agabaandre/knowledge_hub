import React from 'react';
import eventThumb from '../../../../public/assets/images/event/event-thumb-06.webp';
import shapeStar from '../../../../public/assets/images/shape/star.webp';
import Image from 'next/image';
import Link from 'next/link';
import { eventData } from '@/data/events-data';

const UniversityEventSectionCommon = () => {
    return (
        <>
            {/* -- event area start -- */}
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-7">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">our events</span>
                                <h2 className="bd-section-title">Upcoming <span className="down-mark-line">Events</span></h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gx-0 gy-30">
                        <div className="col-xl-7">
                            <div className="bd-event-wrapper style-two">
                                {eventData.slice(0,3).map((event) => (
                                    <div key={event.id} className={`bd-event-item ${event.isActive ? "has-active" : ""}`}>
                                        <div className="bd-event-date">
                                            <h3>{event.date}</h3>
                                            <p>{event.monthYear}</p>
                                        </div>
                                        <div className="bd-event-content">
                                            <ul>
                                                <li> <i className="fa-regular fa-location-dot"></i> {event.location}</li>
                                                <li> <i className="fa-regular fa-clock"></i> {event.time} </li>
                                            </ul>
                                            <h5 className="bd-event-title underline"><Link href={`/event/event-details/${event.id}`}>{event.title}</Link></h5>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                        <div className="col-xl-5">
                            <div className="bd-event-thumb style-two">
                                <Image src={eventThumb} alt="image" priority/>
                            </div>
                        </div>
                    </div>
                    <div className="event-shape rotateme d-none d-xxl-block">
                        <Image src={shapeStar} style={{ width: "100%", height: "auto" }} priority alt="shape" />
                    </div>
                </div>
            {/* -- event area end -- */}
        </>
    );
};

export default UniversityEventSectionCommon;