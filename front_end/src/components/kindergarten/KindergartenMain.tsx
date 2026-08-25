import React from 'react';
import BannerArea from './BannerArea';
import OfferingArea from './OfferingArea';
import AboutKindergarten from './AboutKindergarten';
import ProgramsSliderArea from '../sliders/ProgramsSliderArea';
import KindergratenFaqAArea from './KindergratenFaqAArea';
import EventArea from './EventArea';
import GalleryArea from './GalleryArea';
import InstructorArea from './InstructorArea';
import TestimonialArea from './TestimonialArea';
import LiveClassArea from './LiveClassArea';
import KindergartenBlogArea from './KindergartenBlogArea';

const KindergartenMain = () => {
    return (
        <>
            <BannerArea/>
            <OfferingArea/>
            <AboutKindergarten/>
            <ProgramsSliderArea/>
            <KindergratenFaqAArea/>
            <EventArea/>
            <GalleryArea/>
            <InstructorArea/>
            <TestimonialArea/>
            <LiveClassArea/>
            <KindergartenBlogArea/>
        </>
    );
};

export default KindergartenMain;