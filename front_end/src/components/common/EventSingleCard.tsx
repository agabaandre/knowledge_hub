import { IEvent } from '@/interFace/interFace';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

interface IEventProps{
    event:IEvent
}
const EventSingleCard = ({ event }:IEventProps) => {
    return (
        <>
            <article className="bd-event-wrapper style-one">
                <div className="bd-event-item">
                    <div className="bd-event-thumb">
                        <Link href={`/event/event-details/${event.id}`}>
                            {event?.image && <Image src={event?.image} alt="image" />}
                        </Link>
                        <div className="bd-event-badge">
                            <span className="bd-circle-badge primary">{event?.date} <span className="subtitle">{event.monthYear}</span></span>
                        </div>
                    </div>
                    <div className="bd-event-content">
                        <h5 className="bd-event-title underline mb-15">
                            <Link href={`/event/event-details/${event.id}`}>{event?.title}</Link>
                        </h5>
                        <p className="bd-event-description">{event?.description}</p>
                        <div className="bd-event-divider"></div>
                        <div className="bd-event-meta d-flex-between">
                            <div className="bd-event-meta-list">
                                <span><i className="fa-regular fa-location-dot"></i>{event?.location}</span>
                            </div>
                            <div className="bd-event-meta-list">
                                <span><i className="fa-regular fa-clock"></i>{event?.time}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </>
    );
};

export default EventSingleCard;