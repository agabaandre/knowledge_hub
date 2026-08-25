import React from 'react';
import LanguageAcademyBanner from './LanguageAcademyBanner';
import LanguageAcademyFeatureArea from './LanguageAcademyFeatureArea';
import LanguageAcademyAbout from './LanguageAcademyAbout';
import LearnersArea from './LearnersArea';
import LanguageAcademyCourses from './LanguageAcademyCourses';
import LanguageAcademyTestimonial from '../sliders/testimonial/LanguageAcademyTestimonial';
import TrustedPartners from './TrustedPartners';
import AcademyBlogArea from './AcademyBlogArea';

const LanguageAcademyMain = () => {
    return (
        <>
            <LanguageAcademyBanner/>
            <LanguageAcademyFeatureArea/>
            <LanguageAcademyAbout/>
            <LearnersArea/>
            <LanguageAcademyCourses/>
            <LanguageAcademyTestimonial/>
            <TrustedPartners/>
            <AcademyBlogArea/>
        </>
    );
};

export default LanguageAcademyMain;