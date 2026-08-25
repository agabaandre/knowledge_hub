import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import CoursesInstructoreArea from '@/components/common/course-section/CoursesInstructoreArea';
import instructorsData from '@/data/instructor-data';
import React from 'react';

const InstructorMainArea = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Instructor' />
            {/* -- instructor page area start -- */}
            <div className="bd-instructor-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        {
                            instructorsData.slice(16, 24).map((instructor) => (
                                <div key={instructor.id} className="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <CoursesInstructoreArea instructor={instructor} />
                                </div>
                            ))
                        }
                    </div>
                </div>
            </div>
            {/* -- instructor page area end -- */}
        </>
    );
};

export default InstructorMainArea;