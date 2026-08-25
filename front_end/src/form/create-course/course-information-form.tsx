import React from 'react';

const CourseInformationForm = () => {
    return (
        <>
            <form>
                <div className="form-input-box mb-20">
                    <div className="form-input-title">
                        <label htmlFor="courseTitle">Course Title</label>
                    </div>
                    <div className="form-input">
                        <input name="courseTitle" id="courseTitle" type="text" placeholder="Course Title" />
                    </div>
                </div>
                <div className="form-input-box mb-20">
                    <div className="form-input-title">
                        <label htmlFor="courseDescription">Course Description</label>
                    </div>
                    <div className="form-input">
                        <textarea id="courseDescription" placeholder="Course Description"></textarea>
                    </div>
                </div>
                <div className="form-input-box mb-20">
                    <div className="form-input-title">
                        <label htmlFor="courseExcerpt">Excerpt</label>
                    </div>
                    <div className="form-input">
                        <textarea id="courseExcerpt" placeholder="Excerpt"></textarea>
                    </div>
                </div>
                <div className="form-input-box">
                    <div className="form-input-title">
                        <label htmlFor="courseAuthor">Course Author</label>
                    </div>
                    <div className="form-input">
                        <input name="courseAuthor" id="courseAuthor" type="text" defaultValue="Topylo (admin)" />
                    </div>
                </div>
            </form>
        </>
    );
};

export default CourseInformationForm;