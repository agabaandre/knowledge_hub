import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import programData from '@/data/programe-data';
import React from 'react';
import CurriculumArea from './CurriculumArea';
import CourseFlowDiagram from './CourseFlowDiagram';
import ProgramSidebarWidget from './ProgramSidebarWidget';
import { eventData } from '@/data/events-data';
import EventSingleCard from '@/components/common/EventSingleCard';

const ProgramDetailsMain = ({ programId }: { programId: number }) => {
    const program = programData.find((item) => item.id == programId);
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Program Details University' />
            {/* -- program details area start -- */}
            <section className="bd-program-details-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-xxl-8 col-xl-8 col-lg-8">
                            <div className="bd-program-details-wrapper">
                                <h2 className="bd-course-details-title mb-30">{program?.title}</h2>
                                <div className="bd-course-details-content mb-30">
                                    <p className="description">This comprehensive course covers all aspects of web development from the
                                        basics of HTML, CSS, and JavaScript to advanced topics like React, Node.js, and database
                                        integration. Whether {`you're`} a complete beginner or an experienced developer looking to hone
                                        your skills, this course has everything you need to master web development. Learn best
                                        practices and gain hands-on experience with real-world projects.</p>
                                </div>
                                <div className="bd-course-details-content mb-30">
                                    <h3 className="bd-course-details-content-title">Requirements</h3>
                                    <div className="bd-post-details-list">
                                        <ul>
                                            <li>
                                                Basic understanding of computer usage and internet browsing
                                            </li>
                                            <li>
                                                No prior programming knowledge is required
                                            </li>
                                            <li>
                                                A computer with access to the internet
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <CurriculumArea />
                                <CourseFlowDiagram />
                            </div>
                        </div>
                        <div className="col-xxl-4 col-xl-4 col-lg-4">
                            <ProgramSidebarWidget />
                        </div>
                    </div>
                </div>
            </section>
            {/* -- program details area end -- */}
            {/* -- upcoming event area start -- */}
            <section className="bd-upcoming-event-area section-space-bottom fix">
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
                            eventData.slice(3,6).map((event) => (
                                <div className="col-xl-4 col-lg-4 col-md-6" key={event.id}>
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

export default ProgramDetailsMain;