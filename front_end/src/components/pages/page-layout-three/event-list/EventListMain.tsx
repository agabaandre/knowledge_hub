import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import BasicPagination from '@/components/elements/pagination/BasicPagination';
import { eventData } from '@/data/events-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const EventListMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Event List' />
            {/* -- event grid area start -- */}
            <section className="bd-event-grid-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-10">
                            {
                                eventData.slice(12, 17).map((item) => (
                                    <article className="bd-event-wrapper style-three" key={item.id}>
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
                                                    <div className="bd-event-meta d-flex align-items-center">
                                                        <div className="bd-event-meta-list">
                                                            <span><i className="fa-regular fa-calendar-days"></i> {item.date}</span>
                                                        </div>
                                                        <div className="bd-event-meta-list">
                                                            <span><i className="fa-regular fa-clock"></i> {item.time}</span>
                                                        </div>
                                                        <div className="bd-event-meta-list">
                                                            <span><i className="fa-regular fa-location-dot"></i> Online</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="bd-event-btn">
                                                    <Link className="bd-btn btn-outline-primary" href={`/event/event-details/${item.id}`}>View Details</Link>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                ))
                            }

                        </div>
                        <BasicPagination />
                    </div>
                </div>
            </section>
            {/* -- event grid area end -- */}
        </>
    );
};

export default EventListMain;