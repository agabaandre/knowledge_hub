import BreadcrumbsTwo from '@/components/common/Breadcrumb/BreadcrumbsTwo';
import UniversityAboutSectionCommon from '@/components/common/University-section/UniversityAboutSectionCommon';
import React from 'react';
import AboutUniversityCampusHistoryArea from './AboutUniversityCampusHistoryArea';
import AboutUniversityCounterArea from './AboutUniversityCounterArea';
import UniversityEventSectionCommon from '@/components/common/University-section/UniversityEventSectionCommon';
import UniversityWhyChooseAreaCommon from '@/components/common/University-section/UniversityWhyChooseAreaCommon';
import UniversityCampusAreaCommon from '@/components/common/University-section/UniversityCampusAreaCommon';
import UniversityCtaSectionCommon from '@/components/common/University-section/UniversityCtaSectionCommon';
import AboutUniversityCampusGalleryArea from './AboutUniversityCampusGalleryArea';

const AboutUniversityMain = () => {
    return (
        <>
            <BreadcrumbsTwo breadcrumbTwoTitle='About iStudy University' />
            <UniversityAboutSectionCommon />
            <AboutUniversityCampusHistoryArea />
            <AboutUniversityCounterArea />
            <UniversityWhyChooseAreaCommon />
            <UniversityCampusAreaCommon />
            <section className="bd-event-area section-space-bottom p-relative">
                <UniversityEventSectionCommon />
            </section>
            <AboutUniversityCampusGalleryArea />
            <UniversityCtaSectionCommon />
        </>
    );
};

export default AboutUniversityMain;