"use client"
import NiceSelect from '@/components/elements/nice-select/NiceSelect';
import { selectVideo } from '@/data/dropdown-data';
import React from 'react';

const CourseIntroVideo = () => {
    const selectHandler = () => { }
    return (
        <>
            <div className="bd-course-video-lessons">
                <div className="bd-course-video-lessons-option">
                    <NiceSelect
                        options={selectVideo}
                        defaultCurrent={0}
                        onChange={selectHandler}
                        filterIcon={false}
                        name=""
                        className="course-lessons mb-15"
                    />
                </div>
                <div className="bd-course-video-lessons-input-field">
                    <div className="videoFile mb-15">
                        <input name="videoFile" type="file" placeholder="HTML 5 (mp4)" />
                    </div>
                    <div className="externalUrl mb-15">
                        <input name="externalUrl" type="url" placeholder="External URL" />
                    </div>
                    <div className="youtube">
                        <input name="youtube" type="url" placeholder="youtube url" />
                    </div>
                </div>
            </div>
        </>
    );
};

export default CourseIntroVideo;