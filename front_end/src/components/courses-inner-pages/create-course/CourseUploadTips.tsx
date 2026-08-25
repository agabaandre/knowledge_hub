import React from 'react';

const CourseUploadTips = () => {
    return (
        <>
            <div className="bd-new-course-info sidebar-sticky">
                <h3 className="bd-new-course-info-title mb-20">Course Upload Tips</h3>
                <ul className="bd-new-course-info-list">
                    <li><span className="icon"><i className="fas fa-check"></i></span> Set the Course Price option or make
                        it free.</li>
                    <li><span className="icon"><i className="fas fa-check"></i></span> Standard size for the course
                        thumbnail is 700x430.</li>
                    <li><span className="icon"><i className="fas fa-check"></i></span> Video section controls the course
                        overview video.</li>
                    <li><span className="icon"><i className="fas fa-check"></i></span> Course Builder is where you create
                        &amp; organize a course.
                    </li>
                    <li><span className="icon"><i className="fas fa-check"></i></span> Add Topics in the Course Builder
                        section to create lessons,
                        quizzes, and assignments.</li>
                    <li><span className="icon"><i className="fas fa-check"></i></span> Prerequisites refer to the
                        fundamental courses that must be
                        completed before taking this particular course.</li>
                    <li><span className="icon"><i className="fas fa-check"></i></span> Information from the Additional Data
                        section appears on the
                        course single page.</li>
                </ul>
            </div>
        </>
    );
};

export default CourseUploadTips;