import EventDetailsMain from '@/components/pages/page-layout-three/event/event-details/EventDetailsMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Event Details - Education & Online Courses React NextJs Template",
};

const EventDetails = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <EventDetailsMain id={1} />
                </main>
            </Wrapper>
        </>
    );
};

export default EventDetails;