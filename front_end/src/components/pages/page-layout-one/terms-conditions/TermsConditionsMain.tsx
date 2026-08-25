import React from 'react';
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import TermsConditionArea from './TermsConditionArea';

const TermsConditionsMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Terms and Conditions'/>
            <TermsConditionArea />
        </>
    );
};

export default TermsConditionsMain;