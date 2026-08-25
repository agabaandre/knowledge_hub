import React from 'react';
import BannerSectionOne from './BannerSectionOne';
import CourseFeatureArea from './CourseFeatureArea';
import CourseCatagory from './CourseCatagory';
import OnlineCourseArea from './OnlineCourseArea';
import CoursePricingPlanArea from './CoursePricingPlanArea';
import TestimonialSliderOne from '../sliders/testimonial/TestimonialSliderOne';
import CoursePartnerArea from './CoursePartnerArea';
import CourseBlogArea from './CourseBlogArea';
import CourseCareerArea from './CourseCareerArea';
import CourseAboutArea from '../common/course-section/CourseAboutArea';
import CourseCounterArea from '../common/course-section/CourseCounterArea';
import OnlineCourseInstructor from './OnlineCourseInstructor';

const OnlineCourseMain = () => {
    return (
        <>
            <BannerSectionOne />
            <CourseFeatureArea />
            <CourseCatagory />
            <CourseAboutArea />
            <OnlineCourseArea />
            <CoursePricingPlanArea />
            <TestimonialSliderOne />
            <OnlineCourseInstructor />
            <CourseCounterArea />
            <CoursePartnerArea />
            <CourseBlogArea />
            <CourseCareerArea />
        </>
    );
};

export default OnlineCourseMain;