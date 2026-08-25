import React from 'react';
import DemoBannerArea from './DemoBannerArea';
import DemoPresentationArea from './DemoPresentationArea';
import CoursePageDemo from './CoursePageDemoSection';
import DemoGridSection from './DemoGridSection';
import InnerPageShowcasesArea from './InnerPageShowcasesArea';
import FeatureArea from './FeatureArea';
import ElementsArea from './ElementsArea';
import DemoHeaderFooterArea from './DemoHeaderFooterArea';
import HomeFaqArea from './HomeFaqArea';
import ResponsiveArea from './ResponsiveArea';
import ReviewArea from './ReviewArea';
import DashboardDemoArea from './DashboardDemoArea';

const HomeMain = () => {
    return (
        <>
            <DemoBannerArea />
            <DemoPresentationArea />
            <CoursePageDemo />
            <DemoGridSection />
            <DashboardDemoArea/>
            <InnerPageShowcasesArea />
            <FeatureArea />
            <ElementsArea />
            <DemoHeaderFooterArea />
            <ReviewArea />
            <ResponsiveArea />
            <HomeFaqArea />
        </>
    );
};

export default HomeMain;