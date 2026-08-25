import React from 'react';

const CourseAttachment = () => {
    return (
        <>
            <div className="bd-course-attachments-input label-wrapper-class">
                <input id="new-course-file" type="file" />
                <label htmlFor="new-course-file"><span><i className="fa-regular fa-paperclip"></i></span>
                    Upload Attachments</label>
            </div>
        </>
    );
};

export default CourseAttachment;