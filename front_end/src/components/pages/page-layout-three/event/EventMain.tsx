import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import EventSingleCard from '@/components/common/EventSingleCard';
import BasicPagination from '@/components/elements/pagination/BasicPagination';
import { eventData } from '@/data/events-data';
import React from 'react';

const EventMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Event' />

            {/* -- event grid area start -- */}
            <section className="bd-event-grid-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        {
                            eventData.slice(6, 12).map((event) => (
                                <div className="col-xl-4 col-lg-6 col-md-6" key={event.id}>
                                    <EventSingleCard event={event} />
                                </div>
                            ))
                        }
                        <BasicPagination />
                    </div>
                </div>
            </section>
            {/* -- event grid area end -- */}
        </>
    );
};

export default EventMain;