import EventSingleCard from '@/components/common/EventSingleCard';
import { eventData } from '@/data/events-data';
import React from 'react';

const UpcomingEventArea = () => {
    return (
        <>
            {/* -- upcoming event area start -- */}
            <section className="bd-upcoming-event-area section-space-bottom">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-7">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <h2 className="bd-section-title">Upcoming Event</h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            eventData.slice(6, 9).map((event) => (
                                <div className="col-xl-4 col-lg-6 col-md-6" key={event.id}>
                                    <EventSingleCard event={event} />
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- upcoming event area end -- */}
        </>
    );
};

export default UpcomingEventArea;