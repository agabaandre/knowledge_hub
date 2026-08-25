import BreadcrumbsTwo from '@/components/common/Breadcrumb/BreadcrumbsTwo';
import React from 'react';
import CampusAboutArea from './CampusAboutArea';
import CampusIntroArea from './CampusIntroArea';
import CampusInfustructureArea from './CampusInfustructureArea';
import CampusVirtualTourArea from './CampusVirtualTourArea';
import CampusLocationArea from './CampusLocationArea';
import CampusGalleryArea from './CampusGalleryArea';
import UniversityCtaSectionCommon from '@/components/common/University-section/UniversityCtaSectionCommon';

const CampusMain = () => {
    return (
        <>
            <BreadcrumbsTwo breadcrumbTwoTitle='Campus'/>
            <CampusAboutArea />
            <CampusIntroArea />
            <CampusInfustructureArea />
            <CampusVirtualTourArea />
            <CampusLocationArea />
            <CampusGalleryArea />
            <UniversityCtaSectionCommon />
        </>
    );
};

export default CampusMain;