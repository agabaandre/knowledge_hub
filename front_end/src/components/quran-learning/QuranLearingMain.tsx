
import React from 'react';
import BannerArea from './BannerArea';
import QuranLearningCategoriesSlider from '../sliders/category/QuranLearningCategories';
import QuranLearingAboutArea from './QuranLearingAboutArea';
import QuranLearningCourse from './QuranLearningCourse';
import QuranChooseArea from './QuranChooseArea';
import QuranLarningInstructor from './QuranLarningInstructor';
import QuranLearningFaq from './QuranLearningFaq';
import QuranLearningSkill from './QuranLearningSkill';
import QuranLearningCounter from './QuranLearningCounter';
import PayerTimeArea from './PayerTimeArea';
import QuranLearningBlogArea from './QuranLearningBlogArea';

const QuranLearingMain = () => {

    return (
        <>
            <BannerArea />
            <QuranLearningCategoriesSlider/>
            <QuranLearingAboutArea/>
            <QuranLearningCourse/>
            <QuranChooseArea/>
            <QuranLarningInstructor/>
            <QuranLearningFaq/>
            <QuranLearningSkill/>
            <QuranLearningCounter/>
            <PayerTimeArea/>
            <QuranLearningBlogArea/>
        </>
    );
};

export default QuranLearingMain;
