import React from 'react';
import UniversityBannerSection from './UniversityBannerSection';
import UniversityFeatureSection from './UniversityFeatureSection';
import UniversityCounterSection from './UniversityCounterSection';
import UniversityProgrammeArea from './UniversityProgrammeArea';
import UniversityBrandSection from './UniversityBrandSection';
import UniversityTestimonialArea from '../sliders/testimonial/UniversityTestimonialArea';
import UniversityVideoSection from './UniversityVideoSection';
import UniversityBlogSection from './UniversityBlogSection';
import UniversityAboutSectionCommon from '../common/University-section/UniversityAboutSectionCommon';
import UniversityWhyChooseAreaCommon from '../common/University-section/UniversityWhyChooseAreaCommon';
import UniversityEventSectionCommon from '../common/University-section/UniversityEventSectionCommon';
import UniversityCampusAreaCommon from '../common/University-section/UniversityCampusAreaCommon';
import UniversityCtaSectionCommon from '../common/University-section/UniversityCtaSectionCommon';

const UniversityMain = () => {
    return (
        <>
            <UniversityBannerSection />
            <UniversityFeatureSection />
            <UniversityAboutSectionCommon />
            <UniversityCounterSection />
            <UniversityProgrammeArea />
            <UniversityWhyChooseAreaCommon />
            <section className="bd-event-area section-space p-relative">
                <UniversityEventSectionCommon />
            </section>
            <UniversityBrandSection />
            <UniversityCampusAreaCommon />
            <UniversityTestimonialArea />
            <UniversityVideoSection />
            <UniversityBlogSection />
            <UniversityCtaSectionCommon />
        </>
    );
};

export default UniversityMain;