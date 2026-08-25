import BreadcrumbsTwo from '@/components/common/Breadcrumb/BreadcrumbsTwo';
import React from 'react';
import ScholarshipFinancialDetailsArea from './ScholarshipFinancialDetailsArea';
import UniversityCtaSectionCommon from '@/components/common/University-section/UniversityCtaSectionCommon';

const ScholarshipFinancialDetailsMain = () => {
    return (
        <>
            <BreadcrumbsTwo breadcrumbTwoTitle='Scholarships & Financial Aid Details' />
            <ScholarshipFinancialDetailsArea />
            <UniversityCtaSectionCommon />
        </>
    );
};

export default ScholarshipFinancialDetailsMain;