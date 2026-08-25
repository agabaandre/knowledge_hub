import React from 'react';
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import PolicyArea from './PolicyArea';

const PrivacyPolicyMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Privacy & Policy'/>
            <PolicyArea />
        </>
    );
};

export default PrivacyPolicyMain;