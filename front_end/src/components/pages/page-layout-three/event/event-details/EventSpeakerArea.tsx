import CoursesInstructoreArea from '@/components/common/course-section/CoursesInstructoreArea';
import instructorsData from '@/data/instructor-data';
import React from 'react';

const EventSpeakerArea = () => {
    return (
        <>
            <section className="bd-event-speaker-area section-space-bottom">
                <div className="container">
                    <div className="row">
                        <div className="col-xl-6">
                            <div className="bd-section-wrapper section-title-space">
                                <h2 className="bd-section-title">Event Speakers</h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            instructorsData.slice(0, 4).map((instructor) => (
                                <div key={instructor.id} className="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <CoursesInstructoreArea instructor={instructor} />
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
        </>
    );
};

export default EventSpeakerArea;