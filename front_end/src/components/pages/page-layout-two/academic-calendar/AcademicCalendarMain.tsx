import BreadcrumbsTwo from '@/components/common/Breadcrumb/BreadcrumbsTwo';
import UniversityCtaSectionCommon from '@/components/common/University-section/UniversityCtaSectionCommon';
import React from 'react';
import AcademicCalenderArea from './AcademicCalenderArea';

const AcademicCalendarMain = () => {
    return (
        <>
         <BreadcrumbsTwo breadcrumbTwoTitle='Academic Calendar'/>   
         <AcademicCalenderArea />
         <UniversityCtaSectionCommon />
        </>
    );
};

export default AcademicCalendarMain;