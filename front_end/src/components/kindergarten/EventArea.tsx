import { eventData } from '@/data/events-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import shape1 from '../../../public/assets/images/shape/land2.png';
import shape2 from '../../../public/assets/images/shape/kides-1.png';
import shape3 from '../../../public/assets/images/shape/kides-2.png';

const EventArea = () => {
    return (
        <>
            {/* -- event area start -- */}
            <section className="bd-event-area section-space p-relative">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">our events</span>
                                <h2 className="bd-section-title">Upcoming Events</h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30 justify-content-center">
                            {
                                eventData.slice(17, 20).map((item) => (
                                    <div className="col-xl-10 col-lg-12" key={item.id}>
                                        <div className="bd-event-wrapper style-three">
                                            <div className="bd-event-item">
                                                <div className="bd-event-thumb">
                                                    <Link href={`/event/event-details/${item.id}`}>
                                                        {item.image && <Image style={{width:"100%", height:"auto"}} src={item.image} alt="image" />}
                                                    </Link>
                                                </div>
                                                <div className="bd-event-content">
                                                    <div className="bd-content-inner">
                                                        <h5 className="bd-event-title underline mb-20">
                                                            <Link href={`/event/event-details/${item.id}`}>{item.title}</Link>
                                                        </h5>
                                                        <div className="bd-event-meta">
                                                            <div className="bd-event-meta-list">
                                                                <span><i className="fa-regular fa-calendar-days"></i> {item.date}</span>
                                                            </div>
                                                            <div className="bd-event-meta-list">
                                                                <span><i className="fa-regular fa-clock"></i> {item.time}</span>
                                                            </div>
                                                            <div className="bd-event-meta-list">
                                                                <span><i className="fa-regular fa-location-dot"></i> Beilly Tower</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="bd-event-btn">
                                                        <Link className="bd-btn btn-outline-primary" href={`/event/event-details/${item.id}`}>View Details</Link>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            }
                        </div>
                    </div>
                <div className="bd-event-shape">
                    <div className="shape-1"><Image style={{width:"100%", height:"auto"}} src={shape1} alt="shape" /></div>
                    <div className="shape-2"><Image style={{width:"100%", height:"auto"}} src={shape2} alt="shape" /></div>
                    <div className="shape-3"><Image style={{width:"100%", height:"auto"}} src={shape3} alt="shape" /></div>
                </div>
            </section>
            {/* -- event area end -- */}
        </>
    );
};

export default EventArea;