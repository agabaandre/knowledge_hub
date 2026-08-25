import React from 'react';
import QuranLearningBreadcrumb from './QuranLearningBreadcrumb';
import QuranLearningPromotionArea from './QuranLearningPromotionArea';
import QuranLearningStoryArea from './QuranLearningStoryArea';
import QuranChooseArea from '@/components/quran-learning/QuranChooseArea';
import QuranLarningInstructor from '@/components/quran-learning/QuranLarningInstructor';
import AboutCtaArea from '@/components/common/about-cta/AboutCtaArea';
import QuranLearningTestimonial from './QuranLearningTestimonial';

const AboutQuranLearningMain = () => {
    return (
        <>
            <QuranLearningBreadcrumb/>
            <QuranLearningPromotionArea/>
            <QuranLearningStoryArea/>
            <QuranChooseArea/>
            <QuranLarningInstructor/>
            <QuranLearningTestimonial/>
            <AboutCtaArea />
        </>
    );
};

export default AboutQuranLearningMain;