"use client"
import NiceSelect from '@/components/elements/nice-select/NiceSelect';
import { courseLavel } from '@/data/dropdown-data';
import React from 'react';

const CourseSettings = () => {
    const selectHandler = () => { }
    return (
        <>
            <div className="tab-style-three tab-column">
                <ul className="nav nav-tabs" id="myTabOne" role="tablist">
                    <li className="nav-item" role="presentation">
                        <button className="nav-link active" id="general-tabOne" data-bs-toggle="tab" data-bs-target="#general-tabOne-pane" type="button" role="tab" aria-controls="general-tabOne-pane" aria-selected="true"><i
                            className="fa-solid fa-gear"></i> General</button>
                    </li>
                    <li className="nav-item" role="presentation">
                        <button className="nav-link" id="contentDrip-tabOne" data-bs-toggle="tab" data-bs-target="#contentDrip-tabOne-pane" type="button" role="tab" aria-controls="contentDrip-tabOne-pane" aria-selected="false"><i
                            className="fa-regular fa-clock"></i> Content Drip</button>
                    </li>
                </ul>
                <div className="tab-content" id="myTabOneContent">
                    <div className="tab-pane show active" id="general-tabOne-pane" role="tabpanel" aria-labelledby="general-tabOne" tabIndex={0}>
                        <div className="bd-new-course-general">
                            <div className="bd-new-course-general-item">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="maximumStudents">Maximum students</label>
                                    </div>
                                    <div className="form-input">
                                        <input name="maximumStudents" id="maximumStudents" type="number" placeholder="Course Title" />
                                        <span className="d-block fs-14 mt-10 bd-text-muted"><i
                                            className="fa-light fa-circle-exclamation"></i> Number of
                                            students that can enrol in this course. Set 0 for no
                                            limits.</span>
                                    </div>
                                </div>
                            </div>
                            <div className="bd-new-course-general-item">
                                <div className="form-input-box align-items-center">
                                    <div className="form-input-title">
                                        <label className="mb-0" htmlFor="courseTitle">Difficulty Level</label>
                                    </div>
                                    <div className="bd-new-course-input-level">
                                        <NiceSelect
                                            options={courseLavel}
                                            defaultCurrent={0}
                                            onChange={selectHandler}
                                            filterIcon={false}
                                            name=""
                                            className="course-level"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div className="bd-new-course-general-item">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="enrollmentExpiration">Enrollment Expiration</label>
                                    </div>
                                    <div className="form-input">
                                        <input name="enrollmentExpiration" id="enrollmentExpiration" type="number" placeholder="Course Title" />
                                        <span className="d-block fs-14 mt-10 bd-text-muted"><i
                                            className="fa-light fa-circle-exclamation"></i> {`Student's`}
                                            enrollment will be removed after this number of days. Set 0
                                            for lifetime enrollment</span>
                                    </div>
                                </div>
                            </div>
                            <div className="bd-new-course-general-item">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label>Public Course</label>
                                    </div>
                                    <div className="form-input">
                                        <div className="bd-radio-switcher">
                                            <label htmlFor="publicCourse">
                                                <input type="checkbox" id="publicCourse" />
                                                <span></span>
                                            </label>
                                        </div>
                                        <span className="d-block fs-14 mt-10 bd-text-muted"><i
                                            className="fa-light fa-circle-exclamation"></i> Make This
                                            Course Public. No enrollment required</span>
                                    </div>
                                </div>
                            </div>
                            <div className="bd-new-course-general-item">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="enrollmentExpiration">Q&A</label>
                                    </div>
                                    <div className="form-input">
                                        <div className="bd-radio-switcher">
                                            <label htmlFor="questionAnswer">
                                                <input type="checkbox" id="questionAnswer" />
                                                <span></span>
                                            </label>
                                        </div>
                                        <span className="d-block fs-14 mt-10 bd-text-muted"><i
                                            className="fa-light fa-circle-exclamation"></i> Enable Q&A
                                            section for your course</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="tab-pane" id="contentDrip-tabOne-pane" role="tabpanel" aria-labelledby="contentDrip-tabOne" tabIndex={0}>
                        <div className="bd-new-course-drip-wrap">
                            <div className="bd-new-course-general-item">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                    </div>
                                    <div className="form-input">
                                        <div className="checkbox-option">
                                            <input id="content-drip" type="checkbox" />
                                            <label htmlFor="content-drip">Enable</label>
                                        </div>
                                        <span className="d-block fs-14 mt-10 bd-text-muted">Enable / Disable content drip</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default CourseSettings;