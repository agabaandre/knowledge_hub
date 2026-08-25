import CoursesInstructoreArea from '@/components/common/course-section/CoursesInstructoreArea';
import instructorsData from '@/data/instructor-data';
import Link from 'next/link';
import React from 'react';

const AboutOnlineInstructor = () => {
    return (
        <>
            <section className="bd-instructor-area section-space primary-bg">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Instructor</span>
                                <h2 className="bd-section-title">
                                    Our Course <span className="down-mark-line">Instructor</span>
                                </h2>
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
                        <div className="bd-instructor-btn d-flex-center mt-50">
                            <Link className="bd-btn btn-outline-border-primary" href="/instructor">
                                View More
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default AboutOnlineInstructor;