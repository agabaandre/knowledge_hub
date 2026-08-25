import React from 'react';
import AboutBreadcrumb from './AboutBreadcrumb';
import PromotionArea from './PromotionArea';
import AboutFeatureArea from './AboutFeatureArea';
import NurturingMindsSection from './NurturingMindsSection';
import InstructorArea from '@/components/kindergarten/InstructorArea';
import LiveClassArea from '@/components/kindergarten/LiveClassArea';
import TestimonialArea from '@/components/kindergarten/TestimonialArea';

const AboutKindergartenMain = () => {
    return (
        <>
             <AboutBreadcrumb/>
             <PromotionArea/>
             <AboutFeatureArea/>
             <NurturingMindsSection/>
             <InstructorArea/>
             <LiveClassArea/>
             <TestimonialArea/>
        </>
    );
};

export default AboutKindergartenMain;