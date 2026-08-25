import BreadcrumbsTwo from '@/components/common/Breadcrumb/BreadcrumbsTwo';
import React from 'react';
import MissionVisionArea from './MissionVisionArea';
import UniversityEventSectionCommon from '@/components/common/University-section/UniversityEventSectionCommon';
import UniversityCtaSectionCommon from '@/components/common/University-section/UniversityCtaSectionCommon';

const MissionVisionMain = () => {
    return (
        <>
            <BreadcrumbsTwo breadcrumbTwoTitle='Vision, Mission & Strategy' />
            <MissionVisionArea />
            <section className="bd-event-area section-space-bottom p-relative">
                <UniversityEventSectionCommon />
            </section>
            <UniversityCtaSectionCommon />
        </>
    );
};

export default MissionVisionMain;