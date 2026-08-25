import React from 'react';
import Breadcrumbs from '../../../common/Breadcrumb/Breadcrumbs';
import AboutWhyChooseUs from './AboutWhyChooseUs';
import CourseAboutArea from '@/components/common/course-section/CourseAboutArea';
import AboutMissionVisionArea from './AboutMissionVisionArea';
import TestimonialSliderOne from '@/components/sliders/testimonial/TestimonialSliderOne';
import AboutCtaArea from '../../../common/about-cta/AboutCtaArea';
import AboutOnlineInstructor from './AboutOnlineInstructor';
import AbourCourseCounterArea from './AbourCourseCounterArea';

const AboutOnlineCourseMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='About Online Course' />
            <AboutWhyChooseUs />
            <CourseAboutArea />
            <AboutMissionVisionArea />
            <AbourCourseCounterArea />
            <TestimonialSliderOne />
            <AboutOnlineInstructor />
            <AboutCtaArea />
        </>
    );
};

export default AboutOnlineCourseMain;