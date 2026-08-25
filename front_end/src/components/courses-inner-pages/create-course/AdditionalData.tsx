import React from 'react';

const AdditionalData = () => {
    return (
        <>
            <div className="form-input-box mb-20">
                <div className="form-input-title">
                    <label htmlFor="whatLern">What Will I Learn?</label>
                </div>
                <div className="form-input">
                    <textarea id="whatLern" placeholder="Write here the course benefits (One per line)"></textarea>
                </div>
            </div>
            <div className="form-input-box mb-20">
                <div className="form-input-title">
                    <label htmlFor="whatLern">Targeted Audience</label>
                </div>
                <div className="form-input">
                    <textarea id="whatLern" placeholder="Specify the target audience that will benefit the most from the course. (One line per target audience.)"></textarea>
                </div>
            </div>
            <div className="form-input-box mb-20">
                <div className="form-input-title">
                    <label htmlFor="courseDuration">Total Course Duration</label>
                </div>
                <div className="d-flex-items gap-30">
                    <div className="form-input">
                        <input name="courseDuration" id="courseDuration" type="number" />
                        <span className="d-block mt-5 bd-text-muted fs-14">Hour</span>
                    </div>
                    <div className="form-input">
                        <input name="courseDuration" id="courseDuration" type="number" />
                        <span className="d-block mt-5 bd-text-muted fs-14">Minute</span>
                    </div>
                </div>
            </div>
            <div className="form-input-box">
                <div className="form-input-title">
                    <label htmlFor="whatLern">Requirements/Instructions</label>
                </div>
                <div className="form-input">
                    <textarea id="whatLern" placeholder="Additional requirements or special instructions for the students (One per line)"></textarea>
                </div>
            </div>
        </>
    );
};

export default AdditionalData;