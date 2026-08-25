import Image from 'next/image';
import React from 'react';
import avatarImg from '../../../../public/assets/images/avatar/avatar.webp';

const CourseVideoInstructors = () => {
    return (
        <>
            <div className="bd-course-video-instructors">
                <div className="bd-badge badge-primary"><Image src={avatarImg} alt="images" />istudy Badge</div>
            </div>
        </>
    );
};

export default CourseVideoInstructors;