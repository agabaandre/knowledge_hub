import BreadcrumbFour from '@/components/common/Breadcrumb/BreadcrumbFour';
import React from 'react';
import FacultyMembersArea from './FacultyMembersArea';
import UniversityCtaSectionCommon from '@/components/common/University-section/UniversityCtaSectionCommon';

const FacultyMembersMain = () => {
    return (
        <>
            <BreadcrumbFour title='Department of Civil and Environmental Engineering' subTitle='Faculty Members' />
            <FacultyMembersArea />
            <UniversityCtaSectionCommon />
        </>
    );
};

export default FacultyMembersMain;