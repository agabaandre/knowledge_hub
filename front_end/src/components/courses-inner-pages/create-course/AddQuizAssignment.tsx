import React from 'react';

const AddQuizAssignment = () => {
    return (
        <>
            <div className="form-input-box mb-20">
                <div className="form-input-title">
                    <label htmlFor="title">Title</label>
                </div>
                <div className="form-input">
                    <input name="title" id="title" type="text" placeholder="Title" />
                </div>
            </div>
            <div className="form-input-box">
                <div className="form-input-title">
                    <label htmlFor="summary">Summary</label>
                </div>
                <div className="form-input">
                    <textarea id="summary" placeholder="Summary"></textarea>
                </div>
            </div>
        </>
    );
};

export default AddQuizAssignment;