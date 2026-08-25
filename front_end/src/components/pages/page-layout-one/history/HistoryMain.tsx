import BreadcrumbsTwo from '@/components/common/Breadcrumb/BreadcrumbsTwo';
import React from 'react';
import HistoryArea from './HistoryArea';
import HistoryCenturies from './HistoryCenturies';
import UniversityCtaSectionCommon from '@/components/common/University-section/UniversityCtaSectionCommon';

const HistoryMain = () => {
    return (
        <div>
            <BreadcrumbsTwo breadcrumbTwoTitle='History'/>
            <HistoryArea />
            <HistoryCenturies />
            <UniversityCtaSectionCommon />
        </div>
    );
};

export default HistoryMain;