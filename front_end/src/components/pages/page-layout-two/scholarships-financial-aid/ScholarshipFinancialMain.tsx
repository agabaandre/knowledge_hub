import BreadcrumbsTwo from '@/components/common/Breadcrumb/BreadcrumbsTwo';
import React from 'react';
import ScholarshipFinancialArea from './ScholarshipFinancialArea';
import UniversityCtaSectionCommon from '@/components/common/University-section/UniversityCtaSectionCommon';

const ScholarshipFinancialMain = () => {
    return (
        <>
            <BreadcrumbsTwo breadcrumbTwoTitle='Scholarships & Financial Aid' />
            <ScholarshipFinancialArea />
            <UniversityCtaSectionCommon />
        </>
    );
};

export default ScholarshipFinancialMain;