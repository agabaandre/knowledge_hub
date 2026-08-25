import React from 'react';

const CourseBuilder = () => {
    return (
        <>
            <div className="bd-course-builder d-flex flex-wrap align-items-center gap-15">
                <button type="button" className="bd-btn btn-outline-border-primary"><span
                    className="left-icon"><i className="fa-sharp fa-solid fa-square-plus"></i></span>
                    Add New Topic
                </button>
                <button type="button" className="bd-btn btn-outline-primary"><span className="left-icon"><i
                    className="fa-sharp fa-solid fa-square-plus"></i></span> Lesson
                </button>
            </div>
        </>
    );
};

export default CourseBuilder;