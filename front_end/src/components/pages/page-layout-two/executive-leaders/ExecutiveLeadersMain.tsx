import BreadcrumbFour from '@/components/common/Breadcrumb/BreadcrumbFour';
import React from 'react';
import ExecutiveLeadersArea from './ExecutiveLeadersArea';
import UniversityCtaSectionCommon from '@/components/common/University-section/UniversityCtaSectionCommon';

const ExecutiveLeadersMain = () => {
    return (
        <>
            <BreadcrumbFour title='Executive Leaders' subTitle='Executive Leaders' />
            <ExecutiveLeadersArea />
            <UniversityCtaSectionCommon />
        </>
    );
};

export default ExecutiveLeadersMain;