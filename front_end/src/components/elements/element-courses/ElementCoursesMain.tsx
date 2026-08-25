import ElementsBreadcrumb from '@/components/common/Breadcrumb/ElementsBreadcrumb';
import React from 'react';
import CourseStyleOne from './CourseStyleOne';
import CourseStyleTwo from './CourseStyleTwo';
import CourseStyleThree from './CourseStyleThree';
import CourseStyleFour from './CourseStyleFour';
import CourseStyleSix from './CourseStyleSix';
import CourseStyleSeven from './CourseStyleSeven';
import CourseStyleFive from './CourseStyleFive';
import CourseStyleEight from './CourseStyleEight';
import CourseStyleNine from './CourseStyleNine';
import CourseStyleTen from './CourseStyleTen';
import CourseStyleEleven from './CourseStyleEleven';
import CourseStyleTwelve from './CourseStyleTwelve';
import CourseStyleThirteen from './CourseStyleThirteen';
import CourseStyleFourteen from './CourseStyleFourteen';

const ElementCoursesMain = () => {
    return (
        <>
            <ElementsBreadcrumb title='Courses' subTitle='Courses style' />
            <CourseStyleOne />
            <CourseStyleTwo />
            <CourseStyleThree />
            <CourseStyleFour />
            <CourseStyleFive />
            <CourseStyleSix />
            <CourseStyleSeven />
            <CourseStyleEight />
            <CourseStyleNine />
            <CourseStyleTen />
            <CourseStyleEleven />
            <CourseStyleTwelve />
            <CourseStyleThirteen />
            <CourseStyleFourteen />
        </>
    );
};

export default ElementCoursesMain;