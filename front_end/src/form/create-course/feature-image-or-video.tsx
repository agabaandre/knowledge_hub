"use client"
import React from 'react';
import selectThumb from '../../../public/assets/images/shape/select-thumb.webp';
import { selectVideo } from '@/data/dropdown-data';
import Image from 'next/image';
import NiceSelect from '@/components/elements/nice-select/NiceSelect';

const FeatureImageOrVideo = () => {
    const selectHandler = () => { }
    return (
        <div>
            <div className="bd-course-thumbnail mb-30">
                <div className="bd-course-thumbnail-label">
                    <input type="file" id="imageUpload" />
                    <label htmlFor="imageUpload">
                        <span className="label-title">Change Thumbnail</span>
                        <span className="bd-course-thumbnail-preview">
                            <span className="bd-course-thumbnail-preview-box" id="imagePreview">
                                <Image src={selectThumb} style={{ width: '100%', height: '100%' }} alt="image" />
                            </span>
                        </span>
                    </label>
                </div>
            </div>
            <div className="bd-course-video-lessons">
                <div className="">
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
        </div>
    );
};

export default FeatureImageOrVideo;